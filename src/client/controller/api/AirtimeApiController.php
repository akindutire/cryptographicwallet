<?php
        namespace src\client\controller;

        use Carbon\Carbon;
        use src\client\middleware\Date;
        use src\client\middleware\SecureApi;
        use src\client\middleware\TransactionDailyCounterAndRestrictionChecker;
        use src\client\model\ActivityLog;
        use src\client\model\AirtimeEPins;
        use src\client\model\Authtoken;
        use src\client\model\ExtraUserInfo;
        use src\client\model\Product;
        use src\client\model\Product_cat;
        use src\client\model\SalesPoint;
        use src\client\model\Settings;
        use src\client\model\Transaction;
        use src\client\model\User;
        use src\client\model\Wallet;
        use src\client\service\Bill;
        use src\client\service\MailService;
        use \zil\core\server\Param;
        use \zil\core\server\Response;
        use \zil\factory\View;
        use \zil\core\facades\helpers\Notifier;
        use \zil\core\facades\helpers\Navigator;
        use \zil\core\facades\decorators\Hooks;

        use src\client\Config;
        use zil\security\Encryption;
        use zil\security\Sanitize;
        use zil\security\Validation;

        /**
         *  @Controller:AirtimeApiController []
        */

        class AirtimeApiController{

            use Notifier, Navigator, Hooks;

			public function BuyAirtimeEPin(Param $param){
				try{
                    $V = new Validation(
                        ['network_provider', 'required'],
                        ['data_products', 'required'],
                        ['units', 'required|min:1']
                    )  ;



                    if($V->isPassed()){
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

                        $Pr = (new ActivityLog())->Log("[AIRTIME E-PIN] Validation Passed", 'SUCCESS');
                        if(is_null($Pr))
                            throw new \Exception("An error occurred, process stream broken, please retry");


                        list($CatId, $networkProvider) = explode('+', $param->form('network_provider') );
                        $product = $param->form('data_products');
                        $units = $param->form('units');

                        $AirtimeEPins = new AirtimeEPins();

                        if($AirtimeEPins->as('aep')->with('Product as p', "aep.product = p.pname")->filter('p.pcost')->where(  ['p.pcat', $CatId] ,['aep.product', $product], ['aep.network_provider', $networkProvider], ['aep.status_code', 0]  )->count() == 0) {
                            $M = new MailService();

                            $mailAccounts = $M->mailAccounts();
                            $to = $mailAccounts['NAIJASUB2'];
                            $date = date('D-M-Y, H:i', time());
                            $message = "{$networkProvider},{$product} Airtime E-PIN Exhausted, Goto admin and reload Asap";
                            (new MailService())->sendMail($to, "{$date}: {$networkProvider},{$product} Airtime E-PIN Exhausted", "$message");

                            throw new \Exception("Pin not available");
                        }else {
                            $ProductPriceFromSource = $AirtimeEPins->as('aep')->with('Product as p', "aep.product = p.pname")->filter('p.pdiscount', 'p.pcost')->where(['aep.product', $product], ['aep.network_provider', $networkProvider], ['p.pcat', $CatId], ['aep.status_code', 0])->take(1)->get();
                        }
                        $unitPrice = $ProductPriceFromSource->pcost - $ProductPriceFromSource->pdiscount;
                        $amount = $unitPrice * $units;

                        $W = new Wallet();
                        $pk = $W->getPublickey();

                        $balance = $W->getBalance(sodium_bin2hex($W->getPublickey()));

                        if($balance < $amount)
                            throw new \Exception("Insufficient balance, Cost: NGN {$amount}");

                        $T = new Transaction();

                        /**
                         * Render service
                         */

                        $PinState = (new AirtimeEPins())->buy($units, $networkProvider, $product, $unitPrice);

                        $unit_sold = $PinState['unit_sold'];
                        $PinSold = $PinState['pin_sold'];
                        $PinString = $PinState['pin_string'];

                        /**
                         * Successful sale
                         */
                        $amount = $unit_sold * $unitPrice;


                        $message = "{$unit_sold} {$networkProvider} Airtime E-Pin<br>{$PinString}";
                        /**
                         * Log Activity
                         */
                        (new ActivityLog())->updateLog($Pr,"[AIRTIME E-PIN] Service Rendered \n {$message}", 'SUCCESS');


                        $from_prime_pk = $W->filter('public_key', 'private_key')->where( ['public_key', sodium_bin2hex($pk) ] )->get();

                        $payment_meta_info = [
                            'type' => $T->getTransactionTypes('AIRTIME_E_PIN_TRADE'),
                            'to_address' => $W->getAnyAdminPublickey(),
                            'from_address'=>
                                [
                                    'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                    'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                                ]
                        ];

                        sleep(1);

                        $ServiceTrans = $T->addServiceTrans(
                            $payment_meta_info['type'],
                            'CONFIRMED',
                            [
                                $payment_meta_info['from_address']['pubk'],
                                $payment_meta_info['from_address']['prik']
                            ],
                            $payment_meta_info['to_address'],
                            $amount,
                            $message
                        );

                        if($ServiceTrans == true) {


                            $TransLastInsert = $T->lastInsert();
                            /**
                             * Save Checksum
                             */
                            $T->checksum = $checksum;
                            $T->where(['id', $TransLastInsert])->update();


                            $trade_key = (new Transaction())->filter('trans_hash')->where(['id', $TransLastInsert])->get()->trans_hash;

                            (new ActivityLog())->updateLog($Pr, "[AIRTIME E-PIN] Service Paid & Transaction Logged, \n Trade key: {$trade_key}", 'SUCCESS');

                            sleep(2);

                            // Add to Sales
                            $SalesPoint = new SalesPoint();
                            $SalesPoint->trade_key = $trade_key;
                            $SalesPoint->trade_type = $payment_meta_info['type'];
                            $SalesPoint->ifrom_address = sodium_bin2hex($pk);
                            $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                            $SalesPoint->valueorqtyexchanged = $amount;
                            $SalesPoint->extracharge = 0.00;
                            $SalesPoint->rawamt = $amount;
                            $SalesPoint->icurrency = 'ATEP';
                            $SalesPoint->status = $SalesPoint->getTradeStatus('COMPLETED');
                            $SalesPoint->proofoftrade = (new Encryption())->generateShortHash();
                            $SalesPoint->proofoftradeformat = 'CRYPTOGRAPHIC_KEY';
                            $SalesPoint->tradehistory = $message;
                            $SalesPoint->created_at = Carbon::now();
                            $SalesPoint->updated_at = Carbon::now();
                            $SalesPoint->checksum = $checksum;

                            if ($SalesPoint->create()) {

                                (new ActivityLog())->updateLog($Pr, "[AIRTIME E-PIN] Sale Recorded {$trade_key}", 'SUCCESS');
                                $data = [
                                    'msg' => "{$networkProvider} Airtime E-Pin Trade Successful",
                                    'pins' => $PinSold,
                                    'success' => true
                                ];
                            } else {
                                throw new \Exception("Transaction successful, sales not recorded");
                            }
                        }else{
                            throw new \Exception("Transaction not successful, please retry");
                        }
                    } else{
                        throw new \Exception($V->getErrorString());
                    }
                } catch(\Throwable $t){
				    $data = [ 'msg' => $t->getMessage(), 'success' => false ];
                }finally{
				    echo Response::fromApi($data, 200);
                }
			}

            public function CalculateAirtimeCardUnitPrice(Param $param){
                try{
                    $V = new Validation(
                        ['network_provider', 'required'],
                        ['data_products', 'required'],
                        ['units', 'required|min:1']
                    )  ;

                    if($V->isPassed()){

                        list($networkProviderId, $networkProvider) = explode('+', $param->form('network_provider') );
                        $product = $param->form('data_products');
                        $units = $param->form('units');

                        $PC = new Product();


                        if($PC->filter('pcost', 'pdiscount')->where(  ['pname', $product], ['pcat', $networkProviderId]  )->count() == 0) {
                            $M = new MailService();

                            $mailAccounts = $M->mailAccounts();
                            $to = $mailAccounts['NAIJASUB2'];
                            $date = date('D-M-Y, H:i', time());
                            $message = "{$networkProvider},{$product} Airtime E-PIN Exhausted, Goto admin and reload Asap";
                            (new MailService())->sendMail($to, "{$date}: {$networkProvider},{$product} Airtime E-PIN Exhausted", "$message");
                            throw new \Exception("Pins not available");
                        }else{
                                $unitPriceObj = $PC->filter('pcost', 'pdiscount')->where(['pname', $product], ['pcat', $networkProviderId])->take(1)->get();
                            }
                        $amount = ($unitPriceObj->pcost - $unitPriceObj->pdiscount) * $units;

                        $data = [ 'msg' => $amount, 'success'=> true ];

                    }else{
                        throw new \Exception($V->getErrorString());
                    }
                }
                catch(\Throwable $t){
                    $data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
                }finally{
                    echo Response::fromApi($data, 200);
                }

            }

            /**
             * @param Param $param
             */
            public function BuyAirtime(Param $param){
                try{


                    $Transaction = new Transaction;
                    $Wallet = new Wallet;
                    $Bill = new Bill;
                    $SalesPoint = new SalesPoint;


                    $id = (new ExtraUserInfo())->getUserId();
                    $email = (new ExtraUserInfo())->filter('email')->where( ['id', $id] )->get()->email;


                    if( $Transaction->isLocked($email) ){
                        throw new \Exception("You are not eligible to make transactions at the moment, because of pending transaction\nResolve pending transaction first");
                    }

                    $Validation = new Validation( ['amount', 'number|min:50'], ['phone', 'text|minlength:11|required'], ['network_provider', 'text|required'] );

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

                        $ProcessStr = (new ActivityLog())->Log("[AIRTIME] Validation Passed", 'SUCCESS');
                        if (is_null($ProcessStr))
                            throw new \Exception("An error occurred, process stream broken, please retry");


                        $network_provider = $param->form()->network_provider;
                        if($param->form()->network_provider == 'N9MOBILE')
                            $network_provider = "9MOBILE";

                        $providers = [];
                        foreach ((new Settings())->getNetworkProviders() as $provider){
                            array_push($providers, $provider->value);
                        }

                        if( !in_array($network_provider,  $providers) )
                            throw new \Exception("Network Provider not recognized from this domain");


                        $phone = '234'.ltrim($param->form()->phone, '0');


                        $result = $Bill->MSISDNInfo($phone, 'AIRTIME');


                        if( isset($result->opts->msisdn) && ($result->opts->msisdn != $phone)){
                            throw new \Exception("Please verify that the phone no. {$phone} is valid");
                        }

                        (new ActivityLog())->updateLog($ProcessStr, "[AIRTIME] Phone Verified", 'SUCCESS');


                        // if( isset($result->opts->operator) && preg_match("/$network_provider/", $result->opts->operator) > 0 )
                        // 	throw new \Exception("The phone no. {$phone} is not found on {$network_provider} numbers");


                        $amount = $param->form()->amount;

                        // Reward rate according to account type
                        $rewardRateThroughSpecifiedAccountType = (new User())->getUserAirtimePurchaseReward();

                        // Reward rate according network provider
                        $rewardRateThroughNetworkProvider = (new Settings())->getAirtimePurchaseDiscountRate($network_provider);
                        $discountedAmount = ( (100 - ( $rewardRateThroughSpecifiedAccountType + $rewardRateThroughNetworkProvider) ) / 100 ) * $amount;

                        $extra_charge = 0.00;

                        $message = '';

                        /**
                         * Balance Sufficiency
                         */

                        $amt_to_check = $discountedAmount + $extra_charge;

                        if( !$Wallet->isSufficientBalance( floatval($amt_to_check) ) )
                            throw new \Exception("Insufficient fund in wallet");


                        (new ActivityLog())->updateLog($ProcessStr,"[AIRTIME] Wallet balance checkpoint passed", 'SUCCESS');


                        $pk = $Wallet->getPublickey();
                        $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', sodium_bin2hex($pk) ] )->get();

                        $payment_meta_info = [
                            'type' => (new Transaction())->getTransactionTypes('AIRTIME_TRADE'),
                            'to_address' => $Wallet->getAnyAdminPublickey(),
                            'from_address'=>
                                [
                                    'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                    'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                                ]
                        ];
                        $ServiceTrans = $Transaction->addServiceTrans(
                            $payment_meta_info['type'],
                            'CONFIRMED',
                            [
                                $payment_meta_info['from_address']['pubk'],
                                $payment_meta_info['from_address']['prik']
                            ],
                            $payment_meta_info['to_address'],
                            $discountedAmount
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
                            (new ActivityLog())->updateLog($ProcessStr, "[AIRTIME] Transaction Logged & Service Paid, tradekey: {$tk}", 'SUCCESS');


                            // Initiate Airtime Trade Transaction
                            $result = $Bill->AirtimeAndDataTopUp($phone, $param->form()->amount, 'AIRTIME');

                            if($result !== false && isset($result->status) && $result->status == 201){

                                $result->paid_amount = $discountedAmount;

                                $message .= "
								<b>Airtime Purchase</b><br><br>
								Country: {$result->country}<br><br>
								Details: {$result->message}<br><br>
								Operator name: {$result->operator_name}<br><br>
								Phone: {$param->form()->phone} <br><br>
								Reference: {$result->reference} <br><br>
								Customer Reference: {$result->customer_reference} <br><br>
								Paid Amount: {$result->paid_amount}<br><br>
								Topup Amount: {$result->topup_amount}<br><br>
								Topup Currency: {$result->topup_currency}<br><br>
								Service Charge: NGN {$extra_charge}";


                                (new ActivityLog())->updateLog($ProcessStr, "[AIRTIME] Service Rendered, \n {$message}", 'SUCCESS');



                                // Add to Sales

                                $SalesPoint->trade_key = $trade_key;
                                $SalesPoint->trade_type = $payment_meta_info['type'];
                                $SalesPoint->ifrom_address = sodium_bin2hex($pk);
                                $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                                $SalesPoint->valueorqtyexchanged = $discountedAmount;
                                $SalesPoint->extracharge = $extra_charge;
                                $SalesPoint->rawamt = $discountedAmount;
                                $SalesPoint->icurrency = 'ARTM';
                                $SalesPoint->status = $SalesPoint->getTradeStatus('COMPLETED');
                                $SalesPoint->proofoftrade = $result->reference;
                                $SalesPoint->proofoftradeformat = 'REFERENCE';
                                $SalesPoint->tradehistory = $message;
                                $SalesPoint->created_at = Carbon::now();
                                $SalesPoint->updated_at = Carbon::now();
                                $SalesPoint->checksum = $checksum;

                                if( $SalesPoint->create() ){

                                    (new ActivityLog())->updateLog($ProcessStr,"[AIRTIME] Sale Recored", 'SUCCESS');


                                    (new MailService())->sendOrderReceipt($message, strtoupper("{$payment_meta_info['type']}(".sodium_bin2hex($trade_key).")") );

                                    $data = [ 'msg' => 'Trade completed', 'receipt'=>$result, 'success' => true ];
                                }else{
                                    throw new \Exception("Couldn't complete airtime trade, please try again later");
                                }

                            }else {
                                (new ActivityLog())->updateLog($ProcessStr,"[AIRTIME] Service Not Rendered, \n {$message}", 'FAIL');
                                throw new \Exception("Service rendering not completed, retry or contact admin");
                            }

                        }else {
                            throw new \Exception("Unable to complete airtime trade, try again later");
                        }


                    }else{
                        $data = [ 'msg' => $Validation->getErrorString(), 'success' => false ];
                    }

                }catch(\Throwable $t){

                    $data = [ 'msg' =>  $t->getMessage(), 'success' => false ];

                    if(!is_null($ProcessStr))
                        (new ActivityLog())->updateLog($ProcessStr, "[AIRTIME] {$data['msg']}", 'FAIL');

                }finally{


                    echo Response::fromApi($data, 200);
                    unset($Transaction, $bill, $SalesPoint);

                }
            }


            public function SellAirtimeViaSNS(Param $param){

                try{


                    $AirtimeMin = (new Settings())->getAirtimeMinSale();
                    $AirtimeMax = (new Settings())->getAirtimeMaxSale();

                    $Validation = new Validation(
                        ['phone', 'text|minlength:11|maxlength:11|required'],
                        ['amount', "number|required|min:{$AirtimeMin}|max:{$AirtimeMax}"],
                        ['network_provider', 'required']
                    );

                    if( $Validation->isPassed() ){

                        $Wallet = new Wallet;

                        $network_provider = $param->form()->network_provider;
                        if($param->form()->network_provider == 'N9MOBILE')
                            $network_provider = "9MOBILE";

                        $message = $param->form()->message !== null ? 'Short Message: '.$param->form()->message : '';

                        list($message) = (new Sanitize())->clean( [ $message ] );

                        $service_charges = ( (new Settings())->getAirtimeSaleServiceChargeRate($network_provider) / 100 ) * $param->form()->amount;
                        $amount = $param->form()->amount;

                        // Initiate Airtime Trade Transaction



                        // Initiate Airtime selling transaction
                        $Transaction = new Transaction;

                        $pk = $Wallet->getAnyAdminPublickey();
                        $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', sodium_bin2hex($pk) ] )->get();

                        $payment_meta_info = [
                            'type' => (new Transaction())->getTransactionTypes('AIRTIME_TRADE'),
                            'to_address' => $Wallet->getPublickey(),
                            'from_address'=>
                                [
                                    'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                    'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                                ]
                        ];
                        $Transaction->addTransferTrans(
                            $payment_meta_info['type'],
                            'PENDING',
                            [
                                $payment_meta_info['from_address']['pubk'],
                                $payment_meta_info['from_address']['prik']
                            ],
                            $payment_meta_info['to_address'],
                            $amount,
                            false,
                            false
                        );

                        $TransLastInsert = $Transaction->lastInsert();
                        $trade_key = (new Transaction())->filter('trans_hash')->where( ['id', $TransLastInsert] )->get()->trans_hash;

                        // Add to Sales
                        $SalesPoint = new SalesPoint;

                        $f = sodium_bin2hex($payment_meta_info['to_address']);
                        $message .= "<br>
						Airtime Sold out via SHARE N SELL<br><br>
						NaijaSub Web Service Bought {$network_provider} airtime from <b>{$f}</b><br><br>
						<b>Phone ID:</b> {$param->form()->phone}<br><br>
						<b>Amount:</b> NGN {$amount}<br><br>
						<b>Status:</b> <span style='color: red;'>{$SalesPoint->getTradeStatus('PROGRESS')}</span>
						";

                        $SalesPoint->trade_key = $trade_key;
                        $SalesPoint->trade_type = $payment_meta_info['type'];
                        $SalesPoint->ifrom_address = sodium_bin2hex($pk);
                        $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                        $SalesPoint->valueorqtyexchanged = $amount;
                        $SalesPoint->extracharge = $service_charges;
                        $SalesPoint->rawamt = $amount;
                        $SalesPoint->icurrency = 'ARTM';
                        $SalesPoint->status = $SalesPoint->getTradeStatus('PROGRESS');
                        $SalesPoint->proofoftrade = (new Encryption())->authKey();
                        $SalesPoint->proofoftradeformat = 'CRYPTOGRAPHIC_KEY';
                        $SalesPoint->tradehistory = $message;
                        $SalesPoint->created_at = Carbon::now();
                        $SalesPoint->updated_at = Carbon::now();

                        if( $SalesPoint->create() ){

                            (new MailService())->sendOrderReceipt($message, strtoupper("{$payment_meta_info['type']}(".sodium_bin2hex($trade_key).")") );

                            $data = [ 'msg' => 'Please wait while your request is being processed' , 'success' => true ];
                        }else{
                            throw new \Exception("Couldn't complete airtime trade, please try again later");
                        }


                    }else{
                        $data = [ 'msg' => $Validation->getErrorString(), 'success' => false ];
                    }

                }catch(\Throwable $t){
                    $data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
                }finally{
                    echo Response::fromApi($data, 200);
                }
            }

            public function SellAirtimeViaPIN(Param $param){

                try{



                    $AirtimeMin = (new Settings())->getAirtimeMinSale();
                    $AirtimeMax = (new Settings())->getAirtimeMaxSale();

                    $Validation = new Validation( ['airtime_pin', 'text|required'], ['amount', "number|required|min:{$AirtimeMin}|max:{$AirtimeMax}"], ['network_provider', 'required'] );

                    if( $Validation->isPassed() ){

                        $Wallet = new Wallet;

                        $network_provider = $param->form()->network_provider;
                        if($param->form()->network_provider == 'N9MOBILE')
                            $network_provider = "9MOBILE";

                        $message = $param->form()->message !== null ? 'Short Message: '.$param->form()->message : '';

                        list($airtime_pin, $message) = (new Sanitize())->clean( [ $param->form()->airtime_pin, $message ] );

                        $extra_charge = 0.00;
                        $service_charges = ( (new Settings())->getAirtimeSaleServiceChargeRate($network_provider) / 100 ) * $param->form()->amount;
                        $amount = $param->form()->amount;


                        // Initiate Airtime selling transaction
                        $Transaction = new Transaction;

                        $pk = $Wallet->getAnyAdminPublickey();
                        $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', sodium_bin2hex($pk) ] )->get();

                        $payment_meta_info = [
                            'type' => (new Transaction())->getTransactionTypes('AIRTIME_TRADE'),
                            'to_address' => $Wallet->getPublickey() ,
                            'from_address'=>
                                [
                                    'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                    'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                                ]
                        ];
                        $Transaction->addTransferTrans(
                            $payment_meta_info['type'],
                            'PENDING',
                            [
                                $payment_meta_info['from_address']['pubk'],
                                $payment_meta_info['from_address']['prik']
                            ],
                            $payment_meta_info['to_address'],
                            $amount,
                            false,
                            false
                        );

                        $TransLastInsert = $Transaction->lastInsert();
                        $trade_key = (new Transaction())->filter('trans_hash')->where( ['id', $TransLastInsert] )->get()->trans_hash;

                        // Add to Sales
                        $SalesPoint = new SalesPoint;

                        $f = sodium_bin2hex($payment_meta_info['to_address']);
                        $message .= "
						Airtime Sold out VIA Recharge Pin<br><br>
						NaijaSub Web Service Bought airtime from <b>{$f}</b><br><br>
						<b>PIN:</b> {$airtime_pin}<br><br>
						<b>Amount:</b> {$amount}<br><br>
						<b>Status</b> <span style='color: red;'>{$SalesPoint->getTradeStatus('PROGRESS')}</span>

						";

                        $SalesPoint->trade_key = $trade_key;
                        $SalesPoint->trade_type = $payment_meta_info['type'];
                        $SalesPoint->ifrom_address = sodium_bin2hex($pk);
                        $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                        $SalesPoint->valueorqtyexchanged = $amount;
                        $SalesPoint->extracharge = $extra_charge;
                        $SalesPoint->rawamt = $amount;
                        $SalesPoint->icurrency = 'ARTM';
                        $SalesPoint->status = $SalesPoint->getTradeStatus('PROGRESS');
                        $SalesPoint->proofoftrade = (new Encryption())->authKey();
                        $SalesPoint->proofoftradeformat = 'CRYPTOGRAPHIC_KEY';
                        $SalesPoint->tradehistory = $message;
                        $SalesPoint->created_at = Carbon::now();
                        $SalesPoint->updated_at = Carbon::now();

                        if( $SalesPoint->create() ){

                            (new MailService())->sendOrderReceipt($message, strtoupper("{$payment_meta_info['type']}(".sodium_bin2hex($trade_key).")") );

                            $data = [ 'msg' => 'Please wait while your request is being processed' , 'success' => true ];
                        }else{
                            throw new \Exception("Couldn't complete airtime trade, please try again later");
                        }


                    }else{
                        $data = [ 'msg' => $Validation->getErrorString(), 'success' => false ];
                    }

                }catch(\Throwable $t){
                    $data = [ 'msg' =>  $t->getMessage(), 'success' => false ];



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
