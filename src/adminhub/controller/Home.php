<?php
namespace src\adminhub\controller;

use src\adminhub\service\GuardAdminLogin;
use src\client\model\PwdMutationLock;
use zil\core\facades\decorators\Hooks;
use zil\core\facades\helpers\Navigator;
use zil\core\facades\helpers\Notifier;
use zil\core\server\Param;
use zil\factory\View;

class Home
{

    use Notifier, Navigator, Hooks;


    public function E401(Param $param)
    {

        $OutputData = [];

        #render the desired interface inside the view folder

        View::render("Home/401.php", $OutputData);
    }

    public function ChangePwd(Param $param)
    {

        if (isset($param->url()->request_auth_key)) {

            $PwdLock = new PwdMutationLock();

            if ($PwdLock->where(['msg', $param->url()->request_auth_key])->count() == 1) {

                $OutputData = [
                    'RQAUTH' => $param->url()->request_auth_key
                ];

                #render the desired interface inside the view folder

                View::render("Home/ChangePwd.php", $OutputData);

            } else {
                $this->goTo('login');
            }


        } else {
            $this->goTo('login');
        }
    }

    public function E404(Param $param)
    {

        $OutputData = [];

        #render the desired interface inside the view folder

        View::render("Home/E404.php", $OutputData);
    }

    public function ForgotPassword(Param $param)
    {

        $OutputData = [];

        #render the desired interface inside the view folder

        View::render("Home/ForgotPassword.php", $OutputData);
    }

    public function Login(Param $param)
    {

        $OutputData = [];

        #render the desired interface inside the view folder

        View::render("Home/Login.php", $OutputData);
    }

    public function Logout(Param $param)
    {
        (new GuardAdminLogin())->destroyGuard();
    }


    public function __construct()
    {

    }


    public function onInit(Param $param)
    {
    }

    public function onAuth(Param $param)
    {

    }

    public function onDispose(Param $param)
    {

    }


}

?>
