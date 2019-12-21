<?php
namespace src\client\service;
use \zil\core\tracer\ErrorTracer;

    use src\client\model\Settings;
    use \zil\factory\Logger;


	class Bill{

		private $baseUrl = 'https://sales.ringo.ng/api/';
		private $username = 'Oshegztelecoms@gmail.com';
		private $password = 'Michaelseg1';



		public function __construct(){
			if( empty( (new Settings())->getBillApiToken() ) )
				$this->authenticate( $this->username, $this->password );
		}

	
		private function reviveConnection() : bool {
			try{

				$token = (new Settings())->getBillApiToken();
				$ch = curl_init();
				
				curl_setopt_array($ch, [
					CURLOPT_URL => $this->baseUrl.'status',
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_HTTPHEADER => [
						'Content-Type: application/json',
						"Authorization: Bearer {$token}"
					]
				]);



				$result = json_decode( curl_exec($ch) );

				if( $result === false  || isset($result->error) ){
					throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
				}else{

					if( isset($result->code) && $result->code == 'TOKEN_EXPIRED') {
                        $Authresult = $this->authenticate( $this->username, $this->password );

                        if($Authresult->token > 0){
                            return true;
                        }else{
                            throw new \Exception("Couldn't authorize to acquire a new token");
                        }
					}elseif (isset($result->token)){
                        (new Settings())->updateBillApiToken($result->token);
                        return true;
                    } 
				}
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}finally{
				curl_close($ch);
			}
		}

		public function authenticate( string $username, string $password ){
			try{

				$ch = curl_init();

				curl_setopt_array($ch, [
					CURLOPT_URL => $this->baseUrl.'auth',
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => json_encode([ 'username'=>$username,'password'=>$password ]),
					CURLOPT_HTTPHEADER => [
						'Content-Type: application/json',
						'Accept: application/json'
					]
					
				]);

				$result = json_decode( curl_exec($ch) );

				if( $result === false || isset($result->error) ){
					throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
				}else{
					if(isset($result->token)) {
                        (new Settings())->updateBillApiToken($result->token);
                        return $result;
                    }else{
					    return new \stdClass();
                    }
				}

				
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}finally{
				curl_close($ch);
			}
			
		} 

		public function MSISDNInfo(string $msisdnorphone, $type){
			try{

			    $this->reviveConnection();

				if($type == 'DATA')
					$subUrl = "datatopup/info/";
				elseif( $type == 'AIRTIME')
					$subUrl = "topup/info/";
				
				$token = (new Settings())->getBillApiToken();

				$ch = curl_init();

				curl_setopt_array($ch, [
					CURLOPT_URL => $this->baseUrl."{$subUrl}{$msisdnorphone}",
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_HTTPHEADER => [
						'Content-Type: application/json',
						"Authorization: Bearer {$token}"
					]
				]);

				$result = json_decode( curl_exec($ch) );


				if( isset($result->error)  ){
					throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
				}else{
					return $result;
				}

				
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}finally{
				curl_close($ch);
			}
			
		}

        public function ElectricityTopUp(string $meterno, float $amount, string $product_id, bool $is_prepaid){


                try{

                    $this->reviveConnection();

                    $token = (new Settings())->getBillApiToken();

                    $ch = curl_init();

                    curl_setopt_array($ch, [
                        CURLOPT_URL => $this->baseUrl."billpay/electricity/{$meterno}",
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_POST => 1,
                        CURLOPT_POSTFIELDS => json_encode([
                            "meter" => "$meterno",
                            "prepaid" => $is_prepaid,
                            "denomination" => "$amount",
                            "product_id" => "$product_id"
                        ]),
                        CURLOPT_HTTPHEADER => [
                            'Content-Type: application/json',
                            "Authorization: Bearer {$token}"
                        ]
                    ]);

                    $result = json_decode( curl_exec($ch) );

//                    var_dump($result);
                    if( $result === false || isset($result->error) ){
                        throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
                    }else{
                        return $result;
                    }


                }catch(\Throwable $t){
                    new ErrorTracer($t);
                }finally{
                    curl_close($ch);
                }


            }

        public function NonElectricityTopUp(string $smartcardno, string $service_id, string $product_id, string $product_option_code){


                try{
                    $this->reviveConnection();

                    $token = (new Settings())->getBillApiToken();

                    $ch = curl_init();

                    curl_setopt_array($ch, [
                        CURLOPT_URL => $this->baseUrl."billpay/{$service_id}/$product_id/{$product_option_code}",
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_POST => 1,
                        CURLOPT_POSTFIELDS => json_encode([
                            "meter" => $smartcardno,
                            "prepaid" => true,
                        ]),
                        CURLOPT_HTTPHEADER => [
                            'Content-Type: application/json',
                            "Authorization: Bearer {$token}"
                        ]
                    ]);

                    $result = json_decode( curl_exec($ch) );

                    if( $result === false || isset($result->error) ){
                        throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
                    }else{

                        return $result;
                    }


                }catch(\Throwable $t){
                    new ErrorTracer($t);
                }finally{
                    curl_close($ch);
                }

            }

        public function GetListOfBillServicesInNigeria(?string $bill_type) {


                try{

                    $this->reviveConnection();

                    $token = (new Settings())->getBillApiToken();

                    $client_url = $this->baseUrl."billpay/country/NG/{$bill_type}";
                    if(is_null($bill_type))
                        $client_url = $this->baseUrl."billpay/country/NG";

                    /** @var $ch  Get product information based on type*/
                    $ch = curl_init();

                    curl_setopt_array($ch, [
                        CURLOPT_URL => $client_url,
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_HTTPHEADER => [
                            'Content-Type: application/json',
                            "Authorization: Bearer {$token}"
                        ]
                    ]);

                    $result = json_decode( curl_exec($ch) );

                    if( $result === false || isset($result->error) ){
                        throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
                    }else{

                        return $result;
                    }

                }catch(\Throwable $t){
                    new ErrorTracer($t);
                }finally{
                    curl_close($ch);
                }

            }

        public function GetListOfProductOptionsOfServiceInNigeria(string $service_id, string $product_id) {


            try{

                $this->reviveConnection();

                $token = (new Settings())->getBillApiToken();

                $client_url = $this->baseUrl."billpay/{$service_id}/{$product_id}";

                /** @var $ch  Get product information based on type*/
                $ch = curl_init();

                curl_setopt_array($ch, [
                    CURLOPT_URL => $client_url,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        "Authorization: Bearer {$token}"
                    ]
                ]);

                $result = json_decode( curl_exec($ch) );

                if( $result === false || isset($result->error) ){
                    throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
                }else{

                    return $result;
                }

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }finally{
                curl_close($ch);
            }

        }


        public function GetListOfBillServicesInNigeriaRespectiveToType(string $bill_type) {


            try{

                $this->reviveConnection();

                $token = (new Settings())->getBillApiToken();

                /** @var $ch  Get product information based on type*/
                $ch = curl_init();

                curl_setopt_array($ch, [
                    CURLOPT_URL => $this->baseUrl."billpay/country/NG/{$bill_type}",
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        "Authorization: Bearer {$token}"
                    ]
                ]);

                $result = json_decode( curl_exec($ch) );

                if( $result === false || isset($result->error) ){
                    throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
                }else{

                    return $result;
                }

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }finally{
                curl_close($ch);
            }

        }

        public function GetSmartCardOrMeterNoDetails(string $smartcardno_or_meter_no, string $service_id, string $product_id){


            try{

                $this->reviveConnection();

                $postdata = json_encode([
                    "meter" => "$smartcardno_or_meter_no",
                ]);

                /** @var $ch  Get product information based on type*/
                $chrl = curl_init();

                curl_setopt_array($chrl, [
                    CURLOPT_URL => $this->baseUrl."billpay/{$service_id}/{$product_id}/validate",
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POST => 1,
                    CURLOPT_FRESH_CONNECT => 1,
                    CURLOPT_POSTFIELDS => $postdata,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        "Authorization: Bearer ".(new Settings())->getBillApiToken()
                    ]
                ]);

                $result = json_decode( curl_exec($chrl) );

                if( $result === false || isset($result->error) ){
                    throw new \Exception("Couldn't connect to bill system, try again later. \n" );
                }else{

                    return $result;
                }


            }catch(\Throwable $t){
                new ErrorTracer($t);
            }finally{
                curl_close($chrl);
            }

        }
		
		public function AirtimeAndDataTopUp(string $msisdnorphone,  float $amount, string $type){


			try{


				$this->reviveConnection();

                $rst = $this->MSISDNInfo($msisdnorphone, $type);

                if(isset($rst->opts->msisdn) && $rst->opts->msisdn == $msisdnorphone){

                    if($type == 'DATA') {

                        $subUrl = "datatopup/exec/";

                        $send_sms = false;
                        $sms_text = '';

                    }elseif( $type == 'AIRTIME'){

                        $subUrl = "topup/exec/";
                        $send_sms = false;
                        $sms_text = "";

                    }

                    $token = (new Settings())->getBillApiToken();


                    $postData = json_encode([
                        "product_id" => $rst->products[0]->product_id,
                        "denomination" => "$amount",
                        "send_sms" => $send_sms,
                        "sms_text" => "$sms_text",
                        "customer_reference" => sodium_bin2hex( random_bytes(64) )
                    ]);


                    $ch = curl_init();

                    curl_setopt_array($ch, [
                        CURLOPT_URL => $this->baseUrl."{$subUrl}{$msisdnorphone}",
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_POST => 1,
                        CURLOPT_POSTFIELDS => $postData,
                        CURLOPT_HTTPHEADER => [
                            'Content-Type: application/json',
                            'Authorization: Bearer '.$token
                         ]
                    ]);

                    $result = json_decode( curl_exec($ch) );

                    if( $result === false || isset($result->error) ){
                        throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
                    }

                    curl_close($ch);
                    return $result;


                }else{
				    return false;
                }


				
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}finally{
				
			}
			

		}

		public function checkTransactionStatusOfTopUpForAirtimeAndData(string $customer_reference){
			try{

			    $this->reviveConnection();
	
				$token = (new Settings())->getBillApiToken();

				$ch = curl_init();

				curl_setopt_array($ch, [
					CURLOPT_URL => $this->baseUrl."topup/log/byref/{$customer_reference}",
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_POST => 1,
					CURLOPT_HTTPHEADER => [
						'Content-Type: application/json',
						"Authorization: Bearer {$token}"
					]
				]);

				$result = json_decode( curl_exec($ch) );
				
				if( $result === false || isset($result->error) ){
					throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($ch) );
				}else{

					return $result;
				}

				
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}finally{
				curl_close($ch);
			}
		}

		public function SendSMS(string $msisdnorphone,  string $message ){


			try{

			    $this->reviveConnection();

				if( strlen($message) > 140)
					throw new \Exception("Message too long, max should be 140 characters");

				$token = (new Settings())->getBillApiToken();
				
				$ch = curl_init();

				curl_setopt_array($ch, [
					CURLOPT_URL => $this->baseUrl."sms/{$msisdnorphone}",
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => json_encode([
						"message" => $message
					]),
					CURLOPT_HTTPHEADER => [
						'Content-Type: application/json',
						"Authorization: Bearer {$token}"
					]
				]);

				$result = json_decode( curl_exec($ch) );
//                var_dump($result);
//                die();
				if( $result === false || isset($result->error) ){
					throw new \Exception("Couldn't connect to bill system, try again later. \n".curl_error($result->error) );
				}else{

					return $result;
				}



			}catch(\Throwable $t){
				new ErrorTracer($t);
			}finally{
				curl_close($ch);
			}
			

        }
        

	} 

?>
