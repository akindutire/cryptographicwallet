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
        use src\client\model\Wallet;
        use src\client\service\BlockIOCoinTransferProvider;
        use src\client\service\CoinPaymentTransferProvider;
        use src\client\service\MailService;
        use zil\core\scrapper\Info;
        use \zil\core\server\Param;
        use \zil\core\server\Response;
        use zil\core\tracer\ErrorTracer;
        use zil\factory\Database;
        use zil\factory\Logger;
        use \zil\factory\View;
        use \zil\core\facades\helpers\Notifier;
        use \zil\core\facades\helpers\Navigator;
        use \zil\core\facades\decorators\Hooks;

        use src\client\Config;
        use zil\security\Validation;

        /**
         *  @Controller:BitcoinApiController []
        */

        class BitcoinApiController{

            use Notifier, Navigator, Hooks;

            /**
             * Trade Bitcoin
             *
             * @param Param $param
             * @return void
             */


            public function SellBitcoinToNaijaSub(Param $param) : void{
                try{

                    $Validation = new Validation(
                        ['bitcoin_quantity', 'number|required|min:0']
                    );

                    if($Validation->isPassed()){

                        /**
                         * Run checksum
                         */
                        $checksum = null;
                        if(isset($param->form()->timestamp)){
                            $timestamp = $param->form()->timestamp;

                            $checksum = (new Authtoken())->getCheckSum($timestamp);

                            if ( (new Transaction())->isExists( ['checksum', $checksum] ) ){
                                $data = [ 'msg' =>  "Transaction already saved", 'success' => true ];
                                return;
                            }
                        }

                        $Pr = (new ActivityLog())->Log("[BITCOIN SALE] Validation Passed", 'SUCCESS');

                        if(is_null($Pr))
                            throw new \Exception("An error occurred, process stream broken, please retry");


                        if( !($param->form()->bitcoin_quantity > 0) )
                            throw new \Exception("Bitcoin Quantity must be more than 0");


                        $handle = (new Database())->connect();
                        $handle->beginTransaction();

                        /**
                         * How much naijasub buys bitcoin
                         */
                        $bitCoinBuyingRate = (new Settings())->getBitcoinBuyingRate();

                        /**
                         * Add Sales to transaction pool
                         */

                        $Wallet = new Wallet;

                        $ref_pk = sodium_bin2hex( $Wallet->getPublickey() );
                        $from_prime_pk = sodium_bin2hex($Wallet->getAnyAdminPublickey());

                        if( $Wallet->isValid($ref_pk) && $Wallet->isValid($from_prime_pk) ){

                            $btcQty = floatval( round($param->form()->bitcoin_quantity, 8) );

                            $bitcoin_NGNAmt =  floatval( round(($btcQty * $bitCoinBuyingRate),2)  );


                            $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', $from_prime_pk] )->get();

                            $Transaction = new Transaction;
                            $payment_meta_info = [
                                'type' => $Transaction->getTransactionTypes('BITCOIN_TRADE'),
                                'to_address' => $Wallet->getPublickey(),
                                'from_address'=>
                                    [
                                        'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                        'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                                    ],
                                'trusted' => false,
                                'freeze' => false
                            ];

                            // transfer between prime wallet and ordinary without system event - NO_SESSION REQ.
                            $Transaction->addTransferTrans(
                                $payment_meta_info['type'],
                                'PENDING',
                                [
                                    $payment_meta_info['from_address']['pubk'],
                                    $payment_meta_info['from_address']['prik']
                                ],
                                $payment_meta_info['to_address'],
                                $bitcoin_NGNAmt,
                                $payment_meta_info['trusted'],
                                $payment_meta_info['freeze']
                            );

                            $TransLastInsert = $Transaction->lastInsert();

                            /**
                             * Save Checksum
                             */
                            $Transaction->checksum = $checksum;
                            $Transaction->where(['id', $TransLastInsert])->update();

                            $trade_key = (new Transaction())->filter('trans_hash')->where( ['id', $TransLastInsert] )->get()->trans_hash;

                            (new ActivityLog())->updateLog($Pr,"[BITCOIN TRANSACTION INITIATIED] Wallet balance check & Transaction Staging", 'SUCCESS');


                        }else{
                            (new ActivityLog())->updateLog($Pr, "[BITCOIN TRANSACTION INITIATIED] Wallet balance check & Transaction Staging", 'FAIL');
                            throw new \Exception("Error: Couldn't complete transaction, invalid wallets are involved in this transaction");
                        }

                        /**
                         * CoinPayment API
                         */

                        $Ex = new ExtraUserInfo();
                        $Uid = $Ex->getUserId();

                        $C = $Ex->filter('email', 'name')->where( ['id', $Uid] )->get();

                        $cT = new CoinPaymentTransferProvider();
                        $cT = $cT->ProposeBitcoinAcceptance($btcQty, 'BTC', $C->email, $C->name);

                        if($cT != false) {
                            $cTTxn_id = $cT['txn_id'];
                            $cTAddress = $cT['address'];
                            $cTTimeout = $cT['timeout'] / 60;
                        }else{
                            throw new \Exception("Couldn't complete transaction, please retry");
                        }

                        // Add transaction note
                        $Transaction->note = "BITCOIN SALE<br><br><b>BTC: {$btcQty}</b> cost of <b>NGN{$bitcoin_NGNAmt}</b> @NGN{$bitCoinBuyingRate}/BTC<br><br>Payment Address: {$cTAddress}<br><br>Tnx id: {$cTTxn_id}";
                        $sh_note = $Transaction->note;
                        $Transaction->where( ['id', $TransLastInsert] )->update();

                        $SalesPoint = new SalesPoint;

                        $SalesPoint->trade_key = $trade_key;
                        $SalesPoint->trade_type = (new Transaction())->getTransactionTypes('BITCOIN_TRADE');
                        $SalesPoint->ifrom_address = sodium_bin2hex($payment_meta_info['from_address']['pubk']);
                        $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                        $SalesPoint->valueorqtyexchanged = $btcQty;
                        $SalesPoint->extracharge = 0.00;
                        $SalesPoint->rawamt = $btcQty * $bitCoinBuyingRate;
                        $SalesPoint->icurrency = 'BTC';
                        $SalesPoint->proofoftrade = $cTTxn_id;
                        $SalesPoint->proofoftradeformat = 'CRYPTOGRAPHIC_KEY';
                        $SalesPoint->status = $SalesPoint->getTradeStatus('PROGRESS');
                        $SalesPoint->created_at = Carbon::now();
                        $SalesPoint->updated_at = Carbon::now();
                        $SalesPoint->checksum = $checksum;

                        if ( $SalesPoint->create() && !empty($cTAddress) ){

                            $data = ['transaction_id' => $cTTxn_id, 'msg' => "Trade initiated, Please transfer BTC{$btcQty} to naijasub within {$cTTimeout}min. via {$cTAddress} and confirm your payment", 'address' => $cTAddress, 'timeout' => $cTTimeout, 'success' => true ];

                            $handle->commit();

                            (new ActivityLog())->updateLog($Pr, "[BITCOIN SALE(from Customer) TRANSACTION INITIATIED] {$sh_note}", 'SUCCESS');

                        }else{

                            (new ActivityLog())->updateLog($Pr,"[BITCOIN SALE(from Customer) TRANSACTION INITIATIED] {$sh_note}", 'FAIL');

                            throw new \Exception("Trade not accomplished, Please retry");
                        }

                    }else{


                        $data = [ 'msg' => $Validation->getErrorString(), 'success' => false ];
                    }

                }catch(\Throwable $t){

                    $handle->rollback();
                    $data = [ 'msg' => $t->getMessage(), 'success' => false ];

                    if(!is_null($Pr))
                    (new ActivityLog())->updateLog($Pr,"[BITCOIN TRANSACTION INITIATIED] {$data['msg']}", 'FAIL');

                }finally{
                    echo Response::fromApi($data, 200);
                }
            }

            public function BuyBitcoinFromNaijaSub(Param $param){
                try{

                    $handle = (new Database())->connect();
                    $handle->beginTransaction();

                    $id = (new ExtraUserInfo())->getUserId();
                    $email = (new ExtraUserInfo())->filter('email')->where( ['id', $id] )->get()->email;

                    if( (new Transaction())->isLocked($email) ){
                        throw new \Exception("You are not eligible to make transactions at the moment, because of pending transaction\nResolve pending transaction first");
                    }

                    $Validation = new Validation(
                        ['bitcoin_quantity', 'number|required|min:0'],
                        ['blockchain_publicaddress', 'text|required']

                    );

                    if($Validation->isPassed()){

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

                        if( !($param->form()->bitcoin_quantity > 0) )
                            throw new \Exception("Bitcoin Quantity must be more than 0");


                        $Pr = (new ActivityLog())->Log("[BITCOIN PURCHASE(from Customer) TRANSACTION INITIATED] Validation Passed", 'SUCCESS');

                        if(is_null($Pr))
                            throw new \Exception("An error occurred, process stream broken, please retry");


                        /**
                         * How much naijasub sells bitcoin
                         */
                        $bitCoinSellingRate = (new Settings())->getBitcoinSellingRate();



                        /**
                         * Add Sales to transaction pool
                         */

                        $Wallet = new Wallet;

                        $from_prime_pk = sodium_bin2hex( $Wallet->getPublickey() );
                        $ref_pk = sodium_bin2hex($Wallet->getAnyAdminPublickey());

                        if( $Wallet->isValid($ref_pk) && $Wallet->isValid($from_prime_pk) ){

                            $btcQty = floatval( round($param->form()->bitcoin_quantity, 8) );
                            $bitcoin_NGNAmt =  floatval( round( ($btcQty * $bitCoinSellingRate),2  )  );



                            if( !$Wallet->isSufficientBalance($bitcoin_NGNAmt) )
                                throw new \Exception("Error: You don't have enough fund in wallet to complete this trade");


                            (new ActivityLog())->updateLog($Pr,"[BITCOIN PURCHASE(from Customer) TRANSACTION INITIATIED] Wallet balance checkpoint Passed", 'SUCCESS');

                            /**
                             * BlockIO API
                             */
                            $cT = new CoinPaymentTransferProvider();
                            $ProposalResponse = $cT->ProposeBitcoinWithdrawal($btcQty, $param->form()->blockchain_publicaddress  );

                            if ( $ProposalResponse['status'] != true)
                                throw new \Exception("Trade not accomplished due service error, bitcoin wasn't transferred to {$param->form()->blockchain_publicaddress}, Please contact the admin for trade support\nNote: {$ProposalResponse['note']}");



                            $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', $from_prime_pk] )->get();

                            $Transaction = new Transaction;
                            $payment_meta_info = [
                                'type' => $Transaction->getTransactionTypes('BITCOIN_TRADE'),
                                'to_address' => $Wallet->getAnyAdminPublickey(),
                                'from_address'=>
                                    [
                                        'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                        'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                                    ],
                                'trusted' => false,
                                'freeze' => false
                            ];

                            // Add transaction note
                            $note = "BITCOIN PURCHASE<br><br><b>BTC: {$btcQty}</b> cost of <b>NGN{$bitcoin_NGNAmt}</b> @NGN{$bitCoinSellingRate}/BTC<br><br>Your Address: {$param->form()->blockchain_publicaddress}";


                            // transfer between prime wallet and ordinary without system event - NO_SESSION REQ.
                            $Transaction->addServiceTrans(
                                $payment_meta_info['type'],
                                'CONFIRMED',
                                [
                                    $payment_meta_info['from_address']['pubk'],
                                    $payment_meta_info['from_address']['prik']
                                ],
                                $payment_meta_info['to_address'],
                                $bitcoin_NGNAmt,
                                $note
                            );

                            $TransLastInsert = $Transaction->lastInsert();
                            /**
                             * Save Checksum
                             */
                            $Transaction->checksum = $checksum;
                            $Transaction->where(['id', $TransLastInsert])->update();


                            $trade_key = (new Transaction())->filter('trans_hash')->where( ['id', $TransLastInsert] )->get()->trans_hash;

                            (new ActivityLog())->updateLog($Pr,"[BITCOIN PURCHASE(from Customer) TRANSACTION INITIATIED] Transaction completes", 'SUCCESS');

                        }else{
                            throw new \Exception("Error: Couldn't complete transaction, invalid wallets are involved in this transaction");
                        }


                        $SalesPoint = new SalesPoint;

                        $SalesPoint->trade_key = $trade_key;
                        $SalesPoint->trade_type = (new Transaction())->getTransactionTypes('BITCOIN_TRADE');
                        $SalesPoint->ifrom_address = sodium_bin2hex($payment_meta_info['from_address']['pubk']);
                        $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                        $SalesPoint->valueorqtyexchanged = $btcQty;
                        $SalesPoint->extracharge = 0.00;
                        $SalesPoint->rawamt = $bitcoin_NGNAmt;
                        $SalesPoint->icurrency = 'BTC';
                        $SalesPoint->proofoftrade = $param->form()->blockchain_publicaddress;
                        $SalesPoint->proofoftradeformat = 'CRYPTOGRAPHIC_KEY';
                        $SalesPoint->status = $SalesPoint->getTradeStatus('COMPLETED');
                        $SalesPoint->created_at = Carbon::now();
                        $SalesPoint->updated_at = Carbon::now();
                        $SalesPoint->checksum = $checksum;

                        if ( $SalesPoint->create() ){

                            $data = [ 'msg' => 'Trade Sealed, Thank you', 'success' => true ];

                            $handle->commit();

                            (new ActivityLog())->updateLog($Pr,"[BITCOIN PURCHASE(from Customer) TRANSACTION INITIATIED] Added to SalesPoint/Trades", 'SUCCESS');

                        }else{
                            throw new \Exception("Trade not accomplished due to service error, Please retry");
                        }

                    }else{

                        $data = [ 'msg' => $Validation->getErrorString(), 'success' => false ];
                    }

                }catch(\Throwable $t){

                    $handle->rollback();
                    $data = [ 'msg' => $t->getMessage(), 'success' => false ];

                    (new ActivityLog())->updateLog($Pr, "[BITCOIN PURCHASE(from Customer) TRANSACTION INITIATIED] {$data['msg']}", 'FAIL');

                }finally{
                    echo Response::fromApi($data, 200);
                }
            }


            public function ProbeBitcoinTransfer(Param $param){

                try{
                    if(isset($param->url()->proof_of_trade)){
                        $proof_of_trade = $param->url()->proof_of_trade;

                        $Pr = (new ActivityLog())->Log("[BITCOIN TRANSACTION PROBE for {$proof_of_trade}] INITIATED", 'SUCCESS');
                        if(is_null($Pr))
                            throw new \Exception("An error occurred, process stream broken, please retry");

                        $SalesPoint = new SalesPoint;
                        if ( $SalesPoint->filter('trade_key')->where( [ 'proofoftrade', $proof_of_trade ] )->count() == 1){

                            $SaleReq = $SalesPoint->filter('trade_key', 'valueorqtyexchanged')->where( [ 'proofoftrade', $proof_of_trade ] )->get();


                            $cT = new CoinPaymentTransferProvider();

                            if($cT->isBitcoinTransfered( $proof_of_trade, floatval($SaleReq->valueorqtyexchanged) )){

                                if(  (new Transaction())->confirmTransaction( sodium_bin2hex($SaleReq->trade_key) ) ){

                                    $SalesPoint->status = $SalesPoint->getTradeStatus('COMPLETED');
                                    $SalesPoint->updated_at = Carbon::now();
                                    $SalesPoint->update();

                                    $message = "Trade Sealed<br><br>Transaction id: #".sodium_bin2hex($SaleReq->trade_key);
                                    (new MailService())->sendOrderReceipt($message, strtoupper(" {$SaleReq->trade_type}") );

                                    $data = [ 'msg' => 'Trade Sealed, Thank you', 'halt_probe'=> true, 'success' => true ];

                                    (new ActivityLog())->updateLog($Pr,"[BITCOIN TRANSACTION PROBE for {$proof_of_trade}] COMPLETED", 'SUCCESS');

                                }else{
                                    throw new \Exception("Error: Couldn't confirmed transaction trade");
                                }
                            }else{
                                $data = [ 'msg' => 'Trade in progress, waiting for funds', 'halt_probe' => false, 'success' => true ];

                            }

                        }else{
                            throw new \Exception("Unknown proof of trade");
                        }
                    }else{
                        throw new \Exception("Unknown proof of trade");
                    }
                }catch(\Throwable $t){

                    $data = [ 'msg' => $t->getMessage(), 'success' => false ];

                    if(!is_null($Pr))
                        (new ActivityLog())->updateLog($Pr,"[BITCOIN TRANSACTION PROBE for {$proof_of_trade}] {$data['msg']}", 'FAIL');


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
