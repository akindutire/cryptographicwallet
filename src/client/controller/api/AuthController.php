<?php
namespace src\client\controller\api;

	use src\client\model\Authtoken;
    use src\client\model\ExtraUserInfo;
    use src\client\model\MailingList;
    use src\client\model\Membership_plan;
    use src\client\model\Settings;
    use src\client\model\User;
    use src\client\model\Wallet;
    use src\client\model\ActivityLog;

    use src\client\service\Crypto;
    use src\client\service\DashboardService;
    use src\client\service\UserLoginAuthSetUp;
    use zil\core\scrapper\Info;
    use \zil\core\server\Param;
	use \zil\core\server\Response;

	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

use zil\factory\Session;
use \zil\security\Validation;

 
	

	class AuthController{

		use Notifier, Navigator;

		
		public function __construct(){
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
            header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, X-Requested-With');
		}

		// Auth user
		public function Auth(Param $param){
			try{
				$username = strip_tags(trim($param->form()->username));
				$pwd = $param->form()->pwd;


				$Validation = new Validation(  ['username', 'required'],   ['pwd', 'required'] );
				if  (  $Validation->isPassed() ){

                    $AuthReckoner = new Authtoken();
                    $ExU = new ExtraUserInfo();

                    $LoginReq =
                        [
                            [
                                ['username', $username, 'OR'],
                                ['email', $username]
                            ],
                            ['password', md5($pwd)]
                        ];

                    $UsrTuple = $ExU->filter('email')->where( ...$LoginReq );

                    if($UsrTuple->count() != 1)
                        throw new \Exception("Invalid login credentials, please provide a valid login credentials");


                    $muser = $ExU->filter('email')->where( ...$LoginReq )->get()->email;


                    if($AuthReckoner->isExists( ['claim', $muser] ))
				        $token = $AuthReckoner->filter('token AS tok')->where( ['claim', $muser] )->get()->tok;
				    else
					    $token = (new UserLoginAuthSetUp())->setGuard($username, md5($pwd));



				    if (!is_null( $token ) ){


						/** @var Setup user for authorization $AuthReckoner */


                        $AuthReckoner->token = $token;
                        $AuthReckoner->expires_at = $AuthReckoner->getExpirationTimeStamp();
                        $AuthReckoner->claim = $muser;

                        if($AuthReckoner->isExists( ['token', $token] )){

                            $AuthReckoner->where( ['token', $token] )->update();
                        }else {
                            $AuthReckoner->create();
                        }

                        Info::$_dataLounge['API_CLIENT'] = ['TOKEN' => $token, 'PK' => $muser ];
                        Info::$_dataLounge['API_CLIENT_ACTIVE'] = true;


                        $data = [ 'msg' => ['token' => $token, 'email' => $muser ], 'success' => true ];

					}else{
                        
						$data = [ 'msg' =>  'Incorrect login credentials' , 'success' => false ];
					}

				}else{
					$data = [ 'msg' => implode("\n", $Validation->getError()), 'success' => false ];
				}
			}catch(\Throwable $t){
                
                $data = [ 'msg' => $t->getMessage(), 'success' => false ];
               

			}finally{
				echo Response::fromApi($data, 200);
			}

		}


		public function SignUp(Param $param){
		    try{

                $name = trim($param->form()->name);
                $username = htmlentities(strip_tags(trim($param->form()->username)));
                $email = trim($param->form()->email);
                $phone = trim($param->form()->phone);
                $password = $param->form()->password;
                $t = $param->form()->termsAgreed;

                $referral =  null;
                if( !empty($param->url()->referral) ){
                    /**
                     * Check if referal exist
                     */
                    $key = $param->url()->referral;

                    if ( (new ExtraUserInfo())->filter('name')->where( ['username', $key] )->count() == 1 ){
                        $referral = $key;
                    }else{
                        throw new \Exception("Error: Referer doesn't exists");
                    }

                    unset($key);
                }

                $Validation = new Validation(  ['name', 'required'], ['email', 'email|required'],   ['password', 'required'],  ['username', 'required'], ['phone', 'required'], ['termsAgreed', 'required']  );

                if  (  $Validation->isPassed() ){

                    unset($Validation);

                    $newUser = new ExtraUserInfo();

                    if( $newUser->filter('name')->where( ['username', $username, 'OR'], ['email', $email] )->count() == 0){

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

                                throw new \Exception("Error: Couldn't complete registration, please retry");


                            }else{

                                // Subscribe to mails
                                (new MailingList())->subscribe($email);

                                $UsrObj = ExtraUserInfo::filter('id', 'email')->where(['username', $username])->get();


                                    $userId = $UsrObj->id;
                                    $userMail = $UsrObj->email;

                                    $CryptoUser = (new User())->where(['email', $userMail])->get();

                                    if(is_null($CryptoUser->photo)) {
                                        $PCryptoUser = new User();
                                        $PCryptoUser->photo = 'zdx_avatar.png';
                                        $PCryptoUser->where( ['email', $userMail] )->update();
                                    }

                                    $MemberShipDetails = (new Membership_plan())->filter('level', 'cost', 'tag')->where(['id', $CryptoUser->membership_plan_id])->get();

                                    if (Wallet::filter('id')->where(['owned_by', $userId])->count() == 0) {

                                        $keyPair = (new Crypto())->generatePublicKeyPair();


                                        $NewWallet = new Wallet;

                                        $NewWallet->id = '';
                                        $NewWallet->owned_by = $userId;
                                        $NewWallet->isPrime = false;
                                        $NewWallet->public_key = $keyPair['publickey'];
                                        $NewWallet->private_key = $keyPair['privatekey'];
                                        $NewWallet->sign_publickey = $keyPair['sign_publickey'];;
                                        $NewWallet->sign_privatekey = $keyPair['sign_privatekey'];;
                                        $NewWallet->balance = 0.00 - $MemberShipDetails->cost;
                                        $NewWallet->credits = 0.00;
                                        $NewWallet->debits = $NewWallet->balance;
                                        $NewWallet->acc_no = 0;
                                        $NewWallet->acc_name = 0;
                                        $NewWallet->bank = '';


                                        if ($NewWallet->create()) {
                                            $WalletObj = (new Wallet())->where(['owned_by', $userId])->get();

                                        } else {
                                            throw new \Exception('Error: Couldn\'t create user wallet');
                                        }
                                        unset($NewWallet);

                                    } else {
                                        $WalletObj = (new Wallet())->where(['owned_by', $userId])->get();
                                    }

                                    $User = (new ExtraUserInfo())->where(['username', $username])->get();

                                    $data =  [
                                        'Wallet' => $WalletObj,
                                        'User' => $User,
                                        'ReferalLink' => (new ExtraUserInfo())->getReferalLink($User->username),

                                        'MoreUserDetails' => $CryptoUser,
                                        'MemberShipPlanDetails' => $MemberShipDetails,
                                        'ServiceCharge' => [
                                            'CashOut' => (new Settings())->getCashOutServiceCharge()
                                        ]
                                    ];
                            }



                        }else{
                            throw new \Exception("Error: Couldn't complete registration, please retry");
                        }
                    }else{
                        throw new \Exception("Error: Username or email already used");
                    }

                }else{
                    throw new \Exception("Validation Error, Some fields are empty\n".implode('\n', $Validation->getError()));
                }

            } catch (\Throwable $t){
                $data = [ 'msg' => $t->getMessage(), 'success' => false ];
            } finally {
                echo Response::fromApi($data, 200);
            }
        }

        public function Logout(Param $param){
		    try{
		        $token = $param->url()->token;

                if (  (new Authtoken())->destroyToken($token)  )
                    $data = [ 'msg' => 'You have successfully logged out', 'success' => true];
                else
                    throw new \Exception("Error: couldn't log out");

            } catch (\Throwable $t) {
                $data = [ 'msg' => $t->getMessage(), 'success' => false ];
            } finally {
                echo Response::fromApi($data, 200);
            }
        }
	}

?>
