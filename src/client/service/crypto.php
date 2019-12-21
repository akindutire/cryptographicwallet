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
	

	use src\client\config\Config;
	 
	use \zil\core\tracer\ErrorTracer;
	
	class Crypto{

		public function __construct(){ }
		
		public function generatePublicKeyPair(){
			try{

				$box_kp = sodium_crypto_box_keypair();
				$sign_kp = sodium_crypto_sign_keypair();

					// Split the key for the crypto_box API for ease of use
					$box_secretkey = sodium_crypto_box_secretkey($box_kp);
					$box_publickey = sodium_crypto_box_publickey($box_kp);
					
					// Split the key for the crypto_sign API for ease of use
					$sign_secretkey = sodium_crypto_sign_secretkey($sign_kp);
					$sign_publickey = sodium_crypto_sign_publickey($sign_kp);
					
					$key = [  'publickey' => $box_publickey, 'privatekey' => $box_secretkey, 'sign_publickey'=> $sign_publickey, 'sign_privatekey' => $sign_secretkey ];

					return array_map( function($val){
						return sodium_bin2hex($val);
					}, $key);
					
			}catch(\BadMethodCallException $t){
				new ErrorTracer($t);
			}
		}
	} 

?>
