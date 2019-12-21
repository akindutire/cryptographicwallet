<?php
namespace src\naijasubweb\controller\api;

	use \zil\core\server\Param;
	
	use \zil\factory\View;
	use \zil\factory\Session;
	use \zil\factory\Mailer;

	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

	use \zil\security\Validation;
	use \zil\security\Encryption;
	use \zil\security\Sanitize;

	use \zil\core\server\Response;

	use src\naijasubweb\config\Config;
	use src\naijasubweb\service\DashboardService;
	use src\naijasubweb\service\MailService;

	use src\naijasubweb\model\User;
	use src\naijasubweb\model\ExtraUserInfo;
	use src\naijasubweb\model\Wallet;
	use src\naijasubweb\model\Transaction;

	
	use Carbon\Carbon;
 	use src\naijasubweb\model\Settings;

	use src\naijasubweb\model\SalesPoint;
 
 	
	class MobileTradeController extends TradeController{

		use Notifier, Navigator;

		public function __construct(){
			header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
            header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
		}

        public function getTradeExchangeRates(){
            try{
                
                $rates = (new Settings())->getExchangeRates();
                $data = [ 'msg' => $rates, 'success' => true ];

            }catch(\Throwable $t){
                $data = [ 'msg' => ['error' => $t->getMessage()], 'success' => false ];
            }finally{
                echo Response::fromApi($data, 200);
            }
        }

		
























		/**
		 * Currently Suspended Trade for the meantime
		 *
		 * @param Param $param
		 * @return void
		 */
		public function SaveGiftCardProofOfTrade(Param $param){
            try{
				$response = (new SalesPoint())->uploadGiftCardProofOfTrade($_FILES['file']);

				if($response['status'] == true){
					$data = [ 'proofoftrade' => $response['proofsrc'], 'proofoftradename' => $response['proofname'], 'success' => true ];
					

				}else{
					$data = [ 'msg' => $response['error'], 'success' => false ];
				}

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, 200);
			}
				
		}

		/**
		 * Currently Suspended Trade for the meantime
		 *
		 * @param Param $param
		 * @return void
		 */
        public function GiftCard(Param $param){
            try{
                
                $Validation = new Validation( ['giftcard_amount', 'number|required|min:0'], ['giftcard_type', 'text|required'], ['giftcard_proofoftrade', 'text|required'] );

                if($Validation->isPassed()){

                    $SalesPoint = new SalesPoint;

                    $SalesPoint->trade_key = (new Encryption())->generateShortHash();
                    $SalesPoint->trade_type = (new Transaction())->getTransactionTypes('GIFTCARD_TRADE');
                    $SalesPoint->ifrom_address = sodium_bin2hex( (new Wallet())->getPublickey() );
                    $SalesPoint->ito_address = sodium_bin2hex( (new Wallet())->getAnyAdminPublickey() );
                    $SalesPoint->valueorqtyexchanged = 1;
                    $SalesPoint->extracharge = 0;
                    $SalesPoint->rawamt = floatval($param->form()->giftcard_amount);
                    $SalesPoint->icurrency = 'USD';
                    $SalesPoint->proofoftrade = striptags($param->form()->giftcard_proofoftrade);
                    $SalesPoint->proofoftradeformat = 'IMAGE';
                    $SalesPoint->status = $SalesPoint->getTradeStatus('PROGRESS');
                    $SalesPoint->created_at = Carbon::now();
                    $SalesPoint->updated_at = Carbon::now();

                    if ( $SalesPoint->create() ){
                        $data = [ 'msg' => "Trade completed, Please wait while trade is being confirmed", 'success' => true ]; 
                    }else{
                        throw new \Exception("Trade not accomplished, Please retry");
                    }

                }else{
                    $data = [ 'msg' => $Validation->getError(), 'success' => false ];
                }

			}catch(\Throwable $t){
				$data = [ 'msg' => ['error' => $t->getMessage()], 'success' => false ];
			}finally{
				echo Response::fromApi($data, 200);
			}
        }
	}

?>