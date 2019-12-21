<?php
namespace src\client\controller;

	use src\client\model\EmailValidationTokenLock;
use src\client\service\UserLoginAuthSetUp;
use \zil\core\server\Param;
	
	use \zil\core\scrapper\Info;
	
	use \zil\core\tracer\ErrorTracer;
	
	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

use zil\factory\Utility;
use \zil\security\Encryption;
	use \zil\security\Validation;
	
	use \zil\factory\Redirect;
	use \zil\factory\Session;
	use \zil\factory\View;
	

	use src\client\config\Config;
	
	
	use src\client\service\GuardUserLogin;
	use src\client\service\MailService;

	use src\client\model\ExtraUserInfo;
	use src\client\model\User;
	use src\client\model\PwdMutationLock;
	use src\client\model\Authtoken;
 	use src\client\model\MailingList;
   	use src\client\model\Settings;
   	use src\client\model\Wallet;
	use src\client\model\Transaction;
	   
	use src\client\model\Product;
	use src\client\model\Product_cat;
	
 	use Carbon\Carbon;

	class Home{

		use Notifier, Navigator;

			public function TermsAndCondition(Param $param){

				$OutputData = [];

				#render the desired interface inside the view folder

				View::render("Home/TermsAndCondition.php", $OutputData);
			}

		public function Pricing(Param $param){

			
			$OutputData = [
				
				'cats' => (new Product_cat())->getCategories(1)
			];

			#render the desired interface inside the view folder

			View::render("Home/Pricing.php", $OutputData);
		}


		public function ContactUs(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Home/ContactUs.php", $OutputData);
		}

		public function AboutUs(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Home/AboutUs.php", $OutputData);
		}

		public function Service(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Home/Service.php", $OutputData);
		}

		public function Faq(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Home/Faq.php", $OutputData);
		}

		
		public function ForgotPwd(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Home/ForgotPwd.php", $OutputData);
		}

		public function ChangePwd(Param $param){

			if( isset( $param->url()->request_auth_key ) ){
				
				$PwdLock = new PwdMutationLock();
				
				if(  $PwdLock->where( [ 'msg', $param->url()->request_auth_key ] )->count() == 1){

					$OutputData = [
						'RQAUTH' => $param->url()->request_auth_key
					];
		
					#render the desired interface inside the view folder
		
					View::render("Home/ChangePwd.php", $OutputData);

				}else{
					echo "Link has expired";
				}

				
			}else{
				
				$this->goTo('/login');
			}
			
		}

		public function signup(Param $param){

			$ref = NULL;



			if( isset($param->url()->referral) ){

				if( (new ExtraUserInfo())->isUsernameValid($param->url()->referral) ){
					$ref = $param->url()->referral;
				}else{
					$this->notification('Referral ID is invalid, you wont be registered via '.$param->url()->referral)->send("ERROR");
				}
			}


			$OutputData = [ 'ref' => $ref ];

			#render the desired interface inside the view folder

			View::render("Home/signup.php", $OutputData);
		}

		public function login(Param $param){

			$OutputData = [];


			#render the desired interface inside the view folder
			View::render("Home/login.php", $OutputData);
		}

	

		public function index(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Home/index.php", $OutputData);
		}

		public function logout(Param $param){


			(new UserLoginAuthSetUp())->destroyGuard() ;
			
		}


		/**
		 * Form Direect Processor
		 * 
		 */
		public function VerifyEmail(Param $param) {

			$email = $param->url()->email;
			$verification_key = $param->url()->verification_key;

			$EVTL = new EmailValidationTokenLock();

			/**
			 * Run Garbage collector before verification
			 */
			$EVTL->EmailMutationGC();

			if(  $EVTL->where( [ 'token', $verification_key ], ['email', $email] )->count() == 1){

				$U = new User();

				$U->isEmailVerified = true;

				if( $U->where( ['email', $email] )->update() == 1){
					$EVTL->where( [ 'token', $verification_key ], ['email', $email] )->delete();
					echo "Email successfully verified";
				}else{
					echo "An Error Occurred, email not verified, please retry!!";
				}

			}else{
				echo "Link has expired";
			}

		}

		public function actionRegisterUser(Param $param){
			try{	
				$name = trim($param->form()->name);
				$username = htmlentities(strip_tags(trim($param->form()->username)));
				$email = trim($param->form()->email);
				$phone = trim($param->form()->phone);
				$password = $param->form()->password;
				$termsAgreed = $param->form()->termsAgreed;
				
				$referral = null;
				if( !empty($param->form()->referral) ){
					/**
					 * Check if referal exist
					 */
					$key = $param->form()->referral;
					
					if ( (new ExtraUserInfo())->filter('name')->where( ['username', $key] )->count() == 1 ){
						$referral = $key;
					}else{
						$this->notification("Error: Referer doesn't exists")->send("ERROR");
						$this->goBack();
						return;
					}

					unset($key);
				}
				
				$Validation = new Validation(  ['name', 'required'], ['email', 'email|required'],   ['password', 'required'],  ['username', 'required'], ['phone', 'required'], ['termsAgreed', 'required']  );

				if  (  $Validation->isPassed() ){

					unset($Validation);

					$newUser = new ExtraUserInfo();

					if( $newUser->filter('name')->where( ['username', $username, 'OR'], ['email', $email, 'OR'], ['phone', $phone] )->count() == 0){

						$newUser->id = '';
						$newUser->name = ucwords($name);
						$newUser->username = $username;
						$newUser->password = md5($password);
						$newUser->email = $email;
						$newUser->phone = $phone;

						if( $newUser->create() ){
							
							/**Crypto user */
							$CryptoUser = new User();
							
							$CryptoUser->id = '';
							$CryptoUser->user_type = $CryptoUser->defaultUserType();
							$CryptoUser->isAdmin = 0;
							$CryptoUser->password = '';
							$CryptoUser->email = $email;
							$CryptoUser->mobile = $phone;
							$CryptoUser->referer = $referral;
							$CryptoUser->membership_plan_id = 1;
							$CryptoUser->previous_plans = "1;";
							$CryptoUser->hidden = 0;
							$CryptoUser->suspended = 0;
							$CryptoUser->created_at = time();
							$CryptoUser->trans_lock = 0;
							
							if( !$CryptoUser->create() ){
								$lastUser = $newUser->lastInsert();
								$newUser->where( ['id', $lastUser] )->delete();
								
								$this->notification("Error: Couldn't complete registration, please retry")->send("ERROR");
								$this->goBack();
								return;
							}else{
								
								$Wallet = new Wallet;

								// Subscribe to mails
								(new MailingList())->subscribe($email);

								
									$token = (new UserLoginAuthSetUp())->setGuard($username, md5($password) );

								if (!is_null( $token ) ){
			
			
									/** @var Setup user for authorization $AuthReckoner */
			
		
									$AuthReckoner = new Authtoken();
									$AuthReckoner->token = $token;
									$AuthReckoner->expires_at = $AuthReckoner->getExpirationTimeStamp();
									$AuthReckoner->claim = $email;
			
									$AuthReckoner->create();
			
									Info::$_dataLounge['API_CLIENT'] = ['TOKEN' => $token, 'PK' => $email ];
									Info::$_dataLounge['API_CLIENT_ACTIVE'] = true;
			
									$this->goTo("activate/token/as/session/app/cert/{$email}/{$token}");
			
								}else{
			
									$LOGIN_LINK = Utility::route('login');
									$this->notification("Success: Registration complete, proceed to <a href='{$LOGIN_LINK}'>login</a> to your account")->send("SUCCESS");
									$this->goBack();
									return;
								}
								
							}

						}else{
							$this->notification("Error: Couldn't complete registration, please retry")->send("ERROR");
							$this->goBack();
							return;
						}
					}else{
						$this->notification("Error: Username,email or phone already used")->send("ERROR");
						$this->goBack();
					}
					
				}else{
					$this->goBack();
				}
			}catch(\Throwable $t){
				new ErrorTracer($t);
			} finally {}
		}


		public function ActivateAppCertAsToken(Param $param){
            try {

                if( isset($param->url()->token) && isset($param->url()->email) ){

                    $A = new Authtoken;

                    if ( $A->isValid($param->url()->token) ) {

						if( (new ExtraUserInfo())->filter('username AS u')->where( ['email', $param->url()->email ] )->count() == 0 ){
							$this->goTo('login');
							return;
						}

						$username = (new ExtraUserInfo())->filter('username AS u')->where( ['email', $param->url()->email ] )->get()->u;


                        (new Session())
                            ->build('AUTH_CERT', $param->url()->token, true)
                            ->build('email', $param->url()->email)
							->build('username', $username)
							->build('Last_Visit', time());


                        $this->goTo('dashboard');

                    }else{
                        $this->goBack();
                    }

                }else{
                    $this->goBack();
                }

            } catch (\Throwable $t){
                new ErrorTracer($t);
            }

		}

		public function actionForgotP(Param $param){
			
			$V = new Validation( ['email', 'email|required'] );
			
			if($V->isPassed()){

				$d = (new ExtraUserInfo())->filter('id')->where( ['email', $param->form()->email] )->count();

				if($d == 0 ){
					$this->notification("Email is not recognised")->clear()->send("ERROR");
					$this->goBack();
					return;
				}

				
				$PwdLock = new PwdMutationLock();
				
				$PwdLock->where( ['email', $param->form()->email] )->delete();

				$msg = (new Encryption())->authKey();
			
				$PwdLock->email = $param->form()->email;
				$PwdLock->msg = $msg;
				$PwdLock->created_at = Carbon::now();
				$PwdLock->create();


				// Mail to user

				$link = $_SERVER['HTTP_HOST']."/account/change/password/{$msg}";
				$msg01 = "<p style='text-align: center;'> Use below link to change your password</><p style='text-align: center;'> <a href='$link'>{$link}</a></p>";

				(new MailService())->sendChangePwdRequest($param->form()->email, $msg01);
				

				$this->notification("Request granted, check your email")->send('SUCCESS');
				
			}
			
			$this->goBack();
			return;
		}

		public function actionChangeP(Param $param){
			
			$V = new Validation( ['password', 'text|required'], ['RQAUTH', 'text|required'] );
			
			if($V->isPassed()){
				
				$PwdLock = new PwdMutationLock();

				if($PwdLock->filter('email')->where( ['msg', $param->form()->RQAUTH] )->count() == 0){
					$this->notification("Something went wrong, request a new password")->clear()->send("ERROR");
					$this->goBack();
					return;
				}
				
				$email = $PwdLock->filter('email')->where( ['msg', $param->form()->RQAUTH] )->get()->email;

				if( $email !== null ){
					$npass = md5($param->form()->password);

					$Ex = new ExtraUserInfo;
					$Ex->password = $npass;
					$Ex->where( ['email', $email] )->update();

					$PwdLock->where( ['email', $email] )->delete();

					$this->goTo('/login');
					
				}else{
					$this->notification("Something went wrong, request a new password")->clear()->send("ERROR");
					$this->goBack();
				}

			}else{
				$this->goBack();
			}
			
			

		}

		public function actionContactUs(Param $param){
			try{

				$V = new Validation( ['name', 'text|required'], ['phone', 'text|minlength:11|required'], ['email', 'email|required'], [ 'message', 'text|required'] );
				
				if($V->isPassed()){


					// Mail to user
					$m = strip_tags($param->form()->message);
					$p = strip_tags($param->form()->phone);

					$msg01 = "<p style=''> {$m} <br> Contact: {$p}</p>";

					if( (new MailService())->sendSupportMail($param->form()->email, $msg01, $param->form()->name) ){
						$this->notification("Thank you, We will get back to you shortly.")->send('SUCCESS');
					}else{
						$this->notification("Thank you, Mail not sent.")->send('ERROR');
					}
					

					
				}else{
					$this->notification($V->getErrorString())->send('ERROR');	
				}
				
				$this->goBack();
			}catch (\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function __construct(){
			
			//Pwd Request Gabage Collector
			(new PwdMutationLock())->PwdMutationGC();
		}

		
				
	}

?>
