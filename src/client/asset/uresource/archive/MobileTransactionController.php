<?php
namespace src\naijasubweb\controller\api;

	use \zil\core\server\Param;
	use \zil\core\server\Response;

    use \zil\factory\View;
	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

	use src\naijasubweb\config\Config;
	 
	use src\naijasubweb\model\Transaction;
 	use zil\factory\Logger;
    use src\naijasubweb\model\Wallet;
    use src\naijasubweb\model\CashoutRequest;
	use src\naijasubweb\model\SalesPoint;
 
	

	class MobileTransactionController{

		use Notifier, Navigator;

		
		public function __construct(){
			header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
            header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
		}

        private function isTokenValid(string $token) : bool{
            if( $token != Session::get('APP_CERT') ){
                return false;
			}else{
				(new SalesPoint())->SalesGC();
				return true;
			}
		}


		public function UserTransactions(Param $param){
			
			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }

				$trans_state = $param->url()->transaction_state;

				$TransactionObj = new Transaction;

				if($trans_state == 'pending'){

					$transactions = $TransactionObj->getPendingTransactions();

				}else if($trans_state == 'confirmed'){

					$transactions = $TransactionObj->getconfirmedTransactions();
				}else if($trans_state == 'rollback'){

					$transactions = $TransactionObj->getRollbackTransactions();
				}else{
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

					array_push($container, $transaction);
				}

				$data = [ 'msg' =>  $container, 'success' => true ];
			}catch(\Throwable $t){
				$data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi( $data, $status);
			}
			

		}
		
		public function UserIncomingTransactions(Param $param){
			
			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$trans_state = $param->url()->transaction_state;

				$TransactionObj = new Transaction;

				if($trans_state == 'pending'){

					$transactions = $TransactionObj->getIncomingPendingTransactions();

				}else if($trans_state == 'confirmed'){

					$transactions = $TransactionObj->getConfirmedTransactions();
				}else if($trans_state == 'rollback'){

					$transactions = $TransactionObj->getRollbackTransactions();
				}else{
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

					array_push($container, $transaction);
				}

				$data = [ 'msg' =>  $container, 'success' => true ];
				
			}catch(\Throwable $t){
				$data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi( $data, $status);
			}

		}
		
		public function UserOutgoingTransactions(Param $param){
			
			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$trans_state = $param->url()->transaction_state;

				$TransactionObj = new Transaction;

				if($trans_state == 'pending'){

					$transactions = $TransactionObj->getOutgoingPendingTransactions();

				}else if($trans_state == 'confirmed'){

					$transactions = $TransactionObj->getConfirmedTransactions();
				}else if($trans_state == 'rollback'){

					$transactions = $TransactionObj->getRollbackTransactions();
				}else{
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
				echo Response::fromApi( $data, $status);
			}

		}

		public function ConfirmTransactions(Param $param){

			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$trans_hash = $param->url()->transaction_hash;

				if( (new Transaction())->confirmTransaction($trans_hash) )
					$data = [ 'msg' =>  'Transaction confirmed', 'success' => true ];
				else
					throw new \Exception("Couldn't confirm transaction, please retry");

			}catch(\Throwable $t){
				$data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi( $data, $status);
			}
			
		}

		public function CashOutFund(Param $param){
			try{

                $status = 200;
                if(!$this->isTokenValid($param->url()->token)){
                    $status = 401;
                    throw new \Exception('401, Bad Authorization , token is invalid');
                }


				$public_key = $param->url()->wallet_key;

				$amount = floatval($param->form()->amount);
				if($amount > 0){

					$CashOutReq = new CashoutRequest;
					if( (new Wallet())->isSufficientBalance($amount) && !$CashOutReq->isTotalProspectCashOutExceedsBalance($amount)){
						

						if ( $CashOutReq->request($amount) )
							$data = [ 'msg' =>  'Cash out request granted, wait for confirmation', 'success' => true ];
						else
							throw new \Exception("Cash out request not granted, please retry");

					}else{
						throw new \Exception("Error: Insufficient fund / Total prospect cash out exceeds wallet");
					}
				}else{
					throw new \Exception("Error: Can\'t cash out NGN{$amount}");
					
				}

			}catch(\Throwable $t){
				$data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi( $data, $status);
			}
		}

	}

?>