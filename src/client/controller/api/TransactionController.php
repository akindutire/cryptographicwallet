<?php
namespace src\client\controller\api;

	use src\client\middleware\Date;
	use src\client\middleware\SecureApi;
use src\client\model\ActivityLog;
use src\client\model\Authtoken;
use src\client\model\Wallet;
use zil\core\facades\decorators\Hooks;
	use \zil\core\server\Param;
	use \zil\core\server\Response;
	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

	use zil\factory\Logger;
	

	use src\client\model\Transaction;
	use src\client\model\SalesPoint;
	use src\client\model\Settings;
	use src\client\model\User;
use zil\security\Validation;

class TransactionController{

		use Notifier, Navigator, Hooks;

		
		public function __construct(){
			
			
			header('Access-Control-Allow-Origin: *');
			header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
			header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
			
		}

		public  function onInit(Param $param)
		{
			new Date($param);

		}

		public function onAuth(Param $param)
		{
			new SecureApi($param);
			(new Transaction())->TransactionGC();
		}


		public function isTransactionLocked(Param $param){
			try{

				$email = (new Authtoken())->getPk($param->url()->token);

				if( User::filter('trans_lock')->where( ['email', $email ], ['trans_lock', 1])->count() == 1){
					$data = [ 'msg' => ['state' => true], 'success' => true ];
				}else{
					$data = [ 'msg' => ['state' => false], 'success' => true ];
				}

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, 200);
			}
		}

	// Transfer fund between wallet
	public function TransferFund(Param $param){

		try{

			$wallet_key = $param->url()->wallet_key;

			$destination_address = $param->form()->des_address;
			$amount = doubleval($param->form()->amount);

			if( $wallet_key == $destination_address )
				throw new \Exception("Forbidden: Can't transfer fund to yourself");


			$Wallet = new Wallet;
			$User = new User;

			if(sodium_bin2hex($Wallet->getPublickey()) != $wallet_key)
				throw new \Exception("Tayunchi: Inconsistent wallet key");

			if( !$Wallet->isValid( $wallet_key ) )
				throw new \Exception("Invalid wallet, please ensure you are using your wallet key");


			if( $User->isTransactionLocked() )
				throw new \Exception("Sorry, you still have pending transaction");


			$Validation = new Validation(  ['des_address', 'required'], ['amount', 'number|min:0'] );

			if( $Validation->isPassed() && !empty($wallet_key)){


				/**
				 * Run checksum
				 */
				$checksum = null;
				if(isset($param->form()->timestamp)){
					$timestamp = $param->form()->timestamp;

					$checksum = (new Authtoken())->getCheckSum($timestamp);

					if ( (new Transaction())->isExists( ['checksum', $checksum] ) ){
						$data = [ 'msg' =>  "Fund already transfered, wait for receiver verification", 'success' => true ];
						return;
					}
				}


				(new ActivityLog())->Log("[WALLET TRANSFER] Validation Passed", "SUCCESS");


				if($amount < 0)
					throw new \Exception("Amount must be more than 0.00");


				if( !$Wallet->isSufficientBalance( $amount ) )
					throw new \Exception("Insufficient fund in wallet, please top up wallet");


				if( $Wallet->transfer( $amount, $destination_address, false) ){

					$data = [ 'msg' =>  "Fund transfered, wait for receiver verification", 'success' => true ];
				}else{

					throw new \Exception("Error: Couldn't complete fund transfer");
				}

				unset($Transaction, $Wallet);

			}else{
				$data = [ 'msg' => $Validation->getErrorString(), 'success' => false ];
			}

		}catch(\Throwable $t){
			$data = [ 'msg' =>   $t->getMessage(), 'success' => false ];
		}finally{
			echo Response::fromApi($data, 200);
		}

	}

	public function UserTransactions(Param $param){
			
			try{

				(new SalesPoint())->SalesGC();

				$trans_state = $param->url()->transaction_state;

				$TransactionObj = new Transaction;

				if($trans_state == 'pending'){

					$transactions = $TransactionObj->getPendingTransactions();

				}else if($trans_state == 'confirmed'){

					$transactions = $TransactionObj->getconfirmedTransactions();
				}else if($trans_state == 'rollback'){

					$transactions = $TransactionObj->getRollbackTransactions();
				}else if($trans_state == 'all'){
					//all
					$transactions = $TransactionObj->getAllTransactions();
				}
				
				$container = [];

				foreach( (array)$transactions as $transaction ){

					$transaction = (array)$transaction;
					$transaction = array_map( function($entry){
						
						if( !mb_detect_encoding($entry, 'ASCII', true) )
							return sodium_bin2hex($entry);
						else
							return $entry;

					} , $transaction );

					array_push($container, $transaction );
				}

				$data = [ 'msg' =>  $container, 'success' => true ];
			}catch(\Throwable $t){
				$data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi( $data, 200);
			}
			

		}
		
		public function UserIncomingTransactions(Param $param){
			
			
			try{

				(new SalesPoint())->SalesGC();

				$trans_state = $param->url()->transaction_state;

				$TransactionObj = new Transaction;

				if($trans_state == 'pending'){

					$transactions = $TransactionObj->getIncomingPendingTransactions();

				}else if($trans_state == 'confirmed'){

					$transactions = $TransactionObj->getConfirmedTransactions();
				}else if($trans_state == 'rollback'){

					$transactions = $TransactionObj->getRollbackTransactions();
				}else if($trans_state == 'all'){
					//all
					$transactions = $TransactionObj->getIncomingTransactions();
				}
				
				$container = [];

				foreach( (array)$transactions as $transaction ){

					$transaction = (array)$transaction;
				
					$transaction = array_map( function($entry){
						

						if( !mb_detect_encoding($entry, 'ASCII', true) )
							return sodium_bin2hex($entry);
						else
							return $entry;

					} , $transaction );

					array_push($container, $transaction );
				}

				$data = [ 'msg' =>  $container, 'success' => true ];
				
			}catch(\Throwable $t){
				$data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi( $data, 200);
			}

		}
		
		public function UserOutgoingTransactions(Param $param){
			
			try{
				(new SalesPoint())->SalesGC();

				$trans_state = $param->url()->transaction_state;

				$TransactionObj = new Transaction;

				if($trans_state == 'pending'){

					$transactions = $TransactionObj->getOutgoingPendingTransactions();

				}else if($trans_state == 'confirmed'){

					$transactions = $TransactionObj->getConfirmedTransactions();
				}else if($trans_state == 'rollback'){

					$transactions = $TransactionObj->getRollbackTransactions();
				}else if($trans_state == 'all'){
					//all
					$transactions = $TransactionObj->getOutgoingTransactions();
				}
				
				$container = [];

				foreach( (array)$transactions as $transaction ){

					$transaction = (array)$transaction;
					$transaction = array_map( function($entry){
						
						if( !mb_detect_encoding($entry, 'ASCII', true) )
							return sodium_bin2hex($entry);
						else
							return $entry;

					} , $transaction );

					array_push($container, $transaction);
				}

				$data = [ 'msg' =>  $container, 'success' => true ];
				
			}catch(\Throwable $t){
				$data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi( $data, 200);
			}

		}

		public function ConfirmTransactions(Param $param){

			try{

				(new SalesPoint())->SalesGC();

				$trans_hash = $param->url()->transaction_hash;

				$T = new Transaction;
				
				if( $T->filter('id')->where(['trans_hash', sodium_hex2bin($trans_hash)], ['type', $T->getTransactionTypes('FUND_TRANSFER')])->count() != 1)
					throw new \Exception("Couldn't confirm transaction, only FUND_TRANSFER can be confirmed from this terminal");

				if( (new Transaction())->confirmTransaction($trans_hash) )
					$data = [ 'msg' =>  'Transaction confirmed', 'success' => true ];
				else
					throw new \Exception("Couldn't confirm transaction, please retry");

			}catch(\Throwable $t){
				$data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi( $data, 200);
			}
			
		}

		

		public function ConversionRate(Param $param){
			try{
				$data = [ 'msg' => (new Settings())->getExchangeRates(), 'success' => true ];

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success'=>false] ;
			}finally{
				echo Response::fromApi( $data, 200 );
			
			}
		}


	}

?>
