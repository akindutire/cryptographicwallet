<?php
namespace src\naijasubweb\controller\api;

	use \zil\core\server\Param;
	
	use \zil\factory\View;
	use \zil\factory\Session;
	use \zil\factory\Mailer;

	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

	use \zil\security\Validation;
	use \zil\security\Encryption;

	use \zil\core\server\Response;

	use src\naijasubweb\config\Config;
	use src\naijasubweb\service\DashboardService;
	use src\naijasubweb\service\MailService;

	use src\naijasubweb\model\User;
	use src\naijasubweb\model\ExtraUserInfo;
	use src\naijasubweb\model\Wallet;
	use src\naijasubweb\model\PwdMutationLock;
	use src\naijasubweb\model\Membership_plan;
	use src\naijasubweb\model\Transaction;

	
	use Carbon\Carbon;
 	use src\naijasubweb\model\MailingList;
 
 	
	class MobileUserController{

	
		public function __construct(){
			header('Access-Control-Allow-Origin: *');
			header('Content-Type: application/json');
			header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
			header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
		}

        private function isTokenValid(string $token) : bool{
            if( $token != Session::get('APP_CERT') )
                return false;
            else
                return true;
        }

		// Auth user
		public function Auth(Param $param){
			try{
				$username = strip_tags(trim($param->form()->username));
				$pwd = $param->form()->pwd;
				
				$Validation = new Validation(  ['username', 'required'],   ['pwd', 'required'] );
				if  (  $Validation->isPassed() ){

					$token = (new GuardUserLogin())->setGuard($username, md5($pwd));
					if (!is_null( $token ) ){
						
						$muser = (new ExtraUserInfo())->where( ['username', $username] )->filter('email')->get();
						Session::build('username', $username)->build('email', $muser->email);

						$data = [ 'msg' => ['token' => $token ], 'success' => true ];
					}else{
						
						$data = [ 'msg' => ['error' => 'Incorrect login credentials' ], 'success' => false ];
					}

				}else{
					$data = [ 'msg' => $Validation->getError(), 'success' => false ];
				}
			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, 200);
			}
			
		}

		// Wallet details, including key,  balance...
		public function WalletDetails(Param $param){
			
			try{
                
                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }

				$public_key = $param->url()->wallet_key;

              

				$details =  (new Wallet())->getWalletDetails($public_key);
				$details = (array)$details;

				$details = array_map( function($entry){
					
					if( !mb_detect_encoding($entry, 'ASCII', true) )
						return sodium_bin2hex($entry);
					else
						return $entry;

				} , $details );

				$data = [ 'msg' => $details , 'success' => true ];

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage() , 'success' => false ];
			}finally{
				echo Response::fromApi($data, $status);
			}
			
		}

		// Wallet balance
		public function WalletBalance(Param $param){

			try{
                
                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }

				$public_key = $param->url()->wallet_key;

				$data = [ 'msg' => ['balance' => (new Wallet())->getBalance($public_key) ], 'success' => true ];
				
			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, $status);
			}
		}

		public function isTransactionLocked(Param $param){
			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }

				if( User::filter('trans_lock')->where( ['email', Session::get('email')], ['trans_lock', 1])->count() == 1){
					$data = [ 'msg' => ['state' => true], 'success' => true ];
				}else{
					$data = [ 'msg' => ['state' => false], 'success' => true ];
				}

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, $status);
			}
		}

		public function PreviousPlans(Param $param){
			
			try{
				
				$status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
				}
				
				$data = [ 'msg' => (new User())->getPreviousPlans() , 'success' => true ];

			}catch(\Throwable $t){

				$data = [ 'msg' => $t->getMessage(), 'success' => false ];	
			}finally{
				echo Response::fromApi($data, 200);
			}
		}


		// Account details
		public function Passport(Param $param){

			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$email = strip_tags($param->url()->email);

				$account = (new User())->getAccountDetails($email);
			
				$data = [ 'msg' => array_merge( (array)$account[0], (array)$account[1]), 'success' => true ];
			
			}catch(\Throwable $t){

				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			
			}finally{
				echo Response::fromApi($data, $status);
			}
		}

		public function PassportViaWallet(Param $param){
			
			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }

				// raw public key
		
				$public_key = strip_tags($param->url()->wallet_key);

				if(Wallet::filter('owned_by')->where( ['public_key', $public_key] )->count() == 1){

					$user_id = Wallet::filter('owned_by')->where( ['public_key', $public_key] )->get()->owned_by;
					$email = ExtraUserInfo::filter('email')->where( ['id', $user_id] )->get()->email;

					$account = (new User())->getAccountDetails($email);
					$account = (array)$account;
					
					$data = [ 'msg' => array_merge( (array)$account[0], (array)$account[1]) , 'success' => true ];

				}else{
					$data = [ 'msg' => [] , 'success' => false ];
				}

			}catch(\Throwable $t){

				$data = [ 'msg' => $t->getMessage(), 'success' => false ];	
			}finally{
				echo Response::fromApi($data, $status);
			}
		}

		public function Subscribe(Param $param){
			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }

				$email = strip_tags($param->url()->email);

				$data = [ 'msg' =>  (new MailingList())->subscribe($email) , 'success' => true ];
				
			}catch(\Throwable $t){

				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			
			}finally{
				echo Response::fromApi($data, $status);
			}
		}

		public function Unsubscribe(Param $param){
			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$email = strip_tags($param->url()->email);

				$data = [ 'msg' =>  (new MailingList())->unsubscribe($email) , 'success' => true ];
				
			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, $status);
			}
		}

		// Mutate profile pic.
		public function ChangeProfilePic(Param $param){
			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$response = (new DashboardService())->uploadProfilePic($_FILES['file']);

				if($response['status'] == true){
					$data = [ 'photosource' => $response['picsrc'],'success' => true ];
					
					$user = new User();
					if (! $user->updateProfilePic($response['uploadPath'], $response['photoname']) ){
						$data = [ 'msg' => "Couldn't complete file upload, retry", 'success' => false ];
					}

				}else{
					$data = [ 'msg' => $response['error'], 'success' => false ];
				}

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, $status);
			}
				
			
		
		}

		// Mutate profile details
		public function ChangeProfileDetails(Param $param){
			try{
                
                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$email = trim($param->form()->email);
				$name = trim($param->form()->name);
				$phone = trim($param->form()->phone);
				$acc_no = trim($param->form()->acc_no);

				
				if ( User::filter('id')->where( ['email', Session::get('email')], ['trans_lock', 0] )->count() == 1){

					$Validation = new Validation(  ['email', 'email|required'],   ['name', 'required'], ['phone', 'required|minlength:11'] );

					if  (  $Validation->isPassed() ){

						$CryptoUser=new User();
						$CryptoUser->email = $email;
						$CryptoUser->mobile = $phone;
						$CryptoUser->where( ['email', Session::get('email')] )->update();
						

						$MainUser = new ExtraUserInfo();
						
						$Usr=$MainUser->filter('id')->where( ['email', Session::get('email')] )->get();

						$MainUser->email = $email;
						$MainUser->phone = $phone;
						$MainUser->name = $name;
						$MainUser->where( ['email', Session::get('email')] )->update();
						
						
						$Wallet = new Wallet();
						$Wallet->acc_no = $acc_no;
						$Wallet->where( ['owned_by', $Usr->id] )->update();

						unset($CryptoUser, $MainUser, $Wallet, $Usr);

						Session::build('email', $email);
						$data = [ 'msg' => "Details updated", 'success' => true ];
						
					}else{
						
						$data = [ 'msg' => $Validation->getError(), 'success' => false ];
					}

				}else{
					$data = [ 'msg' => "Couldn't edit profile, you have pending transactions", 'success' => false ];
				}

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, $status);
			}

		}

		// Request password Mutation
		public function RequestPwdChange(Param $param){

			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$password = md5($param->form()->pwd);
				
				if ( ExtraUserInfo::filter('id')->where( ['email', Session::get('email') ], ['password', $password] )->count() == 1){

					$msg = (new Encryption())->authKey();
				
					$PwdLock = new PwdMutationLock();
					
					$PwdLock->where( ['username', Session::get('username')] )->delete();

					$PwdLock->username = Session::get('username');
					$PwdLock->msg = $msg;
					$PwdLock->created_at = Carbon::now();
					$PwdLock->create();


					// Mail to user

					$segments = (new MailService())->mailBodySegments();
					$mailAccounts = (new MailService())->mailAccounts();

					$link = $_SERVER['HTTP_HOST']."/account/change/password/{$msg}";
					$segments['body'] = "<p style='text-align: center;'> Follow the <a href='$link'>link</a> to change your password</p>";

					$Mailer	= new Mailer('PHP_MAILER');

					$Mailer->sendMail('NaijaSub Team', $mailAccounts['INFO'] , [Session::get('email')], 'Password Change Request', implode("\n", $segments) );

					$data = [ 'msg' => "Request granted, check your email", 'success' => true ];
		
				}else{
					$data = [ 'msg' => "Request cancelled, Password incorrect", 'success' => false ];
				}

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, $status);
			}
		
		}
		
		// Upgrade account
		public function UpgradeAccount(Param $param){

			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$plan_level =  intval($param->url()->plan_level);

				if( Membership_plan::filter('id')->where( ['level', $plan_level] )->count() == 1 ){
					
					$data = [ 'msg' => ['error' => "Application Error: Unknown level selected"], 'success' => false ];

					return;
				}

				$Mbp = Membership_plan::filter('id, cost')->where( ['level', $plan_level] )->get();
				
				// Check User wallet
				$Wallet = new Wallet;

				if( !$Wallet->isSufficientBalance( doubleval($Mbp->cost) ) ){ 
					
					$data = [ 'msg' => ['error' => "Insufficient fund in wallet, please top up wallet"], 'success' => false ];
					
					return;
				}
						
				$Transaction = new Transaction;
				$payment_meta = ['type' => $Transaction->getTransactionTypes('ACCOUNT_UPGRADE'), 'to_address' => $Wallet->getAnyAdminPublickey() ];

				// Pay membership due
				if( $Wallet->pay(doubleval($Mbp->cost), $payment_meta ) ){
					
					(new User())->setNewMemberShipLevel($Mbp->id);
					$data = [ 'msg' =>  "Congratulations, Account Upgraded", 'success' => true ];

				}else{
					$data = [ 'msg' => ['error' => "Couldn't complete membership payment"], 'success' => true ];
				}
					
				unset($Wallet, $Mbp);

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, $status);
			}
		
		}

		public function UseAccount(Param $param){
			
			try{

				$plan_level =  intval($param->url()->plan_level);

				if( Membership_plan::filter('id')->where( ['level', $plan_level] )->count() == 0 ){
					
					$data = [ 'msg' => ['error' => "Application Error: Unknown level selected"], 'success' => false ];

					return;
				}

				$prev = (new User())->filter('previous_plans')->where( ['email', Session::get('email') ] )->previous_plans;
				if( $prev === NULL || $prev == 'NULL'){
					throw new \Exception("This plan has never been subscribed to in the past");
				}else{
					$prev = rtrim($prev, ';');
					if( !in_array($plan_level, explode(';', $prev) ) ){
						throw new \Exception("This plan has never been subscribed to in the past");
					}
				}

				$Mbp = Membership_plan::filter('id, cost')->where( ['level', $plan_level] )->get();
				(new User())->setNewMemberShipLevel($Mbp->id, true);
				$data = [ 'msg' =>  "Congratulations, Account Switched", 'success' => true ];

			}catch(\Throwable $t){
				$data = [ 'msg' => ['error' => $t->getMessage() ], 'success' => false ];
			}finally{
				echo Response::fromApi($data, 200);
			}
		}

		
		// Transfer fund between wallet
		public function TransferFund(Param $param){

			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }

				
				$wallet_key = $param->url()->wallet_key;

				$destination_address = $param->form()->des_address;
				$amount = doubleval($param->form()->amount);
				
				if( $wallet_key == $destination_address ){
					$data =  [ 'msg' => [ 'error' => "Forbidden: Can't transfer fund to your self"], 'success' => false ];
					return;
				}

				$Wallet = new Wallet;
				$User = new User;

				if( !$Wallet->isValid( $wallet_key ) ){
					$data = [ 'msg' => [ 'error' => "Invalid wallet, please ensure you are using your wallet key"], 'success' => false ];
					
					return;
				}
				
				if( $User->isTransactionLocked() ){
					$data = [ 'msg' => [ 'error' => "Sorry, you still have pending transaction"], 'success' => false ];
					
					return;
				}

				$Validation = new Validation(  ['des_address', 'required'], ['amount', 'number|min:0'] );

				if( $Validation->isPassed() && !empty($wallet_key)){

					if($amount < 0){

						$data = [ 'msg' => ['error' => "Amount must be more than 0.00" ] , 'success' => false ];
						return;
					}
				
					if( !$Wallet->isSufficientBalance( $amount ) ){
						$data = [ 'msg' => [ 'error' => "Insufficient fund in wallet, please top up wallet"], 'success' => false ];
						return;
					}
					
					if( $Wallet->transfer( $amount, $destination_address) ){

						$data = [ 'msg' =>  "Fund transfered, wait for receiver verification", 'success' => true ];
					}else{
						
						$data = [ 'msg' => ['error' => "Error: Couldn't complete membership payment"], 'success' => false ];
					}

					unset($Transaction, $Wallet);
				
				}else{
					$data = [ 'msg' => $Validation->getError(), 'success' => false ];
				}

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, $status);
			}

		}
			
		// Refs
		public function Referrals(Param $param){
			try{
				
				$data = [ 'msg' =>  [ 'referral' => (new User())->getReferrals(), 'reward' => (new User())->getTotalReferralReward() ] , 'success' => true ];
				
			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, 200);
			}
		}

	}

?>