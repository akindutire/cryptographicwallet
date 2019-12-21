<?php
namespace src\client\service;

	use src\client\model\Affiliate;
use src\client\model\DataCardCustomer;
use src\client\model\Notification;
    use zil\core\tracer\ErrorTracer;
    use \zil\factory\Session;
    use \zil\factory\Fileuploader;
    use \zil\factory\Logger;

    use \zil\factory\Database;
	use \zil\factory\BuildQuery;


	use src\client\config\Config;

	use src\client\model\ExtraUserInfo;
	use src\client\model\User;
	use src\client\model\Wallet;
	use src\client\model\Membership_plan;
	use src\client\model\Settings;
    use src\client\model\Transaction;

class DashboardService{

		public function __construct(){ }

		public function isRecognizedAsDataCardCustomer() : bool {
		    try{

		        return (new DataCardCustomer())->isCustomerRecognized();

            }   catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

        public function isADataCardReseller() : bool {
		    try{

		        return (new Affiliate())->isAnAffiliate();

            } catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

        public function minReqForDataCardArena() : float {
		    try{

		        return floatval((new Settings())->getMinimumBalanceRequirementDataCardReseller());

            } catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

		public function numberOfUnreadNotif() : int {
		    try{
		        return (new Notification())->numberOfUnreadNotif();
            } catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

        public function isAccountKYCValidated()  : bool {
		    try{

		        return (new User())->isKYCKnown();

            } catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

        public function isEmailValidated()  : bool {
            try{

                return (new User())->isEmailVerified();

            } catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function numberOfPendingIncomigTransaction() : int {
		    try{
                
                $n = (new Transaction())->getIncomingPendingTransactions();
                if(\is_array($n))
                    return sizeof($n);
                else
                    return 0;

            } catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

		public function confirmNotificationReadReceipt(int $notif_id){
		    try{
		        return (new Notification())->isNotifRead($notif_id);
            } catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

		public function getDashboardTemplateData(){

		    try {

               
                $UsrObj = ExtraUserInfo::filter('id', 'email')->where(['username', Session::get('username')])->get();
                if (isset($UsrObj->id)) {

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

                        $handle = (new Database())->connect();
                        $sql = new BuildQuery( $handle );

                        $rs = $sql->read('UserLookupForBalance', [ ['user_email',Session::get('email')] ], ['balance'] );
                        
                        if($rs->rowCount() > 0){
                            list($balance) = $rs->fetch();
                            $MemberShipDetails->cost = 0.00;
                        }else{$balance = 0.00; $MemberShipDetails->cost = 0.00;}

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

                        

                        $NewWallet->balance += $balance;   
                        $NewWallet->credits += $balance;
                            
                        

                        if ($NewWallet->create()) {
                            $WalletObj = (new Wallet())->where(['owned_by', $userId])->get();
                            
                            // $sql->delete('UserLookupForBalance', [ ['user_email, Session::get('email')] ] );

                        } else {
                            throw new \Exception('Error: Couldn\'t create user wallet');
                        }
                        unset($NewWallet);

                    } else {
                        $WalletObj = (new Wallet())->where(['owned_by', $userId])->get();
                    }

                    $User = (new ExtraUserInfo())->where(['username', Session::get('username')])->get();

                    return [
                        'Wallet' => $WalletObj,
                        'User' => $User,
                        'ReferalLink' => (new ExtraUserInfo())->getReferalLink($User->username),

                        'MoreUserDetails' => $CryptoUser,
                        'MemberShipPlanDetails' => $MemberShipDetails,
                        'ServiceCharge' => [
                            'CashOut' => (new Settings())->getCashOutServiceCharge()
                        ],
                        'AuthToken' => Session::getEncoded('AUTH_CERT')
                    ];
                } else {
                    (new GuardUserLogin())->destroyGuard();
                }
            }catch (\Throwable $t){
		        new ErrorTracer($t);
            }
		}

		public function uploadProfilePic(array $file){
			
			$name	=	$file['name'];


			$permitted_types = ['image/png','image/jpeg','image/jpg','image/gif'];
			$max_size = 20 * 1058816;

			$cfg = new Config();

			$name = time().preg_replace('/[\s]+/','',$name);
			
			$uploadPath = $cfg->getUploadPath().'/'.$name;

			$fileUploadHandler = (new Fileuploader())->upload([ 'file' => $file, 'size' => $max_size, 'type' => $permitted_types, 'destination' => $uploadPath, 'compress' => false ]);

			if( $fileUploadHandler->isUploaded()){
				
				$picsrc = $cfg->getUrlofUpload($name);

				return ['picsrc' => $picsrc, 'uploadPath' => $uploadPath, 'photoname' => $name, 'status' => true];
			}else{
				$err = $fileUploadHandler->getError();
				$fileUploadHandler->close();
				return ['error' => $err, 'status' => false];
			}
		}
	} 

?>
