<?php
namespace src\adminhub\route;

use \zil\core\interfaces\Route;
use \zil\core\server\Resource as R;

/**
 *   Web Routes
 */

class Web implements Route{

	use \zil\core\facades\decorators\Route_D1;


		/**
		 * Web routes, routes from browser
		 *
		 * @return array
		 */
		public function route(): array{

			return [

				/**
				*	Get Request
				**/

				'login' => (new R('Home@Login'))
					->get()
					->allow('196.27.128.5', '*')
					->alias(''),

				'logout' => (new R('Home@Logout'))->get(),
				'forgot/password' => (new R('Home@ForgotPassword'))->get(),
				'404' => (new R('Home@E404'))->get(),
                '401' => (new R('Home@E401'))->get(),
				'account/change/password/:request_auth_key ' => (new R( 'Home@ChangePwd'))->get(),


				'home' => (new R('DashboardController@Index'))
					->alias('dashboard')
					->get()
					->allow('196.27.128.5', '*'),


				'distributors' => (new R('DashboardController@Affiliate'))->get(),
				'delegation' => (new R('DashboardController@Delegation'))->get(),
				'profile' => (new R('DashboardController@Profile'))->get(),
				'settings' => (new R('DashboardController@Settings'))->get(),
				'pricing' =>(new R('DashboardController@Pricing'))->get(),
				'products/:type_id' => (new R('DashboardController@Products'))->get(),
				'product/of/cats/:cat_id/:product_id' => (new R('DashboardController@ProductofCats'))->get(),
				'products/data/card/pins' => (new R('DashboardController@DataCardsAsAProduct'))->get(),
				'products/airtime/pins' => (new R('DashboardController@AirtimeEPinAsAProduct'))->get(),

				'trade/:trade_type' => (new R('DashboardController@Trades'))->get(),

				'email-marketing' => (new R('DashboardController@EmailMarketing'))->get(),

				'users' => (new R('DashboardController@Users'))->get(),
                'notification' => (new R('DashboardController@Notification'))->get(),

				'requests/cashout' => (new R('DashboardController@CashoutRequests'))->get(),
				'requests/topup' => (new R('DashboardController@TopupRequests'))->get(),
				'requests/airtimetrade' => (new R('DashboardController@AirtimeTradeRequest'))->get(),
				'requests/bitcoin' => (new R('DashboardController@BitcoinTradeRequest'))->get(),

				'delete/account/delegate/:email/:wallet' => (new R('DashboardControllerActionProcessor@DeleteAccount'))
                    ->get(),

				'unfreeze/account/:email' => (new R('DashboardControllerActionProcessor@UnfreezeAccount'))->get(),
				'block/account/delegate/:email' => (new R('DashboardControllerActionProcessor@BlockAccount'))->get(),
				'recess/account/delegate/:email' => (new R('DashboardControllerActionProcessor@RecessAccount'))->get(),
				'prime' => (new R('DashboardController@PrimeUser'))->get()->allow('196.27.128.5'),

				'activity/log/:email' => (new R('DashboardController@ActivityLog'))->get(),

				'enable-cat/:cat_id' => (new R('DashboardControllerActionProcessor@EnableProductCat'))->get(),
				'disable-cat/:cat_id' => (new R('DashboardControllerActionProcessor@DisableProductCat'))->get(),

				'enable-pro/:pro_id' => (new R('DashboardControllerActionProcessor@EnableProduct'))->get(),
				'disable-pro/:pro_id' => (new R('DashboardControllerActionProcessor@DisableProduct'))->get(),

				'delete/data/card/:batch_tag' => (new R('DashboardControllerActionProcessor@DeleteDataCardsByBatch'))
					->get(),
				'delete/airtime/card/pin/:batch_tag' => (new R('DashboardControllerActionProcessor@DeleteAirtimeCardsPinByBatch'))
					->get(),

				// 'account/login' => (new R('HomePostRequestController@LoginAccount'))->get(),

				/**
				*	Post Request
				*/
				'add/account/delegate/:isprime' => (new R('HomePostRequestController@AddAccount'))
					->post()
                    ,

				'account/login' => (new R('HomePostRequestController@LoginAccount'))->post(),

                'settings/exchangerate' => (new R('DashboardControllerActionProcessor@AdjustExchangeRates'))->post()
                    ,

				'settings/:setting_key' => (new R('DashboardControllerActionProcessor@AdjustSettings'))->post()
                    ,

				'act_forgotpwd' => (new R('HomePostRequestController@actionForgotP'))->post(),

				'act_changepwd' => (new R('HomePostRequestController@actionChangeP'))->post(),

				'act_upload_data_card' => (new R('DashboardControllerActionProcessor@UploadDataCard'))->post(),
				'act_edit_data_card_price' => (new R('DashboardControllerActionProcessor@EditDataCardsByBatch'))->post(),

				'act_upload_airtime_card_epin' => (new R('DashboardControllerActionProcessor@UploadAirtimeCardEpin'))->post(),

			];
		}


}

?>
