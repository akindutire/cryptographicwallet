<?php
namespace src\client\service;

	use src\client\model\ExtraUserInfo;
    use zil\core\interfaces\Param;
    use zil\core\scrapper\Info;
    use zil\core\server\Response;

	use zil\factory\Logger;
	use \zil\factory\Session;
	use \zil\factory\Redirect;
	
	use \zil\security\Authentication;
	use \zil\security\Encryption;


	class UserLoginAuthSetUp{

		public function __construct(){
			date_default_timezone_set('Africa/Lagos');
		 }
		
		public function setGuard(string $username, string $password) : ?string {

			$fetched = (new ExtraUserInfo())->as('ex')->with('User as u', 'ex.email = u.email')
                ->filter('ex.id')
                ->where(
                    [
						['ex.username', $username, 'OR'],
						['ex.email', $username ]
					],
                    ['ex.password', $password],
                    ['u.suspended', 0]
                    )->count();

                if($fetched == 1 ) {

                    $token = (new Encryption())->authKey();

                    if(Info::getRouteType() != 'api')
                        Session::build('AUTH_CERT', $token, true);

                    return $token;

                }else{
                    return null;
                }


		}

		public function destroyGuard(){

			Authentication::Destroy();
			Session::delete('username')->delete('email');
			new Redirect('login');

		}

	} 

?>
