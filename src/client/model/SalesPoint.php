<?php

namespace src\client\model;

use Carbon\Carbon;

use src\client\service\CoinPaymentTransferProvider;
use \zil\core\tracer\ErrorTracer;

use src\client\config\Config;

use \zil\factory\Fileuploader;

use \zil\factory\Model;

use \zil\factory\Logger;

use src\client\service\BlockIOCoinTransferProvider;

use src\client\service\MailService;

class SalesPoint {

	use Model;

	public $id = null;
	public $trade_key = null;
	public $trade_type = null;
	public $ifrom_address = null;
	public $ito_address = null;
	public $valueorqtyexchanged = null;
	public $rawamt = null;
	public $icurrency = null;
	public $extracharge = null;
	public $proofoftrade = null;
	public $proofoftradeformat = null;
	public $tradehistory = null;
	public $status = null;
	public $created_at = null;
	public $updated_at = null;
	public $checksum = null;


	public static $table = 'SalesPoint';

		public function SalesGC(){

			try {
				$myPk = sodium_bin2hex((new Wallet())->getPublickey());

				$S = self::filter('trade_key', 'proofoftrade', 'valueorqtyexchanged', 'ito_address', 'trade_type', 'created_at')->where(['status', $this->getTradeStatus('PROGRESS')], [['ifrom_address', $myPk, 'OR'], ['ito_address', $myPk]])->get('VERBOSE');

				$Transaction = new Transaction;
				$CoinTransfer = new CoinPaymentTransferProvider();

				$address = [];


				foreach ($S as $trade) {

					 
					if ( $trade->trade_type == $Transaction->getTransactionTypes('BITCOIN_TRADE') && $trade->ito_address == $myPk) {

						//Money expected to come to customer due to selling of bitcoin to naijasub but wasn't confirmed to be transfered should be reprobed

						if($CoinTransfer->isBitcoinTransfered( $trade->proofoftrade, floatval($trade->valueorqtyexchanged) )){
							
							if(  $Transaction->confirmTransaction( sodium_bin2hex($trade->trade_key) ) ){

								$this->status = $this->getTradeStatus('COMPLETED');
								$this->updated_at = Carbon::now();
								$this->update();

								$message = "Trade Sealed<br><br>Transaction id: #".sodium_bin2hex($trade->trade_key);
								(new MailService())->sendOrderReceipt($message, strtoupper(" {$trade->trade_type}") );

							}else{
								// $Thash = sodium_bin2hex($trade->trade_key);
								// Logger::Init();
								// 	Logger::Log("+--------------------------+", "| \t Couldn't confirm transaction(#{$Thash}) bitcoin transaction within system, but bitcoin has been transfered by {$myPk} through {$trade->proofoftrade}", "+--------------------------+");
								// Logger::kill();
							}
						}else{
							// $Thash = sodium_bin2hex($trade->trade_key);
							// Logger::Init();
							// 	Logger::Log("+--------------------------+", "| \t Bitcoin hasn't been transfered for transaction (#{$Thash}) on naijasub wallet {$myPk} through {$trade->proofoftrade}", "+--------------------------+");
							// Logger::kill();
						}

					}

				}

			} catch (\Throwable $t) {
				new ErrorTracer($t);
			}

		}

		public function cancelTrade( string $trade_key ){

			$Transaction = new Transaction;
				
			$this->where(['trade_key', $trade_key])->delete();
			$Transaction->where(['trans_hash', $trade_key])->delete();

		}

		public function getTrade(string $trade_key){

			$pk =  sodium_bin2hex( (new Wallet())->getPublickey() );

			return self::all()->where( 
				['trade_key', $trade_key], 
				[ 
					['ito_address', $pk, 'OR'], 
					['ifrom_address', $pk] 
			
				] 
				)->get('VERBOSE');
		}

		public function getTradeByType(string $trade_type){

			$pk =  sodium_bin2hex( (new Wallet())->getPublickey() );

			$ST = (new self())->as('s')->with('Transaction as t', 's.trade_key = t.trans_hash')->filter('s.id', 's.ifrom_address', 's.ito_address', 's.trade_key', 's.trade_type', 's.valueorqtyexchanged', 's.rawamt', 's.icurrency', 's.proofoftrade', 's.status', 's.tradehistory','t.status as tst', 's.created_at', 's.updated_at')->where(
				['s.trade_type', $trade_type], 
				[ 
					['ito_address', $pk, 'OR'], 
					['ifrom_address', $pk] 
				] 
				)->get('VERBOSE');

			return $ST;
		}

		public function getTradeStatus( string $status ): string{
			try{
				$statuses = [
					'IN_PROGRESS' => 'PROGRESS',
					'PROGRESS' => 	'PROGRESS',
					'COMPLETED' => 'COMPLETED',
					'SUSPENDED' => 'SUSPENDED',
					'ARCHIVED' => 'ARCHIVED'
				];

				if( !in_array($status, $statuses) )
					throw new \Exception("Possible rejection: Unknown trade status {$status}");
				else
					return $status;

			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}
		public function uploadGiftCardProofOfTrade(array $file){
			$name	=	$file['name'];
			$tmp	=	$file['tmp_name'];

			$permitted_types = ['image/png','image/jpeg','image/jpg','image/gif'];
			$max_size = 20 * 1058816;

			$cfg = new Config();

			$name = time().preg_replace('/[\s]+/','',$name);
			
			$uploadPath = $cfg->getProofOfTradeUploadPath().'/'.$name;

			$fileUploadHandler = (new Fileuploader())->upload([ 'file' => $tmp, 'size' => $max_size, 'type' => $permitted_types, 'destination' => $uploadPath, 'compress' => false ]);

			if( $fileUploadHandler->isUploaded()){
				
				$proofsrc = $cfg->getUrlofProofOfTradeUploadPath($name);

				return ['proofsrc' => $proofsrc, 'uploadPath' => $uploadPath, 'proofname' => $name, 'status' => true];
			}else{
				$err = $fileUploadHandler->getError();
				$fileUploadHandler->close();
				return ['error' => $err, 'status' => false];
			}
		}


		
	}
?>
