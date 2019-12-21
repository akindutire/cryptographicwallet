<?php

namespace src\client\model;

use src\client\config\Config;

use src\client\service\BlockIOCoinTransferProvider;
use src\client\service\RaveAdapter;

use zil\core\scrapper\Info;

use \zil\factory\Model;

use \zil\core\facades\decorators\Route_D1;

use \zil\factory\Session;

use \zil\factory\Database;

use \zil\factory\Mailer;

use \zil\factory\Logger;

use Carbon\Carbon;

use \zil\core\tracer\ErrorTracer;

use src\client\service\MailService;

use src\client\service\Bill;
use zil\factory\Utility;

class Transaction {

	use Model,Route_D1;

	public $id = null;
	public $previous_trans_hash = null;
	public $trans_hash = null;
	public $ifrom = null;
	public $ito = null;
	public $amt_exchanged = null;
	public $type = null;
	public $mode = null;
	public $from_ip = null;
	public $message = null;
	public $nonce = null;
	public $status = null;
	public $note = null;
	public $updated_at = null;
	public $created_at = null;
	public $checksum = null;


	public static $table = 'Transaction';

		public function __construct()
		{
			self::$key = 'id';
		}

		public function TransactionGC(){

			try {

				/** Correct trans lock anomalies */
				if( sizeof($this->getPendingTransactions()) == 0 ){

					$id = (new ExtraUserInfo())->getUserId();

					$email = ExtraUserInfo::filter('email')->where([ 'id', $id] )->get()->email;

					$User = new User;
					$User->trans_lock = 0;
					$User->where( [ 'email', $email] )->update();
				}

				$myPk = (new Wallet())->getPublickey();

				$CoinTransferSvc = new BlockIOCoinTransferProvider();
				/**
				 * Default transaction obj
				 */
				$MT = new self;

				$T = self::filter('trans_hash', 'ifrom', 'type', 'created_at')
				->where(
					['status', $this->getTransactionStates('PENDING')], 
					[ 
						['ifrom', $myPk, 'OR'], 
						['ito', $myPk]
					]
				)->get('VERBOSE');

				// Logger::Init();
				foreach($T as $t){

					// Logger::Log((new Carbon())->diffInDays($t->created_at));

					if( $t->type != $this->getTransactionTypes('BITCOIN_TRADE')  && (new Carbon())->diffInDays($t->created_at) > 3 ){
						
						$this->where(['trans_hash', $t->trans_hash])->delete();
						
						(new SalesPoint())->where(['trade_key', $t->trans_hash])->delete();

					}

					if($t->type == $this->getTransactionTypes('BITCOIN_TRADE') && $t->ifrom == $myPk ){
						$BitcoinSale = (new SalesPoint())->filter('proofoftrade')->where(['trade_key', $t->trans_hash], ['type', $this->getTransactionTypes('BITCOIN_TRADE')])->get();

						$address_to_pay_to = $BitcoinSale->proofoftrade;
						$amount_to_pay = $t->amt_exchanged;

						/**
						 * Wipe out bitcoin transaction not completed within 3 days and archive the payment address
						 */
						 if(!$CoinTransferSvc->isBitcoinTransfered($address_to_pay_to, $amount_to_pay) && (new Carbon())->diffInDays($t->created_at) > 3){

						 	$MT->status = 'ARCHIVED';
						 	$MT->where(['trans_hash', $t->trans_hash])->update();

						 	$MS = new SalesPoint;
						 	$MS->status = 'ARCHIVED';
						 	$MS->where(['trade_key', $t->trans_hash])->update();

						 }

//						$this->where(['trans_hash', $t->trans_hash])->delete();
//
//						(new SalesPoint())->where(['trade_key', $t->trans_hash])->delete();

					}

				}
				// Logger::kill();

				$TopUpReq = new TopupRequest;

				$TpRq = $TopUpReq->filter('id','slipidororderid', 'amount')->where(['bearer_address', $myPk], ['mode', 'CARD'], ['status', 'PENDING'])->get('VERBOSE');

				foreach($TpRq as $rq){

					$transRef = $rq->slipidororderid;
					$Bill = new Bill;

					$PaymentReceipt = (new RaveAdapter())->confirmCardPaymentToRave($transRef);

					if($PaymentReceipt->status == "success"){

						if( $TopUpReq->filter('id')->where(['id', $rq->id], ['status', 'PENDING'])->count() == 1){

							$TopUpReq->confirmCardTopupAsPaid( $rq->amount, $rq->id );

							$TopUpReq->status = "CONFIRMED";
							$TopUpReq->where(['id', $rq->id])->update();
	
						}
					
					}
				}


				// All transactions not present on 
				

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}

		}
		
		public function addNewMemberShipTrans(?string $type = 'ACCOUNT_UPGRADE', string $status, array $key_pair, string $to_address, float $amount, string $note = '') : bool {

			try{
				// Public address of Admin expected as destination address
				
				return $this->addServiceTrans(  $type, $status, $key_pair, $to_address, $amount, $note);
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}


		/**
		 * This is a pre-credited transaction, destination account would have been credited during topup
		 *
		 * @param string $type
		 * @param string $status
		 * @param array $key_pair
		 * @param string $to_address
		 * @param float $amount
		 * @param string $note
		 * @return boolean
		 */
		public function addServiceTrans(string $type, string $status, array $key_pair, string $to_address, float $amount, string $note = '' ) : bool{
			try{
				// Public address of Admin expected as destination address
				
				
				if($amount <= 0){
					
					Logger::Init();
						Logger::ELog("Zero amount can't be sent to {$to_address}");
					Logger::kill();

					return false;
				
				}
				
				$from_public_key = $key_pair[0];
				$from_secret_key = $key_pair[1];

				
				$handle = (new Database())->connect();
				$Wallet = new Wallet;

				$handle->beginTransaction();

					$status = 'CONFIRMED';
					
					$note .= "<hr><b>Mode</b>: ".$this->transactionMode('INTRA_WALLET')."<br><br><b>Type</b>: ".$type."<br><br>";


				$trans_hash = $this->createTransHash();

				$this->previous_trans_hash = $this->getPreviousTransHash();
				$this->trans_hash = $trans_hash;
				$this->ifrom = $from_public_key;
				$this->ito = $to_address;
				$this->amt_exchanged = $amount;
				$this->type = $type;
				$this->mode = $this->transactionMode('INTRA_WALLET');
				$this->from_ip = $this->ipDetect();
				$this->message = sodium_crypto_box_seal("{$amount}|{$from_public_key}|{$to_address}", $to_address);
				$this->status = $status;
				$this->note = $note;
				$this->created_at = Carbon::now();
				$this->updated_at = Carbon::now();

				if($this->create()){

						if ( $Wallet->debit($from_public_key, $amount) && $Wallet->credit($to_address, $amount) ){
							
							$Wallet->balanceWallet($from_public_key) && $Wallet->balanceWallet($to_address);
							
						}else{
							// Rollback
							$handle->rollback();
							return false;
						}							
							// Mail Sender and Receiver

							list($sender_email, $receiver_email)  = $this->getMailPair( $from_public_key, $to_address );

							$link = Utility::route('login');
							$sender = sodium_bin2hex($from_public_key);
							$receiver = sodium_bin2hex($to_address);

							$sender_balance = $Wallet->getBalance($sender);
							$receiver_balance = $Wallet->getBalance($receiver);

							// Sender
							$unsubscriptionLinkS = (new MailingList())->unsubscriptionLink($sender_email);
							$unsubscriptionLinkR = (new MailingList())->unsubscriptionLink($receiver_email);

							$date = date('d m, Y', time());

							$trans_hash = sodium_bin2hex($trans_hash);

								$s_msg = "
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'><br /><br /></span>
								<table width='540' cellpadding='0' cellspacing='0' border='0'>
									
									<tr>
												
											<!--Product One-->
											<td>
												<p>Transaction Receipt ($date)</p>
											</td>
											
										</tr>

									<tr>
									
										<!--Product One-->
										<td>
										<span style='font-size:17px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>{$type}</span><br /><br>

										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>[DEBIT] #{$trans_hash}</span><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>Amount: <b>NGN</b>{$amount}</span><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Receiver: {$receiver}</span><br /><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Status: {$status}</span><br /><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Balance: {$sender_balance}</span><br /><br />
										
										<hr><br>
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Details</span><br /><br />
											
										<div>{$note}</div>

										<a href='$link' class='btn btn-success' border='0'>Login to NaijaSub</a><br><br>
										<a title='Unsubscribe to stop receiving mails' href='$unsubscriptionLinkS' class='btn btn-success' border='0'>Unsubscribe</a>
										</td>
										
									</tr>
									</table>
									</center>
								</td>
								</tr>
								
								";
								

								$c_msg = "
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'><br /><br /></span>
								<table width='540' cellpadding='0' cellspacing='0' border='0'>
								
									
									<tr>
														
										<!--Product One-->
										<td>
											<p>Transaction Receipt ($date)</p>
										</td>
										
									</tr>

						
									<tr>
									
										<!--Product One-->
										<td>
										<span style='font-size:17px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>{$type}</span><br /><br>

										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>[CREDIT] #{$trans_hash}</span><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>Amount: <b>NGN</b>{$amount}</span><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Sender: {$sender}</span><br /><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Status: {$status}</span><br /><br />
										
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Balance: {$receiver_balance}</span><br /><br />
										
										<hr><br>
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Details</span><br /><br />
											
										<div>{$note}</div>
										
										
										<a href='$link' class='btn btn-success' border='0'>Login to NaijaSub</a><br><br>
										<a title='Unsubscribe to stop receiving mails' href='$unsubscriptionLinkR' class='btn btn-success' border='0'>Unsubscribe</a>
										</td>
										
									</tr>
									</table>
									</center>
								</td>
								</tr>
								
								";

							(new MailService())->sendTransactionAlertMail($sender_email, $s_msg);				
							(new MailService())->sendTransactionAlertMail($receiver_email, $c_msg);

							$handle->commit();
							return true;	
				
				}else{
					// Rollback
					$handle->rollback();
					return false;
				}
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function addTransferTrans(string $type = 'FUND_TRANSFER', string $status, array $key_pair, string $to_address, float $amount, bool $isTrusted = false, bool $freezeWallet = true){
	
			try{
				// Public address of Admin expected as destination address

				if($amount <= 0){
					
					Logger::Init();
						Logger::ELog("Zero amount can't be sent to {$to_address}");
					Logger::kill();

					return false;
				
				}


				$from_public_key = $key_pair[0];
				$from_secret_key = $key_pair[1];

				
				$handle = (new Database())->connect();
				$Wallet = new Wallet;

				$handle->beginTransaction();

				if($isTrusted){
					$status = $this->getTransactionStates('CONFIRMED');
					if ( $Wallet->debit($from_public_key, $amount)  &&  $Wallet->credit($to_address, $amount) ){
						
						$Wallet->balanceWallet($from_public_key) && $Wallet->balanceWallet($to_address);
						
					}else{
						// Rollback
						$handle->rollback();
						return false;
					}
				}else{
					$status = $this->getTransactionStates('PENDING');

				}
					
				$msg = $this->encryptTransactionMessage($from_secret_key, $to_address, $amount);
				$trans_hash = $this->createTransHash();

				$note = "<hr><b>Mode</b>: ".$this->transactionMode('INTRA_WALLET')."<br><br><b>Type</b>: ".$type."<br><br>";

				$this->previous_trans_hash = $this->getPreviousTransHash();
				$this->trans_hash = $trans_hash;
				$this->ifrom = $from_public_key;
				$this->ito = $to_address;
				$this->amt_exchanged = $amount;
				$this->type = $type;
				$this->mode = $this->transactionMode('INTER_WALLET');
				$this->from_ip = $this->ipDetect();
				$this->message = $msg['cipher'];
				$this->nonce = $msg['nonce'];
				$this->status = $status;
				$this->note = $note;
				$this->created_at = Carbon::now();
				$this->updated_at = Carbon::now();

				if($this->create()){

						// Lock Transaction
						if(!$isTrusted && $freezeWallet){
							$this->TransactionLock([ 'from' => $from_public_key, 'to' => $to_address ], true);
						}

							
							// Mail Sender and Receiver
							
							list($sender_email, $receiver_email)  = $this->getMailPair( $from_public_key, $to_address );

							$link = Utility::route('login');
							$sender = sodium_bin2hex($from_public_key);
							$receiver = sodium_bin2hex($to_address);

							$sender_balance = $Wallet->getBalance($sender);
							$receiver_balance = $Wallet->getBalance($receiver);
	
							// Sender
							$unsubscriptionLinkS = (new MailingList())->unsubscriptionLink($sender_email);
							$unsubscriptionLinkR = (new MailingList())->unsubscriptionLink($receiver_email);

							$trans_hash = sodium_bin2hex($trans_hash);
							$date = date('d m, Y', time());
							
								$s_msg = "
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'><br /><br /></span>
								<table width='600' cellpadding='0' cellspacing='0' border='0'>

									<tr>
												
											<!--Product One-->
											<td>
												<p>Transaction Receipt ($date)</p>
											</td>
											
									</tr>

									

									<tr>
									
										<!--Product One-->
										<td>

										<span style='font-size:17px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>{$type}</span><br /><br>
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>[DEBIT] #{$trans_hash}</span><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>Amount: <b>NGN</b>{$amount}</span><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Receiver: {$receiver}</span><br /><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Status: {$status}</span><br /><br />
										
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Balance: {$sender_balance}</span><br /><br />
										
										
										
										<a href='$link' class='btn btn-success' border='0'>Login to NaijaSub</a><br><br>
										<a title='Unsubscribe to stop receiving mails' href='$unsubscriptionLinkS' class='btn btn-success' border='0'>Unsubscribe</a>
										</td>
										
									</tr>
									</table>
									</center>
								</td>
								</tr>
								
								";
								

								$c_msg = "
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'><br /><br /></span>
								<table width='600' cellpadding='0' cellspacing='0' border='0'>
									<tr>
									
										<!--Product One-->
										<td>

										<span style='font-size:17px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>{$type}</span><br /><br>

										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>[CREDIT] #{$trans_hash}</span><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif; font-weight:bold;'>Amount: <b>NGN</b>{$amount}</span><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Sender: {$sender}</span><br /><br />
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Status: {$status}</span><br /><br />
										
										<span style='font-size:14px; font-family:Arial, Helvetica, sans-serif;  font-weight:bold;'>Balance: {$receiver_balance}</span><br /><br />
							
										<a href='$link' class='btn btn-success' border='0'>Login to NaijaSub</a><br><br>
										<a title='Unsubscribe to stop receiving mails' href='$unsubscriptionLinkR' class='btn btn-success' border='0'>Unsubscribe</a>
										</td>
										
									</tr>
									</table>
									</center>
								</td>
								</tr>
								
								";

							(new MailService())->sendTransactionAlertMail($sender_email, $s_msg);				
							(new MailService())->sendTransactionAlertMail($receiver_email, $c_msg);


							$handle->commit();
							return true;	
				
				}else{
					
					// Unlock trans.
					if(!$isTrusted && $freezeWallet){
						$this->TransactionLock([ 'from' => $from_public_key, 'to' => $to_address ], false);
					}

					// Rollback
					$handle->rollback();
					return false;
				}
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function confirmTransaction(string $raw_transaction_hash): bool{

			try{

                $handle = (new Database())->connect();

                $handle->beginTransaction();

				$transaction_hash = sodium_hex2bin($raw_transaction_hash);

				$trans = self::filter('id','status', 'ito', 'ifrom', 'nonce', 'message', 'amt_exchanged')->where( ['trans_hash', $transaction_hash] )->get();

				if($trans->ito != (new Wallet())->getPublickey()){
				    return false;
                }

				if($trans->status == 'PENDING'){
					
//					Logger::Init();
//					Logger::ELog("+---------TESTING TRANS CONFIRMATION-----------+",$trans->status);
//					Logger::kill();

					if(  $this->decryptTransactionMessage($trans->ifrom, $trans->amt_exchanged, [ 'cipher' => $trans->message, 'nonce'=>$trans->nonce ] ) ){
						
//						Logger::Init();
//						Logger::ELog("+---------TESTING TRANS CONFIRMATION-----------+","DECRYPTED");
//						Logger::kill();
						
						$Wallet = new Wallet;
						
						$from_pk 	= 	$trans->ifrom;
						$to_pk 		= 	$trans->ito;
						$amount 	=	$trans->amt_exchanged; 

						if ( $Wallet->debit($from_pk, $amount)  &&  $Wallet->credit($to_pk, $amount) ){
							
							$Wallet->balanceWallet($from_pk) && $Wallet->balanceWallet($to_pk);
							
							$this->message = sodium_crypto_box_seal("{$amount}|{$from_pk}|{$to_pk}",$to_pk);
							$this->status = 'CONFIRMED';
							$this->nonce = null;
							$this->updated_at = Carbon::now();
							$this->where( ['trans_hash', $transaction_hash] )->update();

                            //Commit Transaction changes
                            $handle->commit();

                            $this->TransactionLock([ 'from' => $trans->ifrom, 'to' => $trans->ito ], false);
                            return true;

                        }else{
							// Rollback Transaction changes
							$handle->rollback();
							return false;
						}


					}else{
						return false;
					}
				}else{
					return true;
				}
			}catch(\TypeError $t){
				new ErrorTracer($t);
				return false;
			}catch(\Throwable $t){
				new ErrorTracer($t);
				return false;
			}
		}

		public function passTransaction(string $raw_transaction_hash): bool{

			try{

			    $handle = (new Database())->connect();

			    $handle->beginTransaction();

				$transaction_hash = sodium_hex2bin($raw_transaction_hash);

				$trans = self::filter('id','status', 'ito', 'ifrom', 'nonce', 'message', 'amt_exchanged')->where( ['trans_hash', $transaction_hash] )->get();

				if($trans->status == 'PENDING'){

						$Wallet = new Wallet;
						
						$from_pk 	= 	$trans->ifrom;
						$to_pk 		= 	$trans->ito;
						$amount 	=	$trans->amt_exchanged; 

						if ( $Wallet->debit($from_pk, $amount)  &&  $Wallet->credit($to_pk, $amount) ){
							
							$Wallet->balanceWallet($from_pk) && $Wallet->balanceWallet($to_pk);

							$this->message = sodium_crypto_box_seal("{$amount}|{$from_pk}|{$to_pk}",$to_pk);
							$this->status = $this->getTransactionStates('CONFIRMED');
							$this->nonce = null;
							$this->updated_at = Carbon::now();
							$this->where( ['trans_hash', $transaction_hash] )->update();

                            //Commit Transaction changes
							$handle->commit();

						}else{
							// Rollback
							$handle->rollback();
							return false;
						}

						$this->TransactionLock([ 'from' => $trans->ifrom, 'to' => $trans->ito ], false);
						return true;

				}else{
					return true;
				}
			}catch(\TypeError $t){
				new ErrorTracer($t);
				return false;
			}catch(\Throwable $t){
				new ErrorTracer($t);
				return false;
			}
		}

		public function rollbackTransaction( string $transaction_hash, string $note ) : bool {
			try{

                $handle = (new Database())->connect();
                $handle->beginTransaction();

				$transaction_hash = sodium_hex2bin($transaction_hash);
                $fmr_trans = self::filter('ifrom', 'ito', 'amt_exchanged', 'status', 'note', 'updated_at')->where( ['trans_hash', $transaction_hash] )->get();

                $from_public_key = $fmr_trans->ifrom;
                $to_address = $fmr_trans->ito;
				$amount = $fmr_trans->amt_exchanged;

				$Wallet = new Wallet;
				
				$balance = $Wallet->getCredit(  sodium_bin2hex($fmr_trans->ito) ) + $Wallet->getDebit(  sodium_bin2hex($fmr_trans->ito) );


				/**Receiver must not spend money he doesnt has */
				if($balance < $fmr_trans->amt_exchanged){
					return false;
				}

				if($fmr_trans->status == $this->getTransactionStates('CONFIRMED') ){

					if( $Wallet->debit($fmr_trans->ito, $fmr_trans->amt_exchanged)  &&  $Wallet->credit($fmr_trans->ifrom, $fmr_trans->amt_exchanged) ){


						$this->TransactionLock([ 'from' => $from_public_key, 'to' => $to_address ], false);
						if( $Wallet->balanceWallet($from_public_key) && $Wallet->balanceWallet($to_address) ){

							// Commit

							$sender_balance = $Wallet->getBalance( sodium_bin2hex($from_public_key) );
							$receiver_balance = $Wallet->getBalance( sodium_bin2hex($to_address) );

							$this->message = sodium_crypto_box_seal("{$amount}|{$from_public_key}|{$to_address}",$to_address);
							$this->nonce = null;
							$this->status = $this->getTransactionStates('ROLLEDBACK');
							$this->note = "</b>Rollback Note</b> :<br>Date: ".Carbon::now()."<br>Details: ".$note."<br><hr><b>Initial Transaction</b><br><br>".$fmr_trans->note;
							$this->updated_at = Carbon::now();
							
							$this->where( ['trans_hash', $transaction_hash] )->update();

							
							
							// Mail Sender and Receiver
							
							list($sender_email, $receiver_email)  = $this->getMailPair( $from_public_key, $to_address );

							$sender = sodium_bin2hex($from_public_key);
							
							$unsubscriptionLink = (new MailingList())->unsubscriptionLink($receiver_email);
							$Rhash = sodium_bin2hex($transaction_hash);

							$segments['body'] = "<p><b>Transaction {$Rhash}:</b> has just been rolled back,  NGN {$fmr_trans->amt_exchanged} from {$sender} is UNDONE</p><p><b>Balane:</b> {$sender_balance}</p>><br>  <br><br><p style='text-align: center;'>  <a href='$unsubscriptionLink'><u>Unsubscribe to mail</u></a></p>";
							(new MailService())->sendTransactionAlertMail($sender_email, $segments['body']);

							$segments['body'] = "<p><b>Transaction {$Rhash}:</b> has just been rolled back,  NGN {$fmr_trans->amt_exchanged} from {$sender} is UNDONE</p><p><b>Balane:</b> {$receiver_balance}</p><br>  <br><br><p style='text-align: center;'>  <a href='$unsubscriptionLink'><u>Unsubscribe to mail</u></a></p>";
							(new MailService())->sendTransactionAlertMail($receiver_email, $segments['body']);


							$handle->commit();
							return true;	
							
						}else{
							$this->TransactionLock([ 'from' => $from_public_key, 'to' => $to_address ], true);
							$handle->rollback();
							return false;
						}
						
					
					}else{

						$this->TransactionLock([ 'from' => $from_public_key, 'to' => $to_address ], true);
						$handle->rollback();
					}
				}else if($fmr_trans->status == $this->getTransactionStates('PENDING') ){
					
					
					// $this->status = $this->getTransactionStates('ROLLEDBACK');
					// $this->updated_at = Carbon::now();
					
					// $this->where( ['trans_hash', $transaction_hash] )->update();
					// $this->TransactionLock([ 'from' => $from_public_key, 'to' => $to_address ], false);

					// // Mail Sender and Receiver

					// list($sender_email, $receiver_email)  = $this->getMailPair( $from_public_key, $to_address );

					// $link = Utility::route('login');
					// $sender = sodium_bin2hex($from_public_key);
							
					// $unsubscriptionLink = (new MailingList())->unsubscriptionLink($receiver_email);
					// $body = "<p><b>Transaction {$transaction_hash}:</b> has just been rolled back,  NGN {$fmr_trans->amount_exchanged} from {$sender} is UNDONE</p><br>  <br><br><p style='text-align: center;'>  <a href='$unsubscriptionLink'><u>Unsubscribe to mail</u></a></p>";

					// (new MailService())->sendTransactionAlertMail($sender_email, $body);
					// (new MailService())->sendTransactionAlertMail($receiver_email, $body);

											
					// $handle->commit();
					// return true;
					
				}else{
					return true;
				}
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		} 

		public function cancelTransaction(string $transaction_hash) : bool {
			$transaction_hash = sodium_hex2bin($transaction_hash);

			$this->status = $this->getTransactionStates('ARCHIVED');
			$this->nonce = null;
			$affected = $this->where([ 'trans_hash', $transaction_hash])->update();

			if($affected == 1) {

				$S = new SalesPoint();
				$S->status = $this->getTransactionStates('ARCHIVED');
				$S->where(['trade_key', $transaction_hash])->update();

				return true;
			}

			return false;
		}

		public function isLocked(string $email){
			try {

				if( (new User())->filter('trans_lock')->where( ['email', $email ], ['trans_lock', 1])->count() == 1){
					return true;
				}else{
					return false;
				}

			} catch (\Throwable $t){
				new ErrorTracer($t);
			}
		}
		public function getPendingTransactions(){
			
			try{	
				$Wallet = new Wallet;

				$pk = $Wallet->getPublickey();

				return self::all()->where( ['status', 'PENDING'], [ ['ito', $pk, 'OR'], ['ifrom', $pk] ] )->desc()->get('VERBOSE');
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		
		}

		public function getIncomingPendingTransactions(){
			try{	
				$Wallet = new Wallet;

				$pk = $Wallet->getPublickey();

				return self::all()->where( ['status', 'PENDING'],  ['ito', $pk] )->desc()->get('VERBOSE');
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getOutgoingPendingTransactions(){
			try{
				$Wallet = new Wallet;

				$pk = $Wallet->getPublickey();

				return self::all()->where( ['status', 'PENDING'],  ['ifrom', $pk] )->desc()->get('VERBOSE');
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getConfirmedTransactions(){
			try{
				$Wallet = new Wallet;

				$pk = $Wallet->getPublickey();

				return self::all()->where( ['status', 'CONFIRMED'], ['ito', $pk] )->desc()->get('VERBOSE');
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getRollbackTransactions(){
			try{
				$Wallet = new Wallet;

				$pk = $Wallet->getPublickey();

				return self::all()->where( ['status', 'ROLLEDBACK'], ['ito', $pk] )->desc()->get('VERBOSE');
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getOutgoingTransactions(){
			try{
				$Wallet = new Wallet;

				$pk = $Wallet->getPublickey();

				return self::all()->where( ['ifrom', $pk] )->desc()->get('VERBOSE');
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getIncomingTransactions(){
			try{

				$Wallet = new Wallet;

				$pk = $Wallet->getPublickey();

				return self::all()->where( ['ito', $pk] )->desc()->get('VERBOSE');
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

        /**
         * @return array
         */
        public function getAllTransactions() : array {
			try{
				$Wallet = new Wallet;

				$pk = $Wallet->getPublickey();

				return self::all()->where( ['ifrom', $pk, 'OR'], ['ito', $pk] )->desc()->get('VERBOSE');
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getTransactionTypes(string $type): string{

			try{

				$types = [
					'ACCOUNT_UPGRADE' => 'ACCOUNT_UPGRADE',
					
					'CASH_OUT_SERVICE_CHARGE' => 'CASH_OUT_SERVICE_CHARGE',
					'ELECTRICITY_SERVICE_CHARGE' => 'ELECTRICITY_SERVICE_CHARGE',
					'DSTV_SERVICE_CHARGE' => 'DSTV_SERVICE_CHARGE',
					'DATA_SERVICE_CHARGE' => 'DATA_SERVICE_CHARGE',
					'AIRTIME_SERVICE_CHARGE' => 'AIRTIME_SERVICE_CHARGE',
					'TOPUP_SERVICE_CHARGE' => 'TOPUP_SERVICE_CHARGE',

					'BITCOIN_TRADE' => 'BITCOIN_TRADE',
					'GIFTCARD_TRADE' => 'GIFTCARD_TRADE',
					'AIRTIME_TRADE' => 'AIRTIME_TRADE',
					'DATA_BUNDLE_TRADE' => 'DATA_BUNDLE_TRADE',
					'BULK_SMS_TRADE' => 'BULK_SMS_TRADE',
					'ELECTRICITY_BILL_TRADE' => 'ELECTRICITY_BILL_TRADE',
					'BILL_TRADE' => 'BILL_TRADE',
					'CABLETV_BILL_TRADE' => 'CABLETV_BILL_TRADE',
					'E_PIN_ONE_TIME_FEE' => 'E_PIN_ONE_TIME_FEE',
					'DATA_E_PIN_TRADE' => 'DATA_E_PIN_TRADE',
					'AIRTIME_E_PIN_TRADE' => 'AIRTIME_E_PIN_TRADE',
					
					'FUND_TRANSFER' => 'FUND_TRANSFER',
					'REFERRAL_BONUS' => 'REFERRAL_BONUS',

					'ROLLBACK' => 'ROLLBACK',
					
				];

				if( !in_array($type, $types) )
					throw new \DomainException("Possible rejection: Unknown transaction type {$type}");
				else
					return $type;
				
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\DomainException $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function getTransactionStates(string $state): string{

			try{

				$states = [
					'PENDING' => 'PENDING',
					'CONFIRMED' => 'CONFIRMED',
					'ROLLEDBACK' => 'ROLLEDBACK',
					'ARCHIVED' => 'ARCHIVED'
				];

				if( !in_array($state, $states) )
					throw new \DomainException("Possible rejection: Unknown transaction state");
				else
					return $state;

			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function transactionMode(string $mode): string{
			try{
				$modes = [
					'INTER_WALLET' => 'INTER_WALLET',
					'INTRA_WALLET' => 'INTRA_WALLET',
					'CARD' => 'CARD',
					'BANK' => 'BANK',
					'AIRTIME' => 'AIRTIME',
				];

				if( !in_array($mode, $modes) )
					throw new \DomainException("Possible rejection: Unknown transaction mode");
				else
					return $mode;


			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}


		private function TransactionLock(array $sender_receiver_key_pair, bool $lockVal){
			
			try{
				$Wallet = new Wallet;

				$sender = $Wallet->getWalletDetails( sodium_bin2hex($sender_receiver_key_pair['from']) );
				$receiver = $Wallet->getWalletDetails( sodium_bin2hex($sender_receiver_key_pair['to']) );
				
				$sender_email = ExtraUserInfo::filter('email')->where( ['id', $sender->owned_by] )->get()->email;
				$receiver_email = ExtraUserInfo::filter('email')->where( ['id', $receiver->owned_by] )->get()->email;

				$User = new User;
				
				if ( $lockVal == false){

					// Relieve sender account if all its outgoing fund transfer trans. has been confirmed
					if( $this->where( ['ifrom', $sender_receiver_key_pair['from'] , ['status', 'PENDING'], ['type', $this->getTransactionTypes('FUND_TRANSFER')] ] )->filter('trans_hash')->count() == 0 ){
						$User->trans_lock = $lockVal;
						$User->where( ['email', $sender_email ] )->update();
					}

					// Relieve receiver account if all my outgoing fund transfer trans. has been confirmed
					if( $this->where( ['ito', $sender_receiver_key_pair['to'] , ['status', 'PENDING'], ['type', $this->getTransactionTypes('FUND_TRANSFER')] ] )->filter('trans_hash')->count() == 0 ){
						$User->trans_lock = $lockVal;
						$User->where( ['email', $receiver_email ] )->update();

					}
				}else{
					
					$User->trans_lock = $lockVal;
					$User->where( ['email', $sender_email ] )->update();

					$User->trans_lock = $lockVal;
					$User->where( ['email', $receiver_email ] )->update();
				}
				unset($User, $Wallet);			
			
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		
		}
		

		private function encryptTransactionMessage($My_Secret_address ,$destination_public_address, $amount): array{

			try{
                if( Info::$_dataLounge['API_CLIENT_ACTIVE'] )
                    $email = Info::$_dataLounge['API_CLIENT']['PK'];
                else
                    $email = Session::get('email');

				$message = $amount.'|'.$email.', I sent fund to you. '.$destination_public_address;
				$nonce = random_bytes( SODIUM_CRYPTO_BOX_NONCEBYTES );


				$I_send_to_someone_kp = sodium_crypto_box_keypair_from_secretkey_and_publickey( $My_Secret_address, $destination_public_address );

				return [ 
					'nonce' => $nonce, 
					'cipher' => sodium_crypto_box( $message, $nonce, $I_send_to_someone_kp ) 
				];
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		private function decryptTransactionMessage(string $sender_public_address, string $amountSent, array $encrypedBox) : string {
			
			try{

				$Someone_sent_from_somewhere_kp = sodium_crypto_box_keypair_from_secretkey_and_publickey( (new Wallet())->getSecretkey(), $sender_public_address );

				$decrypted = sodium_crypto_box_open( $encrypedBox['cipher'], $encrypedBox['nonce'], $Someone_sent_from_somewhere_kp );
				
				if($decrypted !== false) {
					list($amountEnc, $note) = explode("|", $decrypted);
					if($amountEnc == $amountSent) {
						return true;
					}else{
						Logger::Init();
						Logger::ELog("An error occurred, couldn't decrypt transaction", $decrypted);
						Logger::kill();
						return false;
					}
				}else{
					return false;
				}
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		private function createTransHash() : string{
			try{

                if(Info::getRouteType() == 'api')
                    $email = Info::$_dataLounge['API_CLIENT']['PK'];
                else
                    $email = Session::get('email');

				return sodium_crypto_shorthash( time().''.$this->getPreviousTransHash().''.$email, random_bytes(SODIUM_CRYPTO_SHORTHASH_KEYBYTES) );
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		private function getPreviousTransHash() : string{
			
			try{
				if( self::filter('trans_hash')->count() == 0)
					return '';
				else
					return self::filter('trans_hash')->last()->trans_hash;
			
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}

		}

		private function getTransactionId(string $trans_hash){
			try{
				return self::filter('id')->where( ['trans_hash', $trans_hash] )->get()->id;
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		private function getMailPair( string $from_pk_address, string $to_pk_address ): array{

			try{
				$Wallet = new Wallet;

				$sender = $Wallet->getWalletDetails( sodium_bin2hex($from_pk_address) );
				$receiver = $Wallet->getWalletDetails( sodium_bin2hex($to_pk_address) );
				
				$sender_email = ExtraUserInfo::filter('email')->where( ['id', $sender->owned_by] )->get()->email;
				$receiver_email = ExtraUserInfo::filter('email')->where( ['id', $receiver->owned_by] )->get()->email;
				
				return [ $sender_email, $receiver_email ];
			
			}catch(\TypeError $t){
				new ErrorTracer($t);
			}catch(\Throwable $t){
				new ErrorTracer($t);
			}

		}
	}
?>
