<?php
namespace src\client\route;

use \zil\core\interfaces\Route;
use \zil\core\facades\decorators\Route_D1;
use zil\core\server\Resource;

/**
 *   Api Routes
 */

class Api implements Route{

	use Route_D1;
	
		private const VERIFYTRADELEGIBILITY = 'SpecialRestrictions';
		private const EPinTradeSubscriptionMinimumRequirementAssert = 'EPinTradeSubscriptionMinimumRequirementAssert';

		/**
		 * Api routes
		 *
		 * @return array
		 */
		public function route(): array{

			$webAsyncRoute = $this->prefix('user/',
				[

                    'wallet/balance/:token' => (new Resource("WalletApiController@WalletBalance"))
                        ->middleware()
                        ->get(),

                    'wallet/:token' => (new Resource("WalletApiController@WalletDetails"))
                        ->middleware()
                        ->get(),

                    'passport/:token' => (new Resource("UserController@Passport"))
                        ->middleware()
                        ->get(),

                    'passport/via/wallet/:wallet_key/:token' => (new Resource("UserController@PassportViaWallet"))
                        ->middleware()
                        ->get(),

                    'account/transaction/islocked/:token' => (new Resource("TransactionController@isTransactionLocked"))
                        ->middleware()
                        ->get(),

                    'subscribe/mail/:email/:token' => (new Resource('UserController@Subscribe'))
                        ->middleware()
                        ->get(),
                    
                    'unsubscribe/mail/:email/:token' => (new Resource('UserController@Unsubscribe'))
                        ->middleware()
                        ->get(),

					'subscribe/mail/:email' => (new Resource('UserController@Subscribe'))
						->middleware()
						->get(),

					'unsubscribe/mail/:email' => (new Resource('UserController@Unsubscribe'))
						->middleware()
						->get(),

                    'transactions/:transaction_state/:token' => (new Resource('TransactionController@UserTransactions'))
                        ->middleware()
						->get(),

					'transactions/incoming/:transaction_state/:token' =>(new Resource('TransactionController@UserIncomingTransactions'))
                        ->middleware()
						->get(),

                    'transactions/outgoing/:transaction_state/:token' => (new Resource('TransactionController@UserOutgoingTransactions'))
                        ->middleware()
						->get(),

                    'transaction/confirm/:transaction_hash/:token' => (new Resource('TransactionController@ConfirmTransactions'))
                        ->middleware()
						->get(),

                    'cashouts/:token' => (new Resource('WalletApiController@Cashouts'))
                        ->middleware()
                        ->get(),
                    
                    'topups/:token' => (new Resource('WalletApiController@Topups'))
                        ->middleware()
                        ->get(),

                    'account/plan/levels/:token' => (new Resource("UserController@GetAccountPlanLevels"))
						->middleware()
                        ->get(),

                    'upgrade/account/:plan_level/:token' => (new Resource("UserController@UpgradeAccount"))
                        ->middleware()
                        ->get(),
                    
                    'use/account/:plan_level/:token' => (new Resource("UserController@UseAccount"))
                        ->middleware()
                        ->get(),

                    'referrals/:token' => (new Resource('UserController@Referrals'))
                        ->middleware()
						->get(),

                    'trade/exchange/rates/:token' => (new Resource('TradeController@getTradeExchangeRates'))
                        ->middleware( )
                        ->get(),

                    'probe/trade/completion/bitcoin/:proof_of_trade/:token' => (new Resource('BitcoinApiController@ProbeBitcoinTransfer'))
                        ->middleware()
                        ->get(),

					'product/types/:token' => (new Resource('ProductController@AllTypes'))
						->middleware()
                        ->get(),

					'cat/of/:type_id/:token' => (new Resource('ProductController@AllCategoriesOfThisType'))
						->middleware()
                        ->get(),

                    'product/of/cats/:cat_id/:token' => (new Resource('ProductController@CatsBasedProducts'))
                        ->middleware()
                        ->get(),

                    'conversionrate/:token' => (new Resource('TransactionController@ConversionRate'))
                        ->middleware()
                        ->deny('*')
                        ->get(),

                    'notification/read/:notification_hash/:token' => (new Resource('NotificationApiController@ReadNotification'))
                        ->middleware()
                        ->get(),

                    'notification/unread/count/:token' => (new Resource('NotificationApiController@GetUnReadNotificationCount'))
                        ->middleware()
                        ->get(),

					'notification/:token' => (new Resource('NotificationApiController@GetAllNotification'))
						->middleware()
                        ->get(),

					'hotkeys/:type/:token' => (new Resource('UserController@GetHotkeys'))
						->middleware()
                        ->get(),

                    'topup/via/card/:amount/:transactionRef/:token' => (new Resource("WalletApiController@TopUpViaCard"))
                        ->middleware()
                        ->get(),

					'cancel/cashout/:cashout_id/:token' => (new Resource('WalletApiController@ExCancelCashoutRequest'))
                        ->middleware()
                        ->get(),
                    
                    'cancel/topup/:topup_id/:token' => (new Resource('WalletApiController@ExCancelTopupRequest'))
                        ->middleware()
                        ->get(),

                    'bill/services/:token' => (new Resource('ProductController@GetBillServicesList'))
						->middleware()
                        ->get(),

					'bill/services/:type/:token' => (new Resource('ProductController@GetBillServicesListBasedType'))
						->middleware()
                        ->get(),

					'bill/product/options/:service_id/:product_id/:has_product_list/:token' => (new Resource('ProductController@GetBillProductOptions'))
						->middleware()
                        ->get(),

                    'destroy/:token' => (new Resource('AuthController@Logout'))
                        ->middleware()
                        ->get(),



					/**Todo: Add to postman api docs*/
					'verify/email/:token' => (new Resource('UserController@VerifyEmail'))
						->middleware()
						->get(),


					/**Post Request**/

					/**@Todo: Add to postman api documentation*/
					'validate/bvn/:token' => (new Resource("UserController@ValidateBvn"))
						->middleware()
						->post(),


					/**
					 * @Todo Add to postman docs
					 */
					'apply/as/data/card/reseller/:token' => (new Resource('DataTradeApiController@ApplyAsDataCardReseller'))
						->post(),

					/**
					 * @Todo Add to postman docs
					 */
					'buy/data/epin/:token' => (new Resource('DataTradeApiController@BuyDataEPin'))
						->middleware(self::EPinTradeSubscriptionMinimumRequirementAssert)
						->post(),
					/**
					 * @Todo Add to postman docs
					 */
					'buy/airtime/epin/:token' => (new Resource('AirtimeApiController@BuyAirtimeEPin'))
						->middleware(self::EPinTradeSubscriptionMinimumRequirementAssert)
						->post(),

					/**
					 * @Todo Add to postman docs
					 */
					'load/data/card/epin/:token' => (new Resource('DataTradeApiController@LoadEPin'))->post(),

					/**
					 * @Todo Add to postman docs
					 */
					'subscribe/epin/:token' => (new Resource('DashboardExtension@SubscribeForEPin'))->get(),
					/**
					 * @Todo Add to postman docs
					 */
					'calculate/data/card/epin/per/unit/:token' => (new Resource('DataTradeApiController@CalculateDataCardUnitPrice'))->post(),
					/**
					 * @Todo Add to postman docs
					 */
					'calculate/airtime/card/epin/per/unit/:token' => (new Resource('AirtimeApiController@CalculateAirtimeCardUnitPrice'))->post(),


					'auth' => (new Resource("AuthController@Auth"))->post(),

                    'signup' => (new Resource("AuthController@SignUp"))->alias('signup?referral=:referral')
                        ->post(),

                    'upload/picture/:token' => (new Resource("UserController@ChangeProfilePic"))
                        ->middleware()
                        ->post(),

					'edit/profile/:token'  => (new Resource("UserController@ChangeProfileDetails"))
                        ->middleware()
                        ->post(),

                    'edit/pwd'	=>	(new Resource("UserController@RequestPwdChange"))
                        ->middleware()
                        ->post(),


                    'transfer/fund/:wallet_key/:token' => (new Resource("TransactionController@TransferFund"))
                        ->middleware()->post(),


                    'cashout/fund/:wallet_key/:token' => (new Resource("WalletApiController@CashOutFund"))
                        ->middleware(self::VERIFYTRADELEGIBILITY)
						->post(),

                    'topup/via/sharensell/:token' => (new Resource("WalletApiController@RequestTopUpViaShareNSell"))
                        ->middleware()
                        ->post(),

                    'topup/via/airtimepin/:token' => (new Resource("WalletApiController@RequestTopUpViaAirtimePin"))
                        ->middleware()
                        ->post(),

                    'topup/via/bank/:token' => (new Resource("WalletApiController@RequestTopUpViaBank"))
                        ->middleware()
                        ->post(),

                    'make/trade/giftcard/save/proofoftrade/:token' => (new Resource('GiftCardApiController@SaveGiftCardProofOfTrade'))
                        ->allow('*')
                        ->post(),

                    'make/trade/giftcard/:token' => (new Resource('GiftCardApiController@GiftCard'))
                        ->allow('*')
                        ->post(),

                    'make/trade/bitcoin/buy/:token' => (new Resource('BitcoinApiController@BuyBitcoinFromNaijaSub'))
                        ->middleware()
                        ->post(),

                    'make/trade/bitcoin/sell/:token' => (new Resource('BitcoinApiController@SellBitcoinToNaijaSub'))
                        ->middleware()
                        ->post(),

					'verify/smartcard/:token' => (new Resource('BillApiController@VerifySmartCard'))
						->middleware()
                        ->post(),

                    'pay/bill/electricity/:product_id/:token' => (new Resource('BillApiController@PayElectricityTopUp'))
                        ->middleware()
                        ->allow('*')
                        ->post(),

                    'pay/bill/nonelectricity/:service_id/:product_id/:token' => (new Resource('BillApiController@PayNonElectricityBill'))
                        ->middleware()
                        ->allow('*')
                        ->post(),

                    'buy/data/:token' => (new Resource('DataTradeApiController@BuyDataBundle'))
                        ->middleware()
                        ->allow('*')
                        ->post(),

                    'buy/airtime/:token' => (new Resource('AirtimeApiController@BuyAirtime'))
                        ->middleware()
                        ->allow('*')
                        ->post(),

                    'sell/airtime/via/sharensell/:token' => (new Resource('AirtimeApiController@SellAirtimeViaSNS'))
                        ->middleware( self::VERIFYTRADELEGIBILITY)
						->post(),


					'sell/airtime/via/airtimepin/:token' => (new Resource('AirtimeApiController@SellAirtimeViaPIN'))
                        ->middleware(self::VERIFYTRADELEGIBILITY)
						->post(),


                ]
			);

			$coinPaymentRoute = $this->prefix(
				'cptp/',
				[
					'ipn' => (new Resource('Dashboard@CoinPaymentIPN'))->get(),
					'rate' => (new Resource('TestController@Rates'))->get()
				]
			);


			return  $this->merge($webAsyncRoute, $coinPaymentRoute);
		}


}

?>
