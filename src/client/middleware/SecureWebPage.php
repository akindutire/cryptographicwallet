<?php
namespace src\client\middleware;

use src\client\model\ExtraUserInfo;
use zil\core\interfaces\Middleware;
use zil\core\interfaces\Param;
use zil\core\scrapper\Info;
use zil\factory\Redirect;
use zil\factory\Session;
use zil\security\Authentication;

/**
 * @Middleware:SecureWebPage []
 */
class SecureWebPage implements Middleware
{

    public function __construct(Param $param)
    {

        if (!is_null(Session::getEncoded('AUTH_CERT')) && !is_null(Session::get('email'))) {


            /**
             * To ensure activity of user within 600sec = 5min
             */
            $current_time = time();
            $elapsed_session = (int)Session::get('Last_Visit');
            $session_limit = 600;

            if (($current_time - $elapsed_session) > $session_limit) {
                new Redirect('login');
            }

            Session::build('Last_Visit', time());

            $username = Session::get('email');
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
                Info::$_dataLounge['API_CLIENT'] = ['TOKEN' => Session::getEncoded('AUTH_CERT'), 'PK' => Session::get('email')];


            } else {

                Session::delete('username')->delete('email');
                new Redirect('login');

                // Response::report(401);
                exit();
            }


            Info::$_dataLounge['API_CLIENT_ACTIVE'] = true;
            Info::$_dataLounge['API_CLIENT'] = ['TOKEN' => Session::getEncoded('AUTH_CERT'), 'PK' => Session::get('email')];

        } else {

            Session::delete('username')->delete('email');
            new Redirect('login');
            // Response::report(401);
            exit();
        }

    }

    private function destroyAuth()
    {
        Authentication::Destroy();
        Session::delete('username')->delete('email');
        new Redirect('login');
    }


}
