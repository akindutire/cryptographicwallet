<?php
namespace src\client\service;

	use src\client\model\Settings;
use zil\core\tracer\ErrorTracer;
use \zil\factory\Database;
	use \zil\factory\BuildQuery;
	use \zil\factory\Session;
	use \zil\factory\Fileuploader;
	use \zil\factory\Filehandler;
	use \zil\factory\Logger;
	use \zil\factory\Mailer;
	use \zil\factory\Redirect;
	
	use \zil\security\Authentication;
	use \zil\security\Encryption;
	use \zil\security\Sanitize;



	use src\client\config\Config;
	
	class SmsProvider{

	    private $smsprime_apikey = "9D4337995D3F100120D07694F957D84D";
		private $sms_endpoint = "http://smsprime.com/api.module/oshegz000/xml/";
		
		private $bulksms_apikey = "uvGzd3n8wKYtlwiwbO0SPamxedA3V52aTBYkh9lriCIeB8saxirQp85LLOzr";
		private $bulksms_endpoint = "https://www.bulksmsnigeria.com/api/v1/sms/create";


	    private $username = 'oshegz000';
	    private $source = 'NaijaSub';

		public function __construct(){ }

		private function getSignature() : string {
			try{

				$signature = $this->username.$this->smsprime_apikey;

				return md5($signature);

			}  catch (\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function sendSMSViaBulk(string $to, string $message) {
			try{

				// $this->bulksms_endpoint."?api_token=".$this->bulksms_apikey."&from={$from}&to={$to}&body={$message}"
	
				$from = $this->source;

					$ch = curl_init();

					$Request = json_encode(
						[
							'api_token' => $this->bulksms_apikey,
							'from' => $from,
							'to' => $to,
							'body' => $message
						]
						);
					curl_setopt_array($ch, [
						CURLOPT_URL => $this->bulksms_endpoint,
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FRESH_CONNECT => 1,
						CURLOPT_POST => 1,
						CURLOPT_POSTFIELDS => $Request,
						CURLOPT_HTTPHEADER => [
							'Content-Type: application/json',
							'Accept: application/json'
						]
					]);

					$result = json_decode(curl_exec($ch));


					// Logger::Init();
					// 	Logger::Log($result );
					// Logger::kill();

					if( curl_errno($ch) || !isset($result->data)){

						Logger::Init();
							Logger::ELog("Couldn't connect to send sms, try again later. \n".curl_error($ch) );
						Logger::kill();

						return json_decode(json_encode(['status' => "fail" ]));
					}else{
						return $result->data;
					}


			}catch(\Throwable $t){
				new ErrorTracer($t);
			}finally{
				curl_close($ch);
			}

		}

		public function sendSMS(string $to, string $message)
		{

			try{

				$signature = $this->getSignature();
				$from = $this->source;

				$Request =
					"<?xml version=\"1.0\" encoding=\"utf-8\"?>
					<Request>
						<header>
							<auth>
							   <signature>{$signature}</signature>
							</auth>
						</header>
						<body>
							<method>send</method>
							<parameters>
								<type>default</type>
								<destination>{$to}</destination>
								<source>{$from}</source>
								<shortmessage>{$message}</shortmessage>
							</parameters>
						</body>
					</Request>";

					$ch = curl_init();

					curl_setopt_array($ch, [
						CURLOPT_URL => $this->sms_endpoint,
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_POST => 1,
						CURLOPT_POSTFIELDS => $Request,
					]);

					$result = json_encode( simplexml_load_string(curl_exec($ch)) );

					$resolved = json_decode($result);

					var_dump($resolved);

					if( curl_errno($ch) ){

						Logger::Init();
							Logger::ELog("Couldn't connect to send sms, try again later. \n".curl_error($ch) );
						Logger::kill();

						return json_decode(['statusCode' => 306 ]);
					}else{
						return $resolved;
					}


			}catch(\Throwable $t){
				new ErrorTracer($t);
			}finally{
				curl_close($ch);
			}

		}
		
	} 

?>
