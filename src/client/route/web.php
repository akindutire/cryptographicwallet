<?php
namespace src\client\route;

use \zil\core\interfaces\Route;
use zil\core\server\Resource;

/**
 *   Web Routes
 */

class Web implements Route{

	use \zil\core\facades\decorators\Route_D1;


		private const SECUREWEBPAGE = 'SecureWebPage';
		private const EPinTradeSubscriptionMinimumRequirementAssert = 'EPinTradeSubscriptionMinimumRequirementAssert';
		/**
		 * Web routes, routes from browser
		 *
		 * @return array
		 */
		public function route(): array{

			return [

				'home' => (new Resource('Home@index'))
					->alias('')
					->allow('*')
					->get(),

				'login' => (new Resource('Home@login'))
					->alias('signin')
					->allow( '*')
					->get(),

				'logout' => (new Resource('Home@logout'))->get(),

				'register' => (new Resource('Home@signup'))
					->alias( 'signup' ,'register?referral=:referral', 'register/:referral')
					->get(),

				'forgot/password' => (new Resource('Home@ForgotPwd'))->get(),
				'faq' => (new Resource('Home@Faq'))->get(),

				'service' => (new Resource('Home@Service'))->alias('services')->get(),

				'termsandcondition' => (new Resource('Home@TermsAndCondition'))
					->alias('terms')
					->get(),

				'aboutus' => (new Resource('Home@AboutUs'))->get(),

				'contactus' => (new Resource('Home@ContactUs'))->get(),
				'pricing' => (new Resource('Home@Pricing'))->get(),

				'404' => (new Resource('Home/404'))
					->asView()
					->get(),

				'503' => (new Resource('Home/503'))
					->asView()
					->get(),

				'401' => (new Resource('Home/401'))
					->asView()
					->get(),

				'501' => (new Resource('Home/501'))
					->asView()
					->get(),


				'buk' => (new Resource('Dashboard@BuyBulkSMS'))
					->allow('196.27.128.5')
					->get(),


				'dev/migrate/:migration_name/:rollback' => (new Resource('Migration@Run'))
					->get(),

				'dev/scaffold/:m/:t' => (new Resource('Migration@Scaffold'))
					->get(),


                'activate/token/as/session/app/cert/:email/:token' => (new Resource("Home@ActivateAppCertAsToken"))->get(),

				'dashboard' => (new Resource('Dashboard@index'))
					->allow( '*')
					->get(),


				'bulk/airtime' => (new Resource('DashboardExtension@BulkAirtime'))
					->allow('*')
					->get(),

				'account' => (new Resource('Dashboard@account'))
					->get(),

				'account/upgrade' => (new Resource('Dashboard@AccountUpgrade'))
					->get(),

				'account/kyc' => (new Resource('Dashboard@AccvalidationKYC'))
					->allow('*')
					->get(),

				'account/validate' => (new Resource('Dashboard@AccvalidationBVN'))
					->allow('*')
					->get(),

				'account/change/password/:request_auth_key' => (new Resource('Home@ChangePwd'))->get(),


				'wallet/topup' => (new Resource('Dashboard@topup'))
					->allow('*')
					->get(),


				'wallet/cashout' => (new Resource('Dashboard@cashout'))->get(),

				'transactions' => (new Resource('Dashboard@transactions'))
					->alias('transactions/all')
					->get(),

				'downline' => (new Resource('Dashboard@Downline'))->get(),

				'sell/giftcard' => (new Resource('Dashboard@SellGiftCard'))->get(),

                'trade/bitcoin' => (new Resource('Dashboard@TradeBitcoin'))->get(),

				'databundle/product' => (new Resource('Dashboard@BuyDataProducts'))->get(),

				'airtime' => (new Resource('Dashboard@Airtime'))->allow('*')->get(),

				'buy/data/:product_id' => (new Resource('Dashboard@BuyDataBundle'))
					->get(),

				'epin' => (new Resource('DashboardExtension@EPin'))
					->get(),

				'affiliate/data/card/reseller/apply' => (new Resource('DashboardExtension@DataCardReseller'))
					->get(),

				'view/application/details' => (new Resource('DashboardExtension@OpenApplicationDetails'))
					->get(),

				'buy/data/card/e-pin' => (new Resource('DashboardExtension@BuyDataCard'))
					->middleware(self::EPinTradeSubscriptionMinimumRequirementAssert)
					->get(),

				'info/data/card/e-pin' => (new Resource('DashboardExtension@DataEPinInfo'))
					->get(),

                'load/data/card/e-pin' => (new Resource('DashboardExtension@LoadDataEPin'))
                    ->middleware(self::EPinTradeSubscriptionMinimumRequirementAssert)
                    ->get(),

				'buy/airtime/e-pin' => (new Resource('DashboardExtension@BuyAirtimeEPinOrAirtimeCard'))
					->middleware(self::EPinTradeSubscriptionMinimumRequirementAssert)
					->get(),

				'subscribe/epin' => (new Resource('DashboardExtension@SubscribeForEPin'))->get(),

				'buy/sms' => (new Resource('Dashboard@BuyBulkSMS'))->get(),

                'select/bill' => (new Resource('Dashboard@VerifySmartNo'))
					->get(),


				'pay/bills/:smartcardormeterno/:product_id/:service_id/:has_product_list'
				=> (new Resource('Dashboard@PayBills'))
					->alias('pay/bills/:product_id/:service_id/:has_product_list')
					->get(),


				'cancel/cashout/:cashout_id' => (new Resource('api/WalletApiController@CancelCashoutRequest'))->get(),
				'cancel/topup/:topup_id' => (new Resource('api/WalletApiController@CancelTopupRequest'))->get(),

                'notification' => (new Resource('Dashboard@Notification'))->get(),

				'verify/account/:email/:verification_key' => (new Resource('Home@VerifyEmail'))->get(),




				/**Post Request*/

				'act_registeruser' => (new Resource('Home@actionRegisterUser'))->post(),
				'act_forgotpwd' => (new Resource('Home@actionForgotP'))->post(),
				'act_changepwd' => (new Resource('Home@actionChangeP'))->post(),
				'act_contactus' => (new Resource('Home@actionContactUs'))->post(),

			];

		}


}

?>
