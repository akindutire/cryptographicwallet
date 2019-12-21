<?php
        namespace src\client\controller;

        use Carbon\Carbon;
        use src\client\middleware\Date;
        use src\client\middleware\SecureApi;
        use src\client\model\SalesPoint;
        use src\client\model\Transaction;
        use src\client\model\Wallet;
        use \zil\core\server\Param;
        use \zil\core\server\Response;
        use \zil\factory\View;
        use \zil\core\facades\helpers\Notifier;
        use \zil\core\facades\helpers\Navigator;
        use \zil\core\facades\decorators\Hooks;

        use src\client\Config;
        use zil\security\Encryption;
        use zil\security\Validation;

        /**
         *  @Controller:GiftCardApiController []
        */

        class GiftCardApiController{

            use Notifier, Navigator, Hooks;

            /**
             * Currently Suspended Trade for the meantime
             *
             * @param Param $param
             * @return void
             */
            public function SaveGiftCardProofOfTrade(Param $param){
                try{
                    $response = (new SalesPoint())->uploadGiftCardProofOfTrade($_FILES['file']);

                    if($response['status'] == true){
                        $data = [ 'proofoftrade' => $response['proofsrc'], 'proofoftradename' => $response['proofname'], 'success' => true ];


                    }else{
                        $data = [ 'msg' => $response['error'], 'success' => false ];
                    }

                }catch(\Throwable $t){
                    $data = [ 'msg' => $t->getMessage(), 'success' => false ];
                }finally{
                    echo Response::fromApi($data, 200);
                }

            }

            /**
             * Currently Suspended Trade for the meantime
             *
             * @param Param $param
             * @return void
             */
            public function GiftCard(Param $param){
                try{

                    $Validation = new Validation( ['giftcard_amount', 'number|required|min:0'], ['giftcard_type', 'text|required'], ['giftcard_proofoftrade', 'text|required'] );

                    if($Validation->isPassed()){

                        $SalesPoint = new SalesPoint;

                        $SalesPoint->trade_key = (new Encryption())->generateShortHash();
                        $SalesPoint->trade_type = (new Transaction())->getTransactionTypes('GIFTCARD_TRADE');
                        $SalesPoint->ifrom_address = sodium_bin2hex( (new Wallet())->getPublickey() );
                        $SalesPoint->ito_address = sodium_bin2hex( (new Wallet())->getAnyAdminPublickey() );
                        $SalesPoint->valueorqtyexchanged = 1;
                        $SalesPoint->extracharge = 0;
                        $SalesPoint->rawamt = floatval($param->form()->giftcard_amount);
                        $SalesPoint->icurrency = 'USD';
                        $SalesPoint->proofoftrade = strip_tags($param->form()->giftcard_proofoftrade);
                        $SalesPoint->proofoftradeformat = 'IMAGE';
                        $SalesPoint->status = $SalesPoint->getTradeStatus('PROGRESS');
                        $SalesPoint->created_at = Carbon::now();
                        $SalesPoint->updated_at = Carbon::now();

                        if ( $SalesPoint->create() ){
                            $data = [ 'msg' => "Trade completed, Please wait while trade is being confirmed", 'success' => true ];
                        }else{
                            throw new \Exception("Trade not accomplished, Please retry");
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

            public function onInit(Param $param)
            {
                new Date($param);
            }

            public function onAuth(Param $param)
            {
                new SecureApi($param);
            }

        }
