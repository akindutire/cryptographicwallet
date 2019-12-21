<?php
        namespace src\client\service;

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
         * @Service:RaveAdapter []
        */

        class RaveAdapter{

            private $raveVerifyAccUrl = 'https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify';


            public function __construct(){ }

            public function confirmCardPaymentToRave(string $transactionReference) {

                try{

                    $ch = curl_init();

                    curl_setopt_array($ch, [
                        CURLOPT_URL => $this->raveVerifyAccUrl,
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_POST => 1,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_HTTPHEADER => [
                            'Content-Type: application/json'
                        ],
                        CURLOPT_POSTFIELDS => json_encode([
                            "SECKEY" => "FLWSECK-8e559c894959592f5ff87ccb6f0350fc-X",
                            "txref" => $transactionReference,
                        ])
                    ]);

                    $result = json_decode( curl_exec($ch) );

                    if ( curl_errno($ch) )
                        throw new \Exception(curl_error($ch));

                    // Logger::Init();
                    // Logger::Log("+-------RAVE VERIFICATION RESPONSE ----------+", $result,$transactionReference, "+----------------------+");
                    // Logger::kill();

                    return $result;



                }catch(\Throwable $t){
                    new ErrorTracer($t);
                }finally{
                    curl_close($ch);
                }

            }
        }
