<?php
        namespace src\client\service;

        use src\client\model\ActivityLog;
        use src\client\model\ExtraUserInfo;
        use src\client\model\User;
        use src\client\model\Wallet;
        use \zil\core\server\Http;
        use zil\core\tracer\ErrorTracer;
        use \zil\factory\Session;
        use \zil\factory\Fileuploader;
        use \zil\factory\Filehandler;
        use \zil\factory\Logger;
        use \zil\factory\Mailer;
        use \zil\factory\Redirect;

        use \zil\security\Encryption;
        use \zil\security\Sanitize;

        /**
         * @Service:Bvnverifier []
        */

        class Bvnverifier{

            private $payStackSecretkey = "sk_live_f239629e48b609c1ef8684e95988591b0b19e572";

            public function __construct(){ }

            /**
             * @param string $bvn
             * @return bool
             */
            public function validate(string $bvn) : object {
                try{

                   /**
                    * Get user account details
                    */

                    $ExUser = new ExtraUserInfo();
                    $id = $ExUser->getUserId();

                    $AccountDetailsObj = $ExUser->filter('email')->where([ 'id', $id]);

                    if($AccountDetailsObj->count() == 1){


                        $payStackLiveEndPoint = "https://api.paystack.co/bank/resolve_bvn/{$bvn}";
                        $curlStart = curl_init();

                        curl_setopt_array($curlStart, [
                            CURLOPT_URL => $payStackLiveEndPoint,
                            CURLOPT_RETURNTRANSFER => 1,
                            CURLOPT_HTTPHEADER => [
                                'Content-Type: application/json',
                                "Authorization: Bearer {$this->payStackSecretkey}"
                            ]
                        ]);

                        $result = json_decode(curl_exec($curlStart));
                        if( $result === false  || isset($result->error) ){

                            throw new \Exception("Couldn't connect to paystack api, try again later. \n".curl_error($curlStart) );

                        }else{
                            Logger::Init();
                            Logger::Log("BVN Res. ", $result, "------------");
                            Logger::kill();
                            return $result;
                        }

                    } else {
                        Logger::Init();
                            Logger::ELog("Account not found ");
                        Logger::kill();

                        return false;
                    }

                } catch(\Throwable $t) {
                    new ErrorTracer($t);
                }
            }
        }
