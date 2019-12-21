<?php
namespace src\adminhub\controller;

use Carbon\Carbon;
use src\adminhub\model\User;
use src\adminhub\service\GuardAdminLogin;
use src\client\model\ExtraUserInfo;
use src\client\model\MailingList;
use src\client\model\PwdMutationLock;
use src\client\service\MailService;
use Throwable;
use zil\core\facades\decorators\Hooks;
use zil\core\facades\helpers\Navigator;
use zil\core\facades\helpers\Notifier;
use zil\core\server\Param;
use zil\core\tracer\ErrorTracer;
use zil\factory\Redirect;
use zil\factory\Session;
use zil\security\Encryption;
use zil\security\Validation;


class HomePostRequestController
{

    use Notifier, Navigator, Hooks;


    public function __construct()
    {

    }

    public function onInit(Param $param)
    {
    }

    public function onAuth(Param $param)
    {

    }


    public function LoginAccount(Param $param)
    {
        $email = trim($param->form()->email);
        $pwd = $param->form()->pwd;

        if ((new Validation(['email', 'required'], ['pwd', 'required']))->isPassed()) {

            $Encryption = new Encryption;

            if (!is_null((new GuardAdminLogin())->setGuard($email, $pwd))) {

                $muser = (new ExtraUserInfo())->where(['email', $email])->filter('email')->get();
                Session::build('email', $muser->email);

                new Redirect('dashboard');

            } else {

                $this->notification('Login failed, if issue persist contact admin')->send('ERROR');
                $this->goBack();
            }

        } else {
            $this->goBack();
        }
    }

    public function AddAccount(Param $param)
    {
        try {
            $isPrime = isset($param->url()->isprime) || @$param->url()->isprime == 1 ? 1 : 0;

            $name = trim($param->form()->fname) . ' ' . trim($param->form()->lname);

            $email = trim($param->form()->email);
            $phone = trim($param->form()->phone);

            $pwd = $param->form()->pwd;

            $gender = trim($param->form()->gender);
            $referral = 'NULL';


            $Validation = new Validation(['fname', 'required'], ['lname', 'required'], ['email', 'email|required'], ['pwd', 'required'], ['phone', 'required']);

            if ($Validation->isPassed()) {

                unset($Validation);

                $Encryption = new Encryption;

                $pwd = $Encryption->hash($pwd);
                $newDelegate = new ExtraUserInfo();

                if ($newDelegate->filter('name')->where(['email', $email])->count() == 0) {

                    $newDelegate->name = ucwords($name);
                    $newDelegate->password = $pwd;
                    $newDelegate->email = $email;
                    $newDelegate->phone = $phone;

                    if ($newDelegate->create()) {


                        /**Crypto user */
                        $CryptoUser = new User();

                        $CryptoUser->id = '';
                        $CryptoUser->user_type = $CryptoUser->defaultUserType();
                        $CryptoUser->isAdmin = 1;
                        $CryptoUser->password = '';
                        $CryptoUser->email = $email;
                        $CryptoUser->mobile = $phone;
                        $CryptoUser->referer = NULL;
                        $CryptoUser->membership_plan_id = 1;
                        $CryptoUser->hidden = 0;
                        $CryptoUser->suspended = 0;
                        $CryptoUser->created_at = time();
                        $CryptoUser->trans_lock = 0;
                        $CryptoUser->gender = $gender;

                        if (!$CryptoUser->create()) {
                            $lastUser = $newDelegate->lastInsert();
                            $newDelegate->where(['id', $lastUser])->delete();

                            $this->notification("Error: Couldn't complete registration, please retry")->send("ERR");
                            $this->goBack();
                            return;
                        }

                        // Subscribe to mails
                        (new MailingList())->subscribe($email);

                        $this->notification("Success, delegate added. Delegate login required to complete registration")->send('SUCCESS');

                        $this->goBack();
                        return;

                    } else {
                        $this->notification("Error: Couldn't complete registration, please retry")->send("ERR");
                        $this->goBack();
                        return;
                    }
                } else {
                    $this->notification("Error: Username or email already used")->send("ERR");
                    $this->goBack();
                }

            } else {
                $this->goBack();
            }

            #render the desired interface inside the view folder
        } catch (Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function actionForgotP(Param $param)
    {
        try {
            $V = new Validation(['email', 'email|required']);

            if ($V->isPassed()) {

                $d = (new ExtraUserInfo())->filter('id')->where(['email', $param->form()->email])->count();

                if ($d == 0) {
                    $this->clear()->notification("Email is not recognised")->send("ERR");
                    return;
                }


                $PwdLock = new PwdMutationLock();

                $PwdLock->where(['email', $param->form()->email])->delete();

                $msg = (new Encryption())->authKey();

                $PwdLock->email = $param->form()->email;
                $PwdLock->msg = $msg;
                $PwdLock->created_at = Carbon::now();
                $PwdLock->create();


                // Mail to user

                $link = $_SERVER['HTTP_HOST'] . "/account/change/password/{$msg}";
                $msg01 = "<p style='text-align: center;'> Follow the <a href='$link'>link</a> to change your password</p>";

                (new MailService())->sendChangePwdRequest($param->form()->email, $msg01);


                $this->clear()->notification("Request granted, check your email")->send('SUCCESS');

            }


        } catch (Throwable $t) {
            new ErrorTracer($t);
        } finally {
            $this->goBack();
        }

    }


    public function actionChangeP(Param $param)
    {

        $V = new Validation(['password', 'text|required'], ['RQAUTH', 'text|required']);

        if ($V->isPassed()) {

            $PwdLock = new PwdMutationLock();

            $email = $PwdLock->filter('email')->where(['msg', $param->form()->RQAUTH])->get()->email;

            if ($email !== null) {

                $Encryption = new Encryption;
                $npass = $Encryption->Encode($param->form()->password);

                $Ex = new ExtraUserInfo;
                $Ex->password = $Encryption->hash($npass);
                $Ex->where(['email', $email])->update();

                $PwdLock->where(['email', $email])->delete();

                $this->goTo($param->form()->redirect_url);

            } else {
                $this->notification("Something went wrong, request a new password")->clear()->send("ERROR");
                $this->goBack();
            }

        } else {
            $this->goBack();
        }


    }

}

?>
