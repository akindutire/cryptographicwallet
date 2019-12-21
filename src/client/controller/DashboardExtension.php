<?php
        namespace src\client\controller;

        use src\adminhub\middleware\Date;
        use src\client\middleware\SecureWebPage;
        use src\client\middleware\TransactionDailyCounterAndRestrictionChecker;
        use src\client\model\Affiliate;
        use src\client\model\Authtoken;
        use src\client\model\DataCardCustomer;
        use src\client\model\ExtraUserInfo;
        use src\client\model\Product_cat;
        use src\client\model\Settings;
        use src\client\model\Transaction;
        use src\client\model\Wallet;
        use src\client\service\DashboardService;
        use zil\core\scrapper\Info;
        use \zil\core\server\Param;
        use \zil\core\server\Response;
        use zil\factory\Utility;
        use \zil\factory\View;
        use \zil\core\facades\helpers\Notifier;
        use \zil\core\facades\helpers\Navigator;
        use \zil\core\facades\decorators\Hooks;

        use src\client\Config;

        /**
         *  @Controller:DashboardExtensionForAffiliate []
        */

        class DashboardExtension{

            use Notifier, Navigator, Hooks;


			public function DataEPinInfo(Param $param){

				$OutputData = [];

				#render the desired interface inside the view folder

				View::render("DashboardExtension/DataEPinInfo.php", $OutputData);
			}

			public function LoadDataEPin(Param $param){

                $OutputData = [
                    'DataBundleCategory' => (new Product_cat())->getCategories(1),
                    'network_providers' => (new Settings())->getNetworkProviders()
                ];


                #render the desired interface inside the view folder

				View::render("DashboardExtension/LoadDataEPin.php", $OutputData);
			}

			public function OpenApplicationDetails(Param $param){

                $Af = new Affiliate();

                $user_id = (new ExtraUserInfo())->getUserId();

				$OutputData = [
				    'my-reseller-biz-details' => $Af->where( ['user_id', $user_id] )->get('VERBOSE')
                ];

				#render the desired interface inside the view folder

				View::render("DashboardExtension/OpenApplicationDetails.php", $OutputData);
			}

			public function SubscribeForEPin(Param $param){
                try {

                    if (Info::getRouteType() == 'api'){
                        $token = $param->url('token');
                        if ( (new Authtoken())->isValid($token) == false )
                            throw new \Exception("Bad authorization");
                    }

                    $EU = new ExtraUserInfo();

                    $id = $EU->getUserId();
                    $Ur = $EU->as('e')
                        ->with('User as u', 'e.email = u.email')
                        ->with('Membership_plan as mp', 'u.membership_plan_id = mp.id')
                        ->filter('e.email', 'mp.tag')
                        ->where( ['e.id', $id] )->get();

                    $email = $Ur->email;
                    $tag = trim($Ur->tag);



                    $DataCardSubscriptionFee = (new Settings())->getMinimumBalanceRequirementDataCardReseller();

                    $T = new Transaction();
                    $W = new Wallet();


                    if($tag == 'DEALER'){

                        $DCC = new DataCardCustomer();
                        $DCC->customer_id = (new ExtraUserInfo())->getUserId();
                        $DCC->create();
                    }elseif ( (new DashboardService())->isRecognizedAsDataCardCustomer() ) {
                        return;
                    }else{
                        $pk = $W->getPublickey();
                        $from_prime_pk = $W->filter('public_key', 'private_key', 'balance')->where(['public_key', sodium_bin2hex($pk)])->get();

                        $userBalance = $from_prime_pk->balance;

                        if ($userBalance < $DataCardSubscriptionFee)
                            throw new \Exception("Insufficient balance");

                        $payment_meta_info = [
                            'type' => (new Transaction())->getTransactionTypes('E_PIN_ONE_TIME_FEE'),
                            'to_address' => $W->getAnyAdminPublickey(),
                            'from_address' =>
                                [
                                    'pubk' => sodium_hex2bin($from_prime_pk->public_key),
                                    'prik' => sodium_hex2bin($from_prime_pk->private_key)
                                ]
                        ];
                        $ServiceTrans = $T->addServiceTrans(
                            $payment_meta_info['type'],
                            'CONFIRMED',
                            [
                                $payment_meta_info['from_address']['pubk'],
                                $payment_meta_info['from_address']['prik']
                            ],
                            $payment_meta_info['to_address'],
                            $DataCardSubscriptionFee
                        );


                        if ($ServiceTrans == true) {

                            $DCC = new DataCardCustomer();
                            $DCC->customer_id = (new ExtraUserInfo())->getUserId();
                            $DCC->create();

                        }else{
                            throw new \Exception("Subscription failed");
                        }
                    }


                    $proceedLink = Utility::route('buy/data/card/e-pin');
                    $data = [ 'message' => "Subscription completed.<br> <a class='btn btn-success' href='{$proceedLink}'>Proceed</a>", 'success' => true ];

                    return;

                } catch (\Throwable $t){

                    $data = [ 'message' => $t->getMessage(), 'success' => false ];

                } finally {

                    if(Info::getRouteType() == 'api')
                        Response::fromApi($data, 200);
                    else
                        View::render("Dashboard/PageNotif.php", $data);
                }
			}

            public function EPin(Param $param){

                /**
                 * Deduct min. req amount for DC JJC, except reseller and dealer
                 */

                $Af = new Affiliate();
                $user_id = (new ExtraUserInfo())->getUserId();

                $OutputData = [
                    'network_providers' => (new Settings())->getNetworkProviders(),
                    'my-reseller-biz-details' => $Af->where( ['user_id', $user_id] )->get('VERBOSE')
                ];

                #render the desired interface inside the view folder


                View::render("DashboardExtension/EPin.php", $OutputData);
            }

			public function BuyAirtimeEPinOrAirtimeCard(Param $param){

				$OutputData = [
                    'AirtimeCategory' => (new Product_cat())->getCategories(4),
                    'network_providers' => (new Settings())->getNetworkProviders(),
                    'MinUnitForPinCustomization' => (new Settings())->getMinUnitForPinCustomization(),
                ];

				#render the desired interface inside the view folder

				View::render("DashboardExtension/BuyAirtimeEPinOrAirtimeCard.php", $OutputData);
			}



			public function BuyDataCard(Param $param){

			    $S = new Settings();

				$OutputData = [
                    'DataBundleCategory' => (new Product_cat())->getCategories(1),
                    'network_providers' => (new Settings())->getNetworkProviders(),
                    'MinUnitForPinCustomization' => (new Settings())->getMinUnitForPinCustomization(),
                    'UnitPrice' => [
                        'MTN' => $S->getDataEPinPriceTag('MTN'),
                        'GLO' => $S->getDataEPinPriceTag('GLO'),
                        '9MOBILE' => $S->getDataEPinPriceTag('9MOBILE'),
                        'AIRTEL' => $S->getDataEPinPriceTag('AIRTEL'),
                    ]
                ];

				#render the desired interface inside the view folder

				View::render("DashboardExtension/BuyDataCard.php", $OutputData);
			}

			public function DataCardReseller(Param $param){

				$OutputData = [
                    'minReq' => (new Settings())->getMinimumBalanceRequirementDataCardReseller()
                ];

				#render the desired interface inside the view folder

				View::render("DashboardExtension/DataCardReseller.php", $OutputData);
			}

			public function Index(Param $param){

				$OutputData = [];

				#render the desired interface inside the view folder

				View::render("DashboardExtension/Index.php", $OutputData);
			}

            public function __construct(){}
            public function onInit(Param $param){

                new Date($param);
                (new Transaction())->TransactionGC();

            }

            public function onAuth(Param $param){
                new SecureWebPage($param);
            }
            public function onDispose(Param $param){}

        }
