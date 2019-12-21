<?php
namespace src\client\controller;

	use src\adminhub\middleware\Date;
	use src\client\middleware\SecureWebPage;
	use src\client\model\EmailValidationTokenLock;
	use src\client\model\Notification;
	use src\client\service\UserLoginAuthSetUp;
	use \zil\core\server\Param;
	use \zil\factory\View;
	use \zil\factory\Session;
	use \zil\core\tracer\ErrorTracer;

	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;
	use \zil\core\facades\decorators\Hooks;
	
	use \zil\security\Encryption;

	use src\client\config\Config;
 
	use src\client\model\User;
	use src\client\model\ExtraUserInfo;
	use src\client\model\Wallet;
	use src\client\model\Membership_plan;

	use src\client\model\Settings;
	use src\client\model\CashoutRequest;
	use src\client\model\Transaction;
	use src\client\model\TopupRequest;
	use src\client\model\SalesPoint;
	use src\client\model\Product_cat;
	use src\client\model\Product_type;
	use src\client\model\Product;

	use src\client\service\DashboardService;


	use \zil\factory\BuildQuery;
	use \zil\factory\Database;
	use \zil\core\scrapper\Info;

	class Dashboard{

		use Notifier, Navigator, Hooks;

		public function CoinPaymentIPN(Param $param){
			try{
				echo "We are processing your payment";
			}catch(\Throwable $t){}
		}

		public function PageNotif(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Dashboard/PageNotif.php", $OutputData);
		}


		public function AccvalidationBVN(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Dashboard/AccvalidationBVN.php", $OutputData);
		}

		public function AccvalidationKYC(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Dashboard/AccvalidationKYC.php", $OutputData);
		}

		public function VerifySmartNo(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Dashboard/VerifySmartNo.php", $OutputData);
		}

		public function Notification(Param $param){

			$OutputData = [
                'Notification' => (new Notification())->getAllNotifs(),
            ];

			#render the desired interface inside the view folder

			View::render("Dashboard/Notification.php", $OutputData);
		}

		public function BuyDataProducts(Param $param){

			// DATA BUNDLE is of id 1
			if( (new Product_type())->isValidType(1) ){

				$OutputData = [
					
					'DataBundleCategory' => (new Product_cat())->getCategories(1),
					'Data_Service_Charge_Rate' =>  (new Settings())->getDataBundleServiceChargeRate()
				];
	
				#render the desired interface inside the view folder
	
				View::render("Dashboard/BuyDataBundle.php", $OutputData);

			}else{
				View::render("Home/501.php", []);
			}
			
		}

		public function PayBills(Param $param){

			$OutputData = [
			    'MeterSmartCard' => isset($param->url()->smartcardormeterno) ? $param->url()->smartcardormeterno : null ,
				'service_id' => $param->url()->service_id,
				'product_id' => $param->url()->product_id,
				'has_product_list' => $param->url()->has_product_list,
				'CableTvCategories' => (new Product_cat())->getCategories(2),
                'MaxAmountToPurchasableForElectricityBill' => (new Settings())->getElectricityBillMaxSale(),
                'MinAmountToPurchasableForElectricityBill' => (new Settings())->getElectricityBillMinSale(),
                'ElectricityBillProducts' => (new Product())->getProductsBasedTypes(3),
				'CableTVTransactionRewardRate' => (new User())->getUserTransactionReward()['DISCOUNT_RATE']['CABLE_TV'],
				'ElectricityChargeRate' => (new Settings())->getElectricityBillChargeRate(),
				'TvChargeRate' => (new Settings())->getCableTvBillChargeRate(),
				'MiscChargeRate' => (new Settings())->getMiscBillChargeRate(),
				'InternetChargeRate' => (new Settings())->getInternetBillChargeRate(),
				];

				
			#render the desired interface inside the view folder

			View::render("Dashboard/PayBills.php", $OutputData);
		}

		public function BuyBulkSMS(Param $param){


			// $db = (new Database())->connect();
			// $sql = new BuildQuery($db);

			// var_dump($sql->create('tist', [null,1000]));

			// echo(Info::$_dataLounge["zdx_0xc4_last_insert_into_tist"]);

			$OutputData = [ 
				
				];

			#render the desired interface inside the view folder

			// View::render("Dashboard/BuyBulkSMS.php", $OutputData);
		}

		public function BuyDataBundle(Param $param){

			if( isset( $param->url()->product_id ) ){
				
				$pid = $param->url()->product_id;
				$network_provider =  $param->url()->network_provider;


				$amount = (new Product())->filter('pcost')->where( ['id', $pid] )->get()->pcost;

				$discountedAmount = $amount;
				$service_charge =  ( (new Settings())->getDataBundleServiceChargeRate() / 100 ) * $discountedAmount;

				$discountedAmount -= $service_charge;


				$OutputData = [ 
					
					'discountAmount' => $discountedAmount,
					'Product_Id' => $pid,
					'Network_Provider' => $network_provider

				];
	
				#render the desired interface inside the view folder
	
				View::render("Dashboard/BuyDataBundle.php", $OutputData);
			}else{
				View::render("Home/501.php", []);
			}

		}

		public function TradeBitcoin(Param $param){

			$OutputData = [ 
				
				'BuyingRate' => (new Settings())->getBitcoinBuyingRate(),
				'SellingRate' => (new Settings())->getBitcoinSellingRate(),

				'BuyingRateInNGN' => (new Settings())->getBitcoinBuyingRateInNGN(),
				'SellingRateInNGN' => (new Settings())->getBitcoinSellingRateInNGN(),

				'BTCToUSD' => (new Settings())->getBTCToUSDRate()
				];

			#render the desired interface inside the view folder

			View::render("Dashboard/TradeBitcoin.php", $OutputData);
		}

		public function Airtime(Param $param){

			$OutputData = [ 
				
				'AirtimeTransactionRewardRate' => (new User())->getUserAirtimePurchaseReward(),
				'AirtimePurchaseDiscountRates' => [
					'MTN' => (new Settings())->getAirtimePurchaseDiscountRate('MTN'),
					'9MOBILE' => (new Settings())->getAirtimePurchaseDiscountRate('9MOBILE'),
					'GLO' => (new Settings())->getAirtimePurchaseDiscountRate('GLO'),
					'AIRTEL' => (new Settings())->getAirtimePurchaseDiscountRate('AIRTEL'),
				],
				'AirtimeSaleServiceChargeRates' => [
					'MTN' => (new Settings())->getAirtimeSaleServiceChargeRate('MTN'),
					'9MOBILE' => (new Settings())->getAirtimeSaleServiceChargeRate('9MOBILE'),
					'GLO' => (new Settings())->getAirtimeSaleServiceChargeRate('GLO'),
					'AIRTEL' => (new Settings())->getAirtimeSaleServiceChargeRate('AIRTEL'),
				],
				'MinSale' => (new Settings())->getAirtimeMinSale(),
				'MaxSale' => (new Settings())->getAirtimeMaxSale(),
				'NetworkProviders' => (new Settings())->getNetworkProviders(),
				'AirtimeSaleReceivingAirtime' => (new Settings())->getAirtimeSalePhone()
			];

			#render the desired interface inside the view folder

			View::render("Dashboard/Airtime.php", $OutputData);
		}

		public function SellGiftCard(Param $param){

			$OutputData = [ 
				'giftPhone' => (new Settings())->getGiftCardPhone()
				];

			#render the desired interface inside the view folder

			View::render("Dashboard/SellGiftCard.php", $OutputData);
		}

	

		public function Downline(Param $param){

			
			$refs = (new User())->getReferrals();

			$reward = (new User())->getTotalReferralReward();

			$OutputData = [ 
				
				'Referrals' => $refs,
				'Ref_Reward' => $reward
				];

			#render the desired interface inside the view folder

			View::render("Dashboard/Downline.php", $OutputData);
		}

		

		public function AccountUpgrade(Param $param){

			$Plans = (new  Membership_plan())->getPlans();

			$OutputData = [ 
				 
				'Plans'=>$Plans ,
				'Previous_Plans' => (new User())->getPreviousPlans(),
			];


			View::render("Dashboard/AccountUpgrade.php", $OutputData);
		}

		public function transactions(Param $param){

				(new SalesPoint())->SalesGC();
				(new Transaction())->TransactionGC();

				$transactions = (new Transaction())->getAllTransactions();
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

				$OutputData = [ 
					 
					'Transactions' =>$container,
					'Topups' => (new TopupRequest())->getTopUps(),	
					'Cashouts' => (new CashoutRequest())->getCashouts(),			
				];


//				View::raw("Dashboard/Transactions.php");
//
//				return;

			View::render("Dashboard/Transactions.php", $OutputData);
		}

		public function topup(Param $param){

			$f = (new ExtraUserInfo())->filter('name', 'phone')->where( ['email', Session::get('email') ] )->get();

			$names = explode(' ', $f->name);

			$fname = $f->name;
			$lname = '';

			if( isset($names[0]))
				$fname = $names[0];

			if ( isset( $names[1] ) )
				$lname = $names[1];

			$OutputData = [ 
				
				'fname' => $fname,
				'lname' => $lname,
				'email' => Session::get('email'),
				'mobile' => $f->phone,

				'banktranshash' => (new Encryption())->generateShortHash()."$".time(),
				'cardtransRef' => sodium_bin2hex((new Wallet())->getPublickey())."$".time(),

				'cardCharge' => (new Settings())->getTopViaCardChargeRate(),
				'airtimeCharge' => (new Settings())->getTopUpViaSNS_AirtimeChargeRate(),
                'NetworkProviders' => (new Settings())->getNetworkProviders(),
                'MinTopUpAmountViaBank' => (new Settings())->getMinTopUpThroughBank(),
				'MinTop' => (new Settings())->getMinTopUp(),
				'CompanyBankDetails' => (new Settings())->getCompanyBankDetails()
			];

			
			View::render("Dashboard/WalletTopup.php", $OutputData);
		}

		public function account(Param $param){

			(new SalesPoint())->SalesGC();
			// (new Transaction())->TransactionGC();

			$OutputData = [ 'DashboardTemplateData' =>  (new DashboardService())->getDashboardTemplateData() ];
			View::render("Dashboard/Account.php", $OutputData);
		}
		
		public function index(Param $param){

			
			$UsrExistCount = ExtraUserInfo::filter('id', 'email')->where( ['username', Session::get('username') ])->count();
			

			if( $UsrExistCount == 1 ){
				
				$OutputData = [ 'DashboardTemplateData' => (new DashboardService())->getDashboardTemplateData() ]; 
				
				

				#render the desired interface inside the view folder
				 View::render("Dashboard/index.php", $OutputData);

				unset($CryptoUser, $User, $WalletObj);

			}else{
				(new UserLoginAuthSetUp())->destroyGuard();
			}

		}


		public function __construct(){
			try{

			}catch(\Throwable $t){
				new ErrorTracer($t);
			}
		}

		public function onInit(Param $param)
		{
			new Date($param);
			(new Transaction())->TransactionGC();
		}

		public function onAuth(Param $param)
		{

			new SecureWebPage($param);


		}


	}

?>
