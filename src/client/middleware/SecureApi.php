<?php
namespace src\client\middleware;

use src\client\model\Authtoken;
use src\client\model\ExtraUserInfo;
use zil\core\interfaces\Middleware;
use zil\core\interfaces\Param;
use zil\core\scrapper\Info;
use zil\core\server\Response;

/**
 * @Middleware:SecureApi []
 */
class SecureApi implements Middleware
{

    public function __construct(Param $param)
    {

        if (Info::getRouteType() == 'api') {

            $AuthReckoner = new Authtoken();

            if ($AuthReckoner->isValid($param->url()->token)) {

                $username = $AuthReckoner->getPk($param->url()->token);
                $fetched = (new ExtraUserInfo())->as('ex')->with('User as u', 'ex.email = u.email')
                    ->filter('ex.id')
                    ->where(
                        [
                            ['ex.username', $username, 'OR'],
                            ['ex.email', $username]
                        ],

                        ['u.suspended', 0]
                    )->count();

                if ($fetched == 1) {

                    Info::$_dataLounge['API_CLIENT_ACTIVE'] = true;
                    Info::$_dataLounge['API_CLIENT'] = ['TOKEN' => $param->url()->token, 'PK' => $AuthReckoner->getPk($param->url()->token)];


                } else {

                    Response::fromApi(['401 Bad Authorization, token might be expired or incorrect'], 401);
                    exit();

                }


            } else {

                Response::fromApi(['401 Bad Authorization, token might be expired or incorrect'], 401);
                exit();

            }

        }

    }

}
