<?php
namespace src\client\middleware;

use src\client\service\DashboardService;
use zil\core\interfaces\Middleware;
use zil\core\interfaces\Param;
use zil\core\scrapper\Info;
use zil\core\server\Response;
use zil\factory\View;

/**
 *   @Middleware:SpecialRestrictions []
*/

class SpecialRestrictions implements Middleware{

    public function __construct(Param $param){
        /**
         * Check user validity ahead of Validation middleware call
         */
        new SecureApi($param);

        if( (new DashboardService())->isAccountKYCValidated() != true) {
            $msg = "Please validate your account to enjoy unlimited transactions";
            if(Info::getRouteType() == 'api') {
                Response::fromApi([ 'msg' => $msg, 'success' => false], 200);
            }else{
                $data = [ 'message' => $msg, 'success' => false ];
                View::render("Dashboard/PageNotif.php", $data);
            }
            exit;
        }
    }

}
