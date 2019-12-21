<?php

namespace src\client\model;

use src\config\Config;

use zil\factory\Logger;

use \zil\factory\Model;

use \zil\core\tracer\ErrorTracer;

class TopupRequest {

	use Model;

	public $id = null;
	public $request_hash = null;
	public $bearer_address = null;
	public $mode = null;
	public $amount = null;
	public $service_charge = null;
	public $slipidororderid = null;
	public $bearer = null;
	public $voucherpinorairtimepin = null;
	public $note = null;
	public $status = null;
	public $created_at = null;


	public static $table = 'TopupRequest';

		public function __construct()
		{
			self::$key = 'id';
		}

		public function availableModes(string $mode): string{
			try{
				$modes = [
					'SHARE_N_SELL' => 'SHARE_N_SELL',
					'AIRTIME_PIN' => 'AIRTIME_PIN',
					'BANK' => 'BANK',
					'CARD' => 'CARD'
				];

				if( !in_array($mode, $modes) )
					throw new \DomainException("Possible rejection: Unknown topup mode");
				else
					return $mode;
			}catch(\DomainException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function availableStatus(string $status): string{
			try{
				$statuses = [
					'PENDING' => 'PENDING',
					'CONFIRMED' => 'CONFIRMED',
					'DELETED' => 'DELETED',
					'REJECTED' => 'REJECTED',
				];

				if( !in_array($status, $statuses) )
					throw new \DomainException("Possible rejection: Unknown topup status");
				else
					return $status;
			}catch(\DomainException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getTopUps(){

			try {
				$public_key = (new Wallet())->getPublickey();
				return (new self())->all()->where(['bearer_address', sodium_bin2hex($public_key)])->desc()->get('VERBOSE');
			}catch (\Throwable $t) {
				new ErrorTracer($t);
			}
		}

		public function cancelReq(int $id){
			try {

				$mode = $this->availableStatus('DELETED');
				if( strlen($mode) > 0 ){

					$bearer = $this->where( ['id', $id] )->filter('bearer_address AS ba', 'status')->get();

					$ba = $bearer->ba;
					$status = $bearer->status;

					$pk = (new Wallet())->getPublickey();

					if($ba != sodium_bin2hex($pk) )
						throw new \Exception("Inconsistent bearer address");

					if(!in_array($status, [ 'PENDING','REJECTED']) )
						return false;

					if( $this->where(['id', $id])->delete() == 1)
						return true;

				}else{
					throw new \Exception("Possible rejection: unknown event DELETED");
				}

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}
		}

		public function markRequestAsConfirmed($request_id):bool {

			$this->status = 'CONFIRMED';
			if( $this->where( ['id', $request_id] )->update() == 1)
				return true;

			return false;
		}


		public function confirmCardTopupAsPaid( float $amount, int $req_id ):bool{
			

			$Wallet = new Wallet;

			$public_key = $Wallet->getPublickey() ;
			$TpRq = self::filter('bearer_address', 'mode')->where( ['id', $req_id] )->get();
			
			$raw_user_public_key = $TpRq->bearer_address;
			
			$Service_Charge = 0.00;

			if( $TpRq->mode == $this->availableModes('CARD')  ){
			
				$Service_Charge =  ( (new Settings())->getTopViaCardChargeRate() / 100 ) * $amount;
			}

			if( $Wallet->topUp( sodium_hex2bin($raw_user_public_key), $amount) ){
				$this->markRequestAsConfirmed($req_id);

				if($Service_Charge > 0){
					
					// $exh = $Service_Charge > 2500 ? 100.00 : 0.00;
					$exh = 0.00;
					
					$Service_Charge += $exh;
					// Initiate Top Service Charge
					$Transaction = new Transaction;
	
					$from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', $raw_user_public_key ] )->get();
								
					$payment_meta_info = [ 
						'type' => (new Transaction())->getTransactionTypes('TOPUP_SERVICE_CHARGE'), 
						'to_address' => $public_key , 
						'from_address'=>
							[ 
								'pubk'=> sodium_hex2bin($from_prime_pk->public_key), 
								'prik'=> sodium_hex2bin($from_prime_pk->private_key) 
							]
					];
					$Transaction->addServiceTrans(
						$payment_meta_info['type'],
						'CONFIRMED',
						[ 
							$payment_meta_info['from_address']['pubk'], 
							$payment_meta_info['from_address']['prik'] 
						], 
						$payment_meta_info['to_address'], 
						$Service_Charge
					);
				}

				return true;
			}else{
				return false;
			}

		
		}

	}
?>
