<?php
        namespace src\client\controller;

        use Carbon\Carbon;
        use src\client\middleware\Date;
        use src\client\middleware\SecureApi;
        use src\client\middleware\TransactionDailyCounterAndRestrictionChecker;
        use src\client\model\ActivityLog;
        use src\client\model\Authtoken;
        use src\client\model\ExtraUserInfo;
        use src\client\model\SalesPoint;
        use src\client\model\Settings;
        use src\client\model\Transaction;
        use src\client\model\User;
        use src\client\model\Wallet;
        use src\client\service\Bill;
        use src\client\service\MailService;
        use \zil\core\server\Param;
        use \zil\core\server\Response;
        use zil\factory\Utility;
        use \zil\core\facades\helpers\Notifier;
        use \zil\core\facades\helpers\Navigator;
        use \zil\core\facades\decorators\Hooks;

        use src\client\Config;
        use zil\security\Validation;

        /**
         *  @Controller:BillApiController []
        */

        class BillApiController{

            use Notifier, Navigator, Hooks;

            /**
             * Verify card no. before proceeding to service
             * @param Param $param
             */
            public  function VerifySmartCard(Param $param){

                try{

                    $Validation = new Validation( ['service_type_id', 'text|required'], ['service_type_option_product_id', 'text|required'], ['meter_no_or_smartcard', 'text|required'] );

                    if($Validation->isPassed()) {


                        $result = (new Bill())->GetSmartCardOrMeterNoDetails($param->form()->meter_no_or_smartcard, $param->form()->service_type_id, $param->form()->service_type_option_product_id );

                        $Pr = (new ActivityLog())->Log("[SMART CARD/METER NO VERIFICATION] {$param->form()->meter_no_or_smartcard} used", 'PASSED');
                        if(is_null($Pr))
                            throw new \Exception("An error occurred, process stream broken, please retry");



                        if(isset($result->name)) {
                            $data = ['msg' => $result, 'success' => true];
                            (new ActivityLog())->updateLog($Pr, "[SMART CARD/METER NO VERIFICATION] {$param->form()->meter_no_or_smartcard} verified", 'VERIFIED');
                        }else{
                            (new ActivityLog())->updateLog($Pr, "[SMART CARD/METER NO VERIFICATION] {$param->form()->meter_no_or_smartcard} invalid", 'NOT VERIFIED');
                            throw new \Exception("Smart/Meter Number is invalid");
                        }

                    } else {
                        throw new \Exception($Validation->getErrorString());
                    }
                } catch (\Throwable $t){
                    $data = [ 'msg' => $t->getMessage(), 'success' => false ];
                } finally {
                    echo Response::fromApi($data, 200);
                }

            }


            /**
             * Pay Bills
             */

            public function PayElectricityTopUp(Param $param){
                try{

                    $id = (new ExtraUserInfo())->getUserId();
                    $email = (new ExtraUserInfo())->filter('email')->where( ['id', $id] )->get()->email;

                    if( (new Transaction())->isLocked($email) ){
                        throw new \Exception("You are not eligible to make transactions at the moment, because of pending transaction\nResolve pending transaction first");
                    }

                    $Min = (new Settings())->getElectricityBillMinSale();
                    $Max = (new Settings())->getElectricityBillMaxSale();

                    $Validation = new Validation( ['mode', 'required'], ['meter_no', 'text|required'], ['product_id', 'text|required'], ['amount', "number|required|min:{$Min}|max:{$Max}"] );

                    if( $Validation->isPassed() ){

                        /**
                         * Run checksum
                         */
                        $checksum = null;
                        if(isset($param->form()->timestamp)){
                            $timestamp = $param->form()->timestamp;

                            $checksum = (new Authtoken())->getCheckSum($timestamp);

                            if ( (new Transaction())->isExists( ['checksum', $checksum] ) ){
                                $data = [ 'msg' =>  "Transaction already completed", 'success' => true ];
                                return;
                            }
                        }


                        // Logger::Init();
                        // 	Logger::ELog($param->form());
                        // Logger::kill();

                        $Pr = (new ActivityLog())->Log("[ELECTRICITY TOP UP] Validation Passed", 'SUCCESS');
                        if(is_null($Pr))
                            throw new \Exception("An error occurred, process stream broken, please retry");


                        $Wallet = new Wallet;
                        $Bill = new Bill;
                        $Transaction = new Transaction;

                        $message = null;
                        if( isset($param->form()->message) ){
                            $message = $param->form()->message;
                        }

                        $prepaid = false;
                        if( $param->form()->mode == true ){
                            $prepaid = true;
                        }

                        $product_id = $param->form()->product_id;

                        $ChargeAmt = ( (new Settings())->getElectricityBillChargeRate() / 100 ) * $param->form()->amount;
                        $amount = $param->form()->amount;

                        $rewardRate = 0.00;
                        $discountedAmount = ( (100 - $rewardRate) / 100 ) * $amount;


                        if($param->form()->has_product_list == 0){
                            $discountedAmount += $ChargeAmt;
                        }

                        if( !$Wallet->isSufficientBalance( floatval($discountedAmount) ) )
                            throw new \Exception("Insufficient fund in wallet");


                        (new ActivityLog())->updateLog($Pr, "[ELECTRICITY TOP UP] Wallet balance checkpoint Passed", 'SUCCESS');



                        $pk = $Wallet->getPublickey();
                        $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', sodium_bin2hex($pk) ] )->get();

                        $payment_meta_info = [
                            'type' => (new Transaction())->getTransactionTypes('ELECTRICITY_BILL_TRADE'),
                            'to_address' => $Wallet->getAnyAdminPublickey(),
                            'from_address'=>
                                [
                                    'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                    'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                                ]
                        ];


                        $ServiceTrans = (new Transaction())->addServiceTrans(
                            $payment_meta_info['type'],
                            'CONFIRMED',
                            [
                                $payment_meta_info['from_address']['pubk'],
                                $payment_meta_info['from_address']['prik']
                            ],
                            $payment_meta_info['to_address'],
                            $discountedAmount,
                            $message
                        );


                        if($ServiceTrans == true){

                            $TransLastInsert = $Transaction->lastInsert();
                            /**
                             * Save Checksum
                             */
                            $Transaction->checksum = $checksum;
                            $Transaction->where(['id', $TransLastInsert])->update();


                            $trade_key = (new Transaction())->filter('trans_hash')->where( ['id', $TransLastInsert] )->get()->trans_hash;

                            $tk = sodium_bin2hex($trade_key);
                            (new ActivityLog())->updateLog($Pr,"[ELECTRICITY TOP UP] Service Paid & Transaction Logged, \n Trade key: {$tk}", 'SUCCESS');



                            // Initiate Electricity Bill Trade Transaction
                            $result = $Bill->ElectricityTopUp($param->form()->meter_no, $amount, $product_id, $prepaid);

                            if( $result !== false && isset($result->status) && ($result->status == 201) ){

                                $result->paid_amount = $discountedAmount;

                                $message .= "<br>SubType: [ELECTRICITY]<br><br>
                                Meter No: {$param->form()->meter_no}<br><br>
                                Country: {$result->country}<br><br>
                                Details: {$result->message}<br><br>
                                Operator name: {$result->operator_name}<br><br>
                                Others: {$result->pin_option1}<br><br>
                                Reference: {$result->reference}<br><br>
                                Paid Amount: {$result->paid_amount}<br><br>
                                Topup Currency: {$result->topup_currency}<br><br>
                                Service Charge: NGN {$ChargeAmt}";

                                (new ActivityLog())->updateLog($Pr,"[ELECTRICITY TOP UP] Service Rendered, \n {$message}", 'SUCCESS');


                                // Add to Sales
                                $SalesPoint = new SalesPoint;

                                $SalesPoint->trade_key = $trade_key;
                                $SalesPoint->trade_type = $payment_meta_info['type'];
                                $SalesPoint->ifrom_address = sodium_bin2hex($pk);
                                $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                                $SalesPoint->valueorqtyexchanged = $discountedAmount;
                                $SalesPoint->extracharge = $ChargeAmt;
                                $SalesPoint->rawamt = $discountedAmount;
                                $SalesPoint->icurrency = 'NGN';
                                $SalesPoint->status = $SalesPoint->getTradeStatus('COMPLETED');
                                $SalesPoint->proofoftrade = isset($result->pin_code) ? $result->pin_code : 'N/A';
                                $SalesPoint->proofoftradeformat = 'PIN_CODE';
                                $SalesPoint->tradehistory = $message;
                                $SalesPoint->created_at = Carbon::now();
                                $SalesPoint->updated_at = Carbon::now();
                                $SalesPoint->checksum = $checksum;

                                if ( $SalesPoint->create() ){


                                    if($result->pin_based){

                                        $message .= "=========================<br>PIN<br><br>";

                                        $message .= "<b>PIN_CODE:</b>  ".$result->pin_code."<br>";
                                        // $message .= "<b>Extra(?):</b>  ".$result->pin_option1."<br>";
                                        $message .= "============================<br>";
                                    }

                                    (new MailService())->sendOrderReceipt($message, strtoupper($param->url()->service_id  ." > {$payment_meta_info['type']}(#".sodium_bin2hex($trade_key).")") );

                                    (new ActivityLog())->updateLog($Pr,"[ELECTRICITY TOP UP] SalesPoint Record Added, \n {$message}", 'SUCCESS');


                                    $data = [ 'msg' => 'Electricity bill successfully paid', 'receipt' => $result, 'success' => true ];



                                }else{
                                    throw new \Exception("Couldn't complete bill payment trade, please try again later");
                                }

                            }else{


                                    (new ActivityLog())->updateLog($Pr, "[ELECTRICITY TOP UP] Service Not Rendered, \n {$message}", 'FAIL');

                                throw new \Exception("Service rendering not completed, retry or contact us");

                            }



                        }else{
                            throw new \Exception("Unable to complete bill payment, try again later");
                        }


                    }else{
                        $data = [ 'msg' => $Validation->getErrorString(), 'success' => false ];
                    }

                }catch(\Throwable $t){

                    $data = [ 'msg' =>  $t->getMessage(), 'success' => false ];

                    if(!is_null($Pr))
                        (new ActivityLog())->Log("[ELECTRICITY TOP UP] {$data['msg']}", 'FAIL');


                }finally{
                    echo Response::fromApi($data, 200);
                }
            }

            public function PayNonElectricityBill(Param $param){


                try{

                    $id = (new ExtraUserInfo())->getUserId();
                    $email = (new ExtraUserInfo())->filter('email')->where( ['id', $id] )->get()->email;

                    if( (new Transaction())->isLocked($email) ){
                        throw new \Exception("You are not eligible to make transactions at the moment, because of pending transaction\nResolve pending transaction first");
                    }

                    $Validation = new Validation(
                        ['product_id', 'required'],
                        ['service_id', 'required'],
                        ['amount', 'number|required'],
                        ['smartcardno', 'text|required'],
                        ['product_option_code', 'required'],
                        ['has_product_list', 'text|number']
                    );

                    if( $Validation->isPassed() ){

                        /**
                         * Run checksum
                         */
                        $checksum = null;
                        if(isset($param->form()->timestamp)){
                            $timestamp = $param->form()->timestamp;

                            $checksum = (new Authtoken())->getCheckSum($timestamp);

                            if ( (new Transaction())->isExists( ['checksum', $checksum] ) ){
                                $data = [ 'msg' =>  "Transaction already completed", 'success' => true ];
                                return;
                            }
                        }

                        $Bill = new Bill;
                        $Wallet = new Wallet;
                        $Transaction = new Transaction;

                        $Pr = (new ActivityLog())->Log("[NON-ELECTRICITY TOP UP] Validation Passed", 'SUCCESS');

                        if(is_null($Pr))
                            throw new \Exception("An error occurred, process stream broken, please retry");

                        $message = "";
                        if( isset($param->form()->message) ){
                            $message = $param->form()->message;
                        }


                        $amount = $param->form()->amount;

                        $rewardRate = 0.0;

                        /**Handle different service with different charges */
                        if($param->url()->service_id == 'dstv'){
                            $rewardRate = (new User())->getUserTransactionReward()['DISCOUNT_RATE']['CABLE_TV'];
                            $ChargeAmt = ( (new Settings())->getCableTvBillChargeRate() / 100 ) * $param->form()->amount;
                            $tag = 'TV';
                        } else if($param->url()->service_id == 'internet'){
                            $ChargeAmt = ( (new Settings())->getInternetBillChargeRate() / 100 ) * $param->form()->amount;
                            $tag = 'INTERNET';
                        } else if($param->url()->service_id == 'misc'){
                            $ChargeAmt = ( (new Settings())->getMiscBillChargeRate() / 100 ) * $param->form()->amount;
                            $tag = 'MISC';
                        } else {
                            $ChargeAmt = 0.00;
                            $tag = 'OTHERS';
                        }

                        /**Particular About WAEC */
                        if($param->form()->product_id == 'BPM-NGCA-ASA'){
                            $data =  Utility::asset( 'data/data.json');
                            $data = json_decode(file_get_contents($data));
                            $ChargeAmt = $data->bill->service_charge->waec;
                        }

                        $discountedAmount = ( (100 - $rewardRate) / 100 ) * $amount;

                        if($param->form()->has_product_list == 0){
                            $discountedAmount += $ChargeAmt;
                        }



                        if( !$Wallet->isSufficientBalance( floatval($discountedAmount) ) )
                            throw new \Exception("Insufficient fund in wallet");


                        (new ActivityLog())->updateLog($Pr,"[NON-ELECTRICITY TOP UP] Wallet balance checkpoint Passed", 'SUCCESS');


                        $pk = $Wallet->getPublickey();
                        $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', sodium_bin2hex($pk) ] )->get();

                        $payment_meta_info = [
                            'type' => (new Transaction())->getTransactionTypes('BILL_TRADE'),
                            'to_address' => $Wallet->getAnyAdminPublickey(),
                            'from_address'=>
                                [
                                    'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                    'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                                ]
                        ];
                        $ServiceTrans = (new Transaction())->addServiceTrans(
                            $payment_meta_info['type'],
                            'CONFIRMED',
                            [
                                $payment_meta_info['from_address']['pubk'],
                                $payment_meta_info['from_address']['prik']
                            ],
                            $payment_meta_info['to_address'],
                            $discountedAmount,
                            $message
                        );



                        if($ServiceTrans == true){


                            $TransLastInsert = $Transaction->lastInsert();
                            /**
                             * Save Checksum
                             */
                            $Transaction->checksum = $checksum;
                            $Transaction->where(['id', $TransLastInsert])->update();



                            $trade_key = (new Transaction())->filter('trans_hash')->where( ['id', $TransLastInsert] )->get()->trans_hash;

                            (new ActivityLog())->updateLog($Pr,"[NON-ELECTRICITY TOP UP] Service Paid & Transaction Logged, \n Trade key: {$trade_key}", 'SUCCESS');


                            // Initiate TV/INTERNET/MISC Bill Trade Transaction
                            $code = $param->form()->product_option_code;
                            $result = $Bill->NonElectricityTopUp($param->form()->smartcardno, $param->url()->service_id, $param->url()->product_id, $code);

                            if($result !== false && isset($result->status) && ($result->status == 201)){

                                $result->paid_amount = $discountedAmount;

                                $message .= "<br>SubType:[{$tag}]<br><br>
                                Smart card: {$param->form()->smartcardno}<br><br>
                                Country: {$result->country}<br><br>
                                Details: {$result->message}<br><br>
                                Operator name: {$result->operator_name}<br><br>
                                Reference: {$result->reference}<br><br>
                                Amount: {$result->paid_amount}<br><br>
                                Topup Currency: {$result->topup_currency}<br><br>";


                                (new ActivityLog())->updateLog($Pr,"[NON-ELECTRICITY TOP UP] Service Rendered, \n {$message}", 'SUCCESS');



                                // Add to Sales
                                $SalesPoint = new SalesPoint;

                                $SalesPoint->trade_key = $trade_key;
                                $SalesPoint->trade_type = $payment_meta_info['type'];
                                $SalesPoint->ifrom_address = sodium_bin2hex($pk);
                                $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                                $SalesPoint->valueorqtyexchanged = $discountedAmount;
                                $SalesPoint->extracharge = $ChargeAmt;
                                $SalesPoint->rawamt = $discountedAmount;
                                $SalesPoint->icurrency = 'NGN';
                                $SalesPoint->status = $SalesPoint->getTradeStatus('COMPLETED');
                                $SalesPoint->proofoftrade = $result->reference;
                                $SalesPoint->proofoftradeformat = 'REFERENCE';
                                $SalesPoint->tradehistory = $message;
                                $SalesPoint->created_at = Carbon::now();
                                $SalesPoint->updated_at = Carbon::now();
                                $SalesPoint->checksum = $checksum;

                                if ( $SalesPoint->create() ){

                                    $tk = sodium_bin2hex($trade_key);

                                    (new ActivityLog())->updateLog($Pr, "[NON-ELECTRICITY TOP UP] Sale Recorded {$tk}", 'SUCCESS');


                                    if($result->pin_based){

                                        $message .= "<br>=========================<br>PIN<br><br>";
                                        foreach($result->pins as $upin){

                                            if(isset($upin->numberOfSms))
                                                $message .= "<b>No. of SMS(?):</b>  ".$upin->numberOfSms."<br><br>";

                                            $message .= "<b>PIN:</b>  ".$upin->pin."<br>";
                                            $message .= "<b>SERIAL(?):</b>  ".$upin->serialNumber."<br>";
                                            $message .= "<b>Expires on(?):</b>  ".$upin->expiresOn."<br>";
                                            $message .= "-----------------------------<br><br>";
                                        }
                                        $message .= "============================<br>";
                                    }

                                    (new MailService())->sendOrderReceipt($message, strtoupper($param->url()->service_id  ." > {$payment_meta_info['type']} (#".sodium_bin2hex($trade_key).")") );

                                    $data = [ 'msg' => ucwords($param->url()->service_id).' subscription completed', 'receipt'=>$result, 'success' => true ];
                                }else{
                                    throw new \Exception("Couldn't complete bill trade, please try again later");
                                }

                            }else{
                                (new ActivityLog())->updateLog($Pr,"[NON-ELECTRICITY TOP UP] Service Not Rendered, \n {$message}", 'FAIL');
                                throw new \Exception("Service rendering not completed, retry or contact us");
                            }

                        }else{
                            throw new \Exception("Unable to complete bill payment, try again later");
                        }


                    }else{
                        $data = [ 'msg' => $Validation->getErrorString(), 'success' => false ];
                    }

                }catch(\Throwable $t){

                    $data = [ 'msg' => $t->getMessage(), 'success' => false ];

                    if(!is_null($Pr))
                        (new ActivityLog())->Log("[ELECTRICITY TOP UP] {$data['msg']}", 'FAIL');

                }finally{

                    echo Response::fromApi($data, 200);
                }
            }



            public function __construct(){

                header('Access-Control-Allow-Origin: *');
//            header('Content-Type: application/json');
                header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
                header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

            }

            public function onInit(Param $param){
                new Date($param);
            }

            public function onAuth(Param $param){
                new SecureApi($param);
                new TransactionDailyCounterAndRestrictionChecker($param);
            }

            public function onDispose(Param $param){}

        }
