<?php
namespace src\client\service;

	use \zil\factory\Database;
	use \zil\factory\BuildQuery;
	use \zil\factory\Session;
	use \zil\factory\Fileuploader;
	use \zil\factory\Filehandler;
	use \zil\factory\Logger;
	use \zil\factory\Mailer;
	use \zil\factory\Redirect;

	use \zil\security\Authentication;
	use \zil\security\Encryption;
	use \zil\security\Sanitize;

	use src\client\config\Config;



	class BlockIOCoinTransferProvider{

		private $apikey = '';
		private $version = 2;
		private $pin = 'oshegz000';

		public function __construct(){
			require_once('dependency/block_io-php/lib/block_io.php');
		}

		private function setApikey(){

			// BTC Testnet
//			$this->apikey = 'c0bc-a59e-b4d6-02d1';

			// Bitcoin
			 $this->apikey = 'c808-6483-a7ac-47e3';
		}

		public function getDefaultBTCAddress() : string {
			// BTC Testnes
			// return '2MsQf2RaKu9k2YyAMX6feNiLRATzzZ3AeC2';

			// Bitcoin
			return '3DDRBrTPkfNV5URqt4Zk21sZP7uVcKbERA';
		}

		public function ProposeBitcoinAcceptance(): string {

			$this->setApikey();

			$block_io = new \BlockIo($this->apikey, $this->pin, $this->version);

			$newAddressInfo = $block_io->get_new_address();

			$dynamicAddress = $newAddressInfo->data->address;


			return $dynamicAddress;

		}

		public function ProposeBitcoinWithdrawal(string $pay_to_address, float $btc ):bool{

			$this->setApikey();

			$block_io = new \BlockIo($this->apikey, $this->pin, $this->version);

			$balance = $block_io->get_address_balance();

			if($balance->data->available_balance > 0.0002){

				$w = $block_io->withdraw( ['amounts' => "{$btc}", 'to_addresses' => "{$pay_to_address}"]);

				// Logger::Init();
				// 	Logger::Log($w);
				// Logger::kill();

				if($w->status == 'success')
					return true;
				else
					return false;

			}else{
				return false;
			}


		}

		public function isBitcoinTransfered( string $proof_of_trade, float $expectingAmt ):bool{


			$this->setApikey();

			$block_io = new \BlockIo($this->apikey, $this->pin, $this->version);
			$balance = $block_io->get_address_balance(array('addresses' => "{$proof_of_trade}"));

			Logger::Init();
				Logger::Log($balance->data);
			Logger::kill();

			if($balance->data->balances[0]->available_balance >= $expectingAmt)
				return true;
			else
				return false;

		}

		public function getBalance( ?string $address = null ):float{

			$this->setApikey();

			$block_io = new \BlockIo($this->apikey, $this->pin, $this->version);



			if(!is_null($address)){
			    $proof_of_trade = $address;
				$balance = $block_io->get_address_balance(array('addresses' => "{$proof_of_trade}"));
				return floatval( $balance->data->balances[0]->available_balance );
			}else{
				$balance = $block_io->get_address_balance();
				return floatval( $balance->data->available_balance );
			}

		}

		public function archiveAddress(array $address){

			$this->setApikey();

			if( sizeof($address) > 0 ){
				$addr = implode(',', $address);
				$block_io = new \BlockIo($this->apikey, $this->pin, $this->version);
				$block_io->archive_addresses( ['addresses' => $addr] );
			}

		}

		public function isAddressValid(string $address):bool{
			$this->setApikey();

			$block_io = new \BlockIo($this->apikey, $this->pin, $this->version);

			return $block_io->is_valid_address(array('address' => "{$address}"))->data->is_valid;

		}
	}

?>
