<?php
namespace src\adminhub\middleware;
use zil\core\interfaces\Middleware;
use zil\core\interfaces\Param;
use zil\core\scrapper\Info;
use zil\core\server\Response;
use zil\factory\Redirect;
use zil\factory\Session;
use zil\security\Authentication;

/**
 *   @Middleware:SecureAdmin []
*/

class SecureAdmin implements Middleware {

    public function __construct(Param $param){
        if(is_null(Session::getEncoded('AUTH_CERT')) || is_null(Session::get('email')) ){

            Authentication::Destroy();
            Session::delete('username')->delete('email');

            if (Info::getRouteType() == 'api')
                echo Response::fromApi("Unauthorized access", 401);
            else
                new Redirect('login');



            exit();
        }
    }


}
