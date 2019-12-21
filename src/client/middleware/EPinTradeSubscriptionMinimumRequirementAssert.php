<?php
namespace src\client\middleware;

use src\client\controller\api\UserController;
use src\client\model\Authtoken;
use src\client\model\ExtraUserInfo;
use src\client\model\Settings;
use src\client\service\DashboardService;
use zil\core\facades\helpers\Navigator;
use zil\core\interfaces\Middleware;
use zil\core\interfaces\Param;
use zil\core\scrapper\Info;
use zil\core\server\Http;
use zil\core\server\Request;
use zil\core\server\Response;
use zil\core\tracer\ErrorTracer;
use zil\factory\Utility;
use zil\factory\View;

/**
 *   @Middleware:AffiliateMinimumRequirementAsserter []
*/

class EPinTradeSubscriptionMinimumRequirementAssert implements Middleware {

    use Navigator;
    public function __construct(Param $param){
        try{
            new SecureWebPage($param);

            if( (new DashboardService())->isRecognizedAsDataCardCustomer() )
                return;

            $EU = new ExtraUserInfo();

            $id = $EU->getUserId();
            $Ur = $EU->as('e')
                ->with('User as u', 'e.email = u.email')
                ->with('Membership_plan as mp', 'u.membership_plan_id = mp.id')
                ->filter('e.email', 'mp.tag')
                ->where( ['e.id', $id] )->get();

            $email = $Ur->email;
            $tag = trim($Ur->tag);


            // Is a higher rank member, then go
            if($tag == 'DEALER')
                return;

            /**
             * Extract authorized user token
             */
            if(Info::getRouteType() == 'api')
                $token = $param->url()->token;
            else
                $token = (new Authtoken())->filter('token')->where( ['claim', $email] )->get()->token;

            $routeForUserBalance = Utility::route("api/user/wallet/balance/").$token;


            /**
             * Expected to be Json
             */


            $ResponseFromWalletBalanceApi = (new Http($routeForUserBalance))->hasResponse()->get();

            if($ResponseFromWalletBalanceApi->success == true){

                if( $ResponseFromWalletBalanceApi->msg->balance >= (new Settings())->getMinimumBalanceRequirementDataCardReseller() ){
                    return;
                }else{

                    $minReq = (new Settings())->getMinimumBalanceRequirementDataCardReseller();

                    $data = ['message' => "Unauthorized to use this feature, minimum requirement is NGN {$minReq} or you must be on DEALER plan", 'success' => false];

                    if(Info::getRouteType() == 'api')
                        Response::fromApi( $data, 200 );
                    else
                        View::render('Dashboard/PageNotif', $data);


                    exit();
                }

            }else{

                $data =  ['message' => "Couldn't verify user wallet balance, please re-login or check your connection", 'success' => false];
                if(Info::getRouteType() == 'api')
                    Response::fromApi( $data, 200 );
                else
                    View::render('Dashboard/PageNotif', $data);

                exit();
            }

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }
}
