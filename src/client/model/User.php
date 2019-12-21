<?php

namespace src\client\model;

use src\client\config\Config;

use zil\core\scrapper\Info;

use zil\core\tracer\ErrorTracer;

use \zil\factory\Model;

use \zil\factory\Session;

use \zil\factory\Logger;

class User {

	use Model;

	public $id = null;
	public $user_type = null;
	public $isAdmin = null;
	public $password = null;
	public $email = null;
	public $mobile = null;
	public $referer = null;
	public $membership_plan_id = null;
	public $previous_plans = null;
	public $hidden = null;
	public $suspended = null;
	public $photo = null;
	public $created_at = null;
	public $trans_lock = null;
	public $read_receipt = null;
	public $gender = null;
	public $isVerifiedAccount = null;
	public $isEmailVerified = null;
	public $KYC_FULLNAME = null;
	public $KYC_MOBILE = null;
	public $KYC_DOB = null;


	public static $table = 'User';

		public function __construct()
		{
			self::$key = 'id';
		}

		public function isKYCKnown() : bool {
			try{
				if ( $this->filter('email')->where( ['isVerifiedAccount', true], $this->baseCondition() )->count() == 1)
					return true;

				return false;

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}
		}

		public function isEmailVerified() : bool {
			try{

				if ( $this->filter('email')->where( ['isEmailVerified', true], $this->baseCondition() )->count() == 1)
					return true;

				return false;

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}
		}

		private function baseCondition() : array {
			try{
			    
			    $userId = (new ExtraUserInfo())->getUserId();

                $email = ExtraUserInfo::filter('email')->where(['id', $userId])->get()->email;

				return ['email', $email];

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}
		}

		public function updateProfilePic(string $uploadedFileLocation, string $photoNameToBeUpdated){
			try{

				$row = $this->filter('photo')->where( $this->baseCondition() )->get();
				$prev_pic = $row->photo;

				
				$this->photo = $photoNameToBeUpdated;
				if(  $this->where( $this->baseCondition() )->update() != 1){
					chmod($uploadedFileLocation, 0777);
					unlink($uploadedFileLocation);
					return false;
				}else{

					$path = (new Config())->getUploadPath();

					if( is_file($path.'/'.$prev_pic) && !is_null($prev_pic)  && ( (new Config())->defaultProfilePix() !== $prev_pic) ){
						chmod($path.'/'.$prev_pic, 0777);
						unlink($path.'/'.$prev_pic);
						unset($path, $prev_pic);
					}
					return true;
				}
			}catch(\Throwable $t){
			
				echo $t->getMessage();
			}
		}


		public function setNewMemberShipLevel(int $Plan_id, bool $reuse = null){

			try {

				if(is_null($reuse))
					$reuse = false;

				// Log to previous plan
				$prev_plans = (new self())->filter('previous_plans')->where($this->baseCondition())->get()->previous_plans;
				if ($reuse === false)
					$this->previous_plans = $prev_plans . "{$Plan_id};";
				$this->membership_plan_id = $Plan_id;
				$this->where( $this->baseCondition() )->update();

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}

		}

		public function getUserTransactionReward() : array {

			try {

				// Get member group as an integer, possibly a starter, dealer or reseller
				$mem_id = $this->filter('membership_plan_id')->where($this->baseCondition())->get()->membership_plan_id;// Get reward box according to member group
				$rewards = (new Membership_plan())->getMemberShipRewardRates($mem_id);
				return $rewards;

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}
		}

		public function getUserAirtimePurchaseReward():float{

			try {

				// Get member group as an integer, possibly a starter, dealer or reseller

				$mem_id = $this->filter('membership_plan_id')->where($this->baseCondition())->get()->membership_plan_id;

				// Get reward box according to member group
				$rewards = (new Membership_plan())->getMemberShipRewardRates($mem_id);

				return floatval($rewards['DISCOUNT_RATE']['AIRTIME_PURCHASE']);

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}
		}

		public function getAccountDetails(string $email){

			try {

				$id = ( new ExtraUserInfo())->filter('id')->where(['email', $email])->get()->id;
				return [

					(new self())->all()->where(['email', $email])->get(),
					(new ExtraUserInfo())->filter('name', 'username')->where(['email', $email])->get(),
					(new Wallet())->all()->where(['owned_by', $id], ['isPrime', 0])->get()
				];
			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}

		}
		
		public function getReferrals(){
			try {

				if( Info::$_dataLounge['API_CLIENT_ACTIVE'] )
					$email = Info::$_dataLounge['API_CLIENT']['PK'];
				else
					$email = Session::get('email');

				$username = (new self())->with('ExtraUserInfo as ex', 'User.email = ex.email')->where( ['User.email', $email] )->filter('ex.username AS u')->get()->u;

				return (new self())->with('ExtraUserInfo as ex', 'User.email = ex.email')->with('Wallet as wl', 'wl.owned_by = ex.id')->filter('User.id', 'wl.public_key', 'ex.name', 'User.email', 'User.mobile', 'User.photo')->where(['referer', $username])->get('VERBOSE');

			} catch (\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getTotalReferralReward(): float{

			try {

				$public_key = (new Wallet())->getPublickey();
				$type = (new Transaction())->getTransactionTypes('REFERRAL_BONUS');
				$amt = (new Transaction())->filter('SUM(amt_exchanged) as TotalRefFund')->where(['ito', $public_key], ['type', $type])->get()->TotalRefFund;

				return floatval($amt);

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}

		}

		public function getPreviousPlans():array{
			try {
				$prev = (new User())->filter('previous_plans')->where( $this->baseCondition() )->get()->previous_plans;
				if ($prev === NULL || $prev == 'NULL') {
					return [];
				} else {
					$prev = rtrim($prev, ';');

					return explode(';', $prev);

				}
			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}

		}

		public function isTransactionLocked():bool{

			try {
				if (self::filter('trans_lock')->where( $this->baseCondition(), ['trans_lock', 1])->count() == 1)
					return true;
				else
					return false;
			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}
		}

		public function defaultUserType() : string{
            return 'MEMBER';
        }
	}


?>
