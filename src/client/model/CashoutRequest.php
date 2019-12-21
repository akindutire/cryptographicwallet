<?php

namespace src\client\model;

use \zil\factory\Model;
use zil\core\tracer\ErrorTracer;
use src\client\controller\api\UserController;
use src\client\service\MailService;
use zil\security\Encryption;

class CashoutRequest {

	use Model;

	public $id = null;
	public $request_hash = null;
	public $receiver_address = null;
	public $amount = null;
	public $paid = null;
	public $created_at = null;


	public static $table = 'CashoutRequest';

		public function __construct()
		{
			self::$key = 'id';
		}

	public function request(float $amount): bool{
			
			if( !(new User())->isTransactionLocked() ) {
				if($amount > 0){
					
					$Wallet = new Wallet;
					
					if($Wallet->isSufficientBalance($amount)){

						$Rhash = (new Encryption())->generateShortHash();
						$this->request_hash = $Rhash;
						$this->receiver_address = sodium_bin2hex($Wallet->getPublickey());
						$this->paid = false;
						$this->amount = $amount;

					
						if( $this->create() ){
							$msg01 = "<p style='text-align: center;'><b>Request id:{$Rhash}</b></p><br><p style='text-align: center;'> Cash out of <b>NGN {$amount}</b> request granted, wait for confirmation</p><br><p style='color: red;'><b>Status: PENDING</b></p>";

							$email = (new UserController())->getUserEmail();

							(new MailService())->sendMail($email, "Cash out(#{$Rhash})", $msg01);

							return true;

						}else{
							return false;
						}
					}else{

						return false;
					}
					
				}else{
					return false;
				}		
			}else{
				return false;
			}
		}

		public function cancelReq(int $id) : bool {
			try {

				$receiver = $this->where( ['id', $id] )->filter('receiver_address AS ra', 'paid')->get();

				$ra = $receiver->ra;
				$paid = $receiver->paid;


				$pk = (new Wallet())->getPublickey();

				if($ra != sodium_bin2hex($pk) )
					throw new \Exception("CASHOUTREQ_ERR : Inconsistent bearer address");


				if($paid != 0)
					return false;

				if( $this->where(['id', $id])->delete() == 1)
					return true;


			} catch (\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function isTotalProspectCashOutExceedsBalance(float $amount):bool {
			try{
				$Wallet = new Wallet;
				$raw_pk = sodium_bin2hex($Wallet->getPublickey());

				$cashOutServiceCharge = (new Settings())->getCashOutServiceCharge();

				// Amount of current request and the service charge
				$ProspectiveAmount = $amount + $cashOutServiceCharge;

				
				$ReqPool = $this->where( [ 'receiver_address', $raw_pk ], ['paid', false] )->filter('amount')->get('VERBOSE');


					foreach($ReqPool as $req){
				
						$ProspectiveAmount += $req->amount;
					}
				
				

				/** Total main amount of previous unpaid request, current request + service charge, and service
				 * charges of previous requests 
				 * **/
				$ProspectiveAmount += ( sizeof($ReqPool) * $cashOutServiceCharge);
				
				if( $ProspectiveAmount > $Wallet->getBalance($raw_pk) )
					return true;
				
				return false;
				
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function markRequestAsPaid($request_id):bool {
			
			$this->paid = true;
			if( $this->where( ['id', $request_id] )->update() == 1){
				return true;
			}
			
			return false;
		}

		public function getCashouts(){

			$public_key = (new Wallet())->getPublickey();

			return (new self())->all()->where( ['receiver_address', sodium_bin2hex($public_key) ] )->desc()->get('VERBOSE');

		}

	}
?>
