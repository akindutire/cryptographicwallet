<?php

namespace src\client\middleware;

use Carbon\Carbon;
use src\client\model\Settings;
use src\client\model\Transaction;
use src\client\model\Wallet;
use src\client\service\DashboardService;
use zil\core\interfaces\Middleware;
use zil\core\interfaces\Param;
use zil\core\scrapper\Info;
use zil\core\server\Response;
use zil\core\tracer\ErrorTracer;
use zil\factory\Logger;
use zil\factory\View;

/**
 * @Middleware:TransactionDailyCounterAndRestrictionChecker []
 */
class TransactionDailyCounterAndRestrictionChecker implements Middleware
{

    public function __construct(Param $param)
    {
        try {
            /**
             * Check user validity ahead of Validation middleware call
             */
            new SecureApi($param);

            if ((new DashboardService())->isAccountKYCValidated() != true) {
                $today = (Carbon::today())->toDateString();
                $pk = (new Wallet())->getPublickey();

                $todayTransactions = (new Transaction())->filter('created_at')->where(['created_at', 'LIKE', "%$today%"], [['ifrom', $pk, 'OR'], ['ito', $pk]])->count();
                $dailyLimitForRestrictedAccount = (new Settings())->getDailyTransactionLimitForRestrictedAccount();

                if ($todayTransactions >= $dailyLimitForRestrictedAccount) {
                    $msg = "Daily transaction limit exceeded, please validate your account to enjoy unlimited transactions";
                    if (Info::getRouteType() == 'api') {
                        Response::fromApi(['msg' => $msg, 'success' => false], 200);
                    } else {
                        $data = ['message' => $msg, 'success' => false];
                        View::render("Dashboard/PageNotif.php", $data);
                    }
                    exit;
                }
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

}
