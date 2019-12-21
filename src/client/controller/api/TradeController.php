<?php
namespace src\client\controller\api;

	use src\client\middleware\Date;
	use src\client\middleware\SecureApi;
	use src\client\model\Membership_plan;
	use src\client\service\SmsProvider;
	use zil\core\facades\decorators\Hooks;
	use \zil\core\server\Param;

	use \zil\factory\Database;
	use \zil\factory\Logger;

	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;
	use \zil\core\server\Response;

use zil\factory\Utility;
use \zil\security\Validation;
	use \zil\security\Encryption;
	use \zil\security\Sanitize;


	use src\client\service\MailService;
	use src\client\service\BlockIOCoinTransferProvider;
	use src\client\service\Bill;

	use src\client\model\User;
	use src\client\model\AuthToken;
	use src\client\model\ActivityLog;
	use src\client\model\ExtraUserInfo;
	use src\client\model\Wallet;
	use src\client\model\Transaction;
	use src\client\model\Settings;
	use src\client\model\SalesPoint;
	use src\client\model\Product;

	use Carbon\Carbon;
	use Exception;

	class TradeController{

		use Notifier, Navigator, Hooks;

		public function __construct(){

		    header('Access-Control-Allow-Origin: *');
//            header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
            header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

		}

		public function onAuth(Param $param)
		{
			new SecureApi($param);
			new Date($param);
		}


        public function getTradeExchangeRates():void{
            try{

                $rates = (new Settings())->getExchangeRates();
                $data = [ 'msg' => $rates, 'success' => true ];

            }catch(\Throwable $t){
                $data = [ 'msg' =>  $t->getMessage(), 'success' => false ];
            }finally{
                echo Response::fromApi($data, 200);
            }
        }



	}

?>
