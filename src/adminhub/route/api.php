<?php
namespace src\adminhub\route;

use \zil\core\server\Resource as R;
use \zil\core\interfaces\Route;

/**
 *   Api Routes
 */

class Api implements Route{

	use \zil\core\facades\decorators\Route_D1;

		/**
		 * Api routes
		 *
		 * @return array
		 */
		public function route(): array{

			$webAsyncRoute = $this->prefix('user/',
				[
					'wallet/:wallet_key' => (new R("UserController@WalletDetails"))->get(),
					'wallet/amountdetails/:wallet_key' => (new R("UserController@WalletAmtDetails"))->get(),
					'account/transaction/islocked' => (new R("UserController@isTransactionLocked"))->get(),
					'passport/via/wallet/:wallet_key' => (new R("UserController@PassportViaWallet"))->get(),
					'subscribe/mail/:email' => (new R('UserController@Subscribe'))->get(),
					'unsubscribe/mail/:email' => (new R('UserController@Unsubscribe'))->get(),
					'transactions/:transaction_state' => (new R('TransactionController@UserTransactions'))->get(),
					'transactions/incoming/:transaction_state' => (new R('TransactionController@UserIncomingTransactions'))->get(),
					'transactions/outgoing/:transaction_state' => (new R('TransactionController@UserOutgoingTransactions'))->get(),
					'transaction/confirm/:transaction_hash' => (new R('TransactionController@ConfirmTransactions'))->get(),
                    'transaction/history/:username' => (new R('TransactionController@GetSpecificClientTransactionHistory'))->get(),

					'requests/cashout/:status' => (new R('UserController@CashoutReq'))->get(),
					'requests/topup/:status' => (new R('UserController@TopupReq'))->get(),
					'requests/airtimetrade/sellingreq/progress' => (new R('UserController@AirtimeTradeSellingReq'))->get(),
					'requests/airtimetrade/sellingreq/completed' => (new R('UserController@AirtimeTradeSellingCompletedReq'))->get(),

					'requests/airtimetrade/buyingreq/progress' => (new R('UserController@AirtimeTradeBuyingReq'))->get(),

					'confirm/payout/:amount/:request_id' => (new R("TransactionController@ConfirmPayout"))->get(),
					'confirm/topup/:amount/:request_id' => (new R("TransactionController@ConfirmTopup"))->get(),
                    'reject/topup/:request_id' => (new R("TransactionController@RejectTopup"))->get(),
					'confirm/airtimetradeinprogress/selling/:request_id' => (new R("TransactionController@ConfirmAirtimeTradeDueSelling"))->get(),
					'confirm/airtimetradeinprogress/buying/:request_id' => (new R("TransactionController@ConfirmAirtimeTradeDueBuying"))->get(),
					'cancel/airtimetradeinprogress/:request_id' => (new R("TransactionController@CancelAirtimeTrade"))->get(),

					'cancel/transaction/:trans_hash' => (new R("TransactionController@CancelTransaction"))->get(),

					'all_contacts' => (new R('UserController@UserListAsAllContacts'))->get(),

					'product/types' => (new R('ProductController@Types'))->get(),

					'product/:type_id' => (new R('ProductController@TypeBasedProducts'))->get(),
					'product/of/cats/:cat_id' => (new R('ProductController@CatsBasedProducts'))->get(),

					'product/cats/:type_id' => (new R('ProductController@TypeBasedCats'))->get(),

					'product/delete/:product_id' => (new R("ProductController@DeleteProduct"))->get(),

                    'notification/get/all' => (new R('UserController@GetAllNotification'))->get(),
                    'notification/getp/:notification_hash' => (new R('UserController@GetSpecificNotification'))->get(),
                    'notification/delete/:notification_hash' => (new R('UserController@DeleteNotification'))->get(),



					/**Post Request Route*/

					'upload/picture' => (new R("UserController@ChangeProfilePic"))->post(),
					'edit/profile'  => (new R("UserController@ChangeProfileDetails"))->post(),
					'edit/pwd'	=>	(new R("UserController@RequestPwdChange"))->post(),
					'transfer/fund/:wallet_key' => (new R("UserController@TransferFund"))->post(),

					'product/edit/:product_id' => (new R("ProductController@EditProduct"))->post(),
					'product/add' => (new R("ProductController@AddProduct"))->post(),

					'notification/send' => (new R('UserController@SendNotification'))->post(),

					'rollback/transaction/:trans_hash' => (new R('TransactionController@RollbackTransaction'))->post(),

					'transaction/history' => (new R('TransactionController@GetSpecificClientTransactionHistory'))->post(),

					'send-mail' => (new R('UserController@SendMailToAllMailingList'))->post()


				]
			);

			return  $this->merge($webAsyncRoute);

		}



}

?>
