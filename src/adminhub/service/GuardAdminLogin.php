<?php
namespace src\adminhub\service;

	use zil\core\interfaces\Param;
    use zil\core\server\Response;
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
	use \zil\security\Trial;


	use src\adminhub\config\Config;
	
	use \zil\core\interfaces\Guard;

	use src\adminhub\model\User;
	use src\naijasubweb\model\ExtraUserInfo;

	class GuardAdminLogin{

		public function __construct(){
			date_default_timezone_set('Africa/Lagos');
		 }
		
		public function setGuard(string $email, string $pwd) : ?string {
			
			$hashPwd = (new ExtraUserInfo())->filter('password')->where( ['email', $email] )->get()->password;
			$authToken = null;


			if( !is_null($hashPwd) && (new Encryption())->hashVerify($pwd, $hashPwd) ){

			    $fetched = User::filter('id')->where( ['email', $email], ['suspended', false], ['hidden', false] )->count();

			    if($fetched == 1 ) {
                    Session::build('AUTH_CERT', (new Encryption())->authKey(), true);
                    $authToken = Session::getEncoded('AUTH_CERT');
                }

			}
			
			return !is_null($authToken) ? $authToken : null;
				
		}

		public function destroyGuard(){
            Authentication::Destroy();
            Session::delete('email');
            new Redirect('login');
        }



	} 

?>
