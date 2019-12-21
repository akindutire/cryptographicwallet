<?php
namespace src\client\service;

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

	use \zil\core\tracer\ErrorTracer;


	use src\client\config\Config;
	
	class CurrencyConverter{

		private $CurrencyLayerAPIKey = '7b5fe4470d226d60adc8bb548413e904';

		public function __construct(){
			// require_once('dependency/currencylayer/currency.class.php');
		}

		public function getConversionRate() : float {
			try{

				// return 1.00;

				$currencyLayerLiveEndPoint = "https://apilayer.net/api/live?access_key={$this->CurrencyLayerAPIKey}&source=BTC&currencies=USD&format=1";
				$curlStart = curl_init();

				curl_setopt_array($curlStart, [
					CURLOPT_URL => $currencyLayerLiveEndPoint,
					CURLOPT_RETURNTRANSFER => 1,
				]);

				$result = json_decode(curl_exec($curlStart));

				if($result->success == true){
					return floatval($result->quotes->BTCUSD);
				}else{
					throw new \Exception("Naijasub is currently out of service from currency layer api");
				}




				// // $currencyLayerNS = new \currencyLayer();
				// // $currencyLayerNS->setEndPoint('live'); 

				// // $currencyLayerNS->setParam('currencies','NGN');

				// // //get the response from the api
				// // $currencyLayerNS->getResponse();


				// // $usdngn = $currencyLayerNS->response->quotes->USDNGN;
				
				// if( is_numeric( $usdngn ) )
				// 	return $usdngn;
				// else {

                //     if (!empty($currencyLayerNS->error->code)) {
                //         //handle the error
                //         throw new \Exception($currencyLayerNS->error->text);
                //     }else{
                //         throw new \RangeException("Unexpected value from conversion rate, ".gettype($usdngn)." returned");
                //     }
                // }

			}catch(\Throwable $t){
				new ErrorTracer($t);
			} finally {
				
			}
		}

		
	} 

?>
