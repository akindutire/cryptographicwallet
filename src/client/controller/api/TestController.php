<?php
namespace src\client\controller;

use Carbon\Carbon;
use src\client\middleware\Date;
use src\client\middleware\SecureApi;
use src\client\model\ExtraUserInfo;
use src\client\model\Transaction;
use src\client\model\Wallet;
use src\client\service\CoinPaymentTransferProvider;
use \zil\core\server\Param;
use \zil\core\server\Response;
use zil\core\tracer\ErrorTracer;
use zil\factory\Session;
use \zil\factory\View;
use \zil\core\facades\helpers\Notifier;
use \zil\core\facades\helpers\Navigator;
use \zil\core\facades\decorators\Hooks;

use src\client\Config;

/**
 *  @Controller:TestController []
*/

class TestController{

    use Notifier, Navigator, Hooks;


    public function Rates(Param $param){
        try{

//            $id = (new ExtraUserInfo())->getUserId();
//
//            $W = new Wallet;
//
//            $pk = $W->as('w')->with('ExtraUserInfo as ex', 'w.owned_by = ex.id')->filter('w.public_key')->get()->public_key;

//            $today = (Carbon::today())->toDateString();
            var_dump(1);

//            $todayTransactions = (new Transaction())->filter('created_at')->where(['created_at', 'LIKE', $today], [ ['ifrom', $pk, 'OR'], ['ito', $pk] ] )->get();


//            echo (new CoinPaymentTransferProvider())->convert(1);

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function TransStatus(Param $param){
        try{

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function __construct(){}
    public function onInit(Param $param){
        new Date($param);
    }

    public function onAuth(Param $param){
        new SecureApi($param);
    }
    public function onDispose(Param $param){}

}
