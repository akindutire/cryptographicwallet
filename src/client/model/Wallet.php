<?php

namespace src\client\model;

use src\client\config\Config;
use src\client\service\Crypto;
use zil\factory\Logger;
use \zil\factory\Model;
use \zil\core\tracer\ErrorTracer;

class Wallet {

	use Model;

	public $id = null;
	public $owned_by = null;
	public $isPrime = null;
	public $public_key = null;
	public $private_key = null;
	public $sign_publickey = null;
	public $sign_privatekey = null;
	public $balance = null;
	public $credits = null;
	public $debits = null;
	public $acc_no = null;
	public $acc_name = null;
	public $bank = null;
	public $created_at = null;


	public static $table = 'Wallet';

		public function __construct()
		{
			self::$key = 'public_key';
		}

		public function createCryptoWallet():array{
			return (new Crypto())->generatePublicKeyPair();
		}

		public function isSufficientBalance(float $amount): bool{

			try{

				$user_id = (new ExtraUserInfo())->getUserId();
				if( (new self())::filter('id')->where( ['balance', '>=', $amount, 'AND'], ['owned_by', $user_id] )->count() == 1)
					return true;
				else
					return false;

			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function isValid(string $wallet_key): bool{

			try{
				if( (new self())::filter('id')->where( ['public_key', $wallet_key] )->count() == 1) 
					return true;
				else
					return false;
			
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}


		public function pay( $amount, array $payment_meta_info ): bool{

			try{
			
				// Session and init by system event from auth sender
				if($this->isSufficientBalance( $amount)){

					// transfer to recipient wallet

					$Transaction = new Transaction;

					if($payment_meta_info['type'] == $Transaction->getTransactionTypes('ACCOUNT_UPGRADE') ){

						// Membership account upgrade - SESSION REQ.
						$TransFeedback = $Transaction->addNewMemberShipTrans(
							$payment_meta_info['type'], 
							'CONFIRMED', 
							[
								$this->getPublickey(), 
								$this->getSecretkey() 
							], 
							$this->getAnyAdminPublickey(), 
							$amount,
							"ACCOUNT UPGRADE"
						);

					}else if($payment_meta_info['type'] == $Transaction->getTransactionTypes('FUND_TRANSFER')){
						
						// transfer between wallet- SESSION REQ.
						$TransFeedback = $Transaction->addTransferTrans(
							$payment_meta_info['type'],
							'PENDING', 
							[
								$this->getPublickey(), 
								$this->getSecretkey() 
							], 
							$payment_meta_info['to_address'], 
							$amount, 
							$payment_meta_info['trusted'] 
						);
						
					}else{
						
						$TransFeedback = false;
					}

					return $TransFeedback;

				
				}else{
					return false;
				}
			
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		// Credit receiver
		public function credit( $address, float $amount) : bool{

			try{

				$address = sodium_bin2hex($address);

				if($this->getCredit($address) < 0)
						throw new \RangeException("Credits are supposed to be positives");

				$this->credits = $this->getCredit($address) + $amount;
				
				if ( $this->where( ['public_key', $address] )->update() == 1 )
					return true;
				else
					return false;

			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		// Debit sender
		public function debit( $address, float $amount) : bool{
			
			try{

				$address = sodium_bin2hex($address);

				if($this->getDebit($address) > 0)
					throw new \RangeException("Debits are supposed to be negatives");

				
				$this->debits = $this->getDebit($address) - $amount ;

				if ( $this->where( ['public_key', $address] )->update() == 1 )
					return true;
				else
					return false;

			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function balanceWallet( $address) : bool{
			
			try{

				$address = sodium_bin2hex($address);
				
				if($this->getDebit($address) >  0)
					throw new \RangeException("Debits are supposed to be negatives");

				$this->balance = $this->getDebit($address) + $this->getCredit($address) ;

				if ( $this->where( ['public_key', $address] )->update() == 1 )
					return true;
				else
					return false;

			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function transfer( float $amount, string $raw_to_address, bool $trusted = null ): bool{
			
			try{

				if( is_null($trusted) )
					$trusted = false;

				$Transaction = new Transaction;
				$payment_meta_info = [ 'type' => $Transaction->getTransactionTypes('FUND_TRANSFER'), 'to_address' => sodium_hex2bin($raw_to_address), 'trusted' => $trusted ];

				return $this->pay( $amount, $payment_meta_info);

			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}	
		}

		public function topUp( string $address, float $amount ): bool{
			return $this->credit($address, $amount) && $this->balanceWallet($address);
		}

		public function cashOut( string $address, float $amount ): bool{
			
			return $this->debit($address, $amount) && $this->balanceWallet($address);
			
		}

		public function getBalance( string $raw_public_key){
			try{
				$user_id = self::filter('owned_by')->where( ['public_key', $raw_public_key] )->get()->owned_by;
				return self::filter('balance')->where( ['owned_by', $user_id] )->get()->balance;
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}	
		}

		public function getCredit( string $raw_public_key){
			try{
				
				return self::filter('credits')->where( ['public_key', $raw_public_key] )->get()->credits;
				
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}	
		}

		public function getDebit( string $raw_public_key){
			
			try{

				return self::filter('debits')->where( ['public_key', $raw_public_key] )->get()->debits;

			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}	
		}

		/**
		 * Extracts public key of current user and return its binary form
		 *
		 * @return string
		 */
		public function getPublickey(): string{

			try{

				$user_id = (new ExtraUserInfo())->getUserId();
			
				$pk = (new self())::filter('public_key')->where( ['owned_by', $user_id] )->get()->public_key;
			
				return sodium_hex2bin($pk);
			
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}	
		}

		public function getSecretkey(): string{
			
			try{
				$user_id = (new ExtraUserInfo())->getUserId();
				$pk = (new self())::filter('private_key')->where( ['owned_by', $user_id] )->get()->private_key;
				return sodium_hex2bin($pk);
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}	
		}

		/**
		 * Extracts any admin public key of current user and return its binary form
		 *
		 * @return string
		 */
		public function getAnyAdminPublickey(): string{
			
			try{
				$pk = (new self())->as('w')->with('ExtraUserInfo as ex', 'w.owned_by = ex.id')->with('User as u', 'ex.email = u.email')->filter('w.public_key')->where( ['w.isPrime', 1], ['u.hidden', 0], ['u.suspended', 0] )->orderBy('RAND()')->first()->public_key;
				return sodium_hex2bin($pk);
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}	
		}

		public function getWalletDetails(string $raw_public_key){
			
			try{
				$user_id = self::filter('owned_by')->where( ['public_key', $raw_public_key] )->get()->owned_by;
				return self::all()->where( ['owned_by', $user_id] )->get();
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}	
		}

		public function isBankDetailsAvailable($raw_public_key):bool{
			try{
				
				$check_ct = self::filter('bank')->where( 
					['public_key', $raw_public_key], 
					[ 
						['bank', '', 'OR'],  
						['acc_no', 0, 'OR'], 
						['acc_name', 0] 
					] 
					)->count();
				
				if($check_ct == 1)
					return false;
				else
					return true;

			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\RangeException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}
	}
?>
