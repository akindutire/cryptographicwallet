<?php
namespace src\client\controller\api;

use Carbon\Carbon;
use Exception;
use src\client\middleware\Date;
use src\client\middleware\SecureApi;
use src\client\model\ActivityLog;
use src\client\model\Authtoken;
use src\client\model\EmailValidationTokenLock;
use src\client\model\ExtraUserInfo;
use src\client\model\MailingList;
use src\client\model\Membership_plan;
use src\client\model\Notification;
use src\client\model\PwdMutationLock;
use src\client\model\Settings;
use src\client\model\Transaction;
use src\client\model\User;
use src\client\model\Wallet;
use src\client\service\Bill;
use src\client\service\Bvnverifier;
use src\client\service\DashboardService;
use src\client\service\MailService;
use Throwable;
use zil\core\facades\decorators\Hooks;
use zil\core\facades\helpers\Navigator;
use zil\core\facades\helpers\Notifier;
use zil\core\scrapper\Info;
use zil\core\server\Param;
use zil\core\server\Response;
use zil\core\tracer\ErrorTracer;
use zil\factory\Session;
use zil\security\Encryption;
use zil\security\Validation;


class UserController
{

    use Notifier, Navigator, Hooks;

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
//			header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
    }

    public function onInit(Param $param)
    {
        new Date($param);
    }

    public function onAuth(Param $param)
    {
        new SecureApi($param);
    }

    public function getUserEmail(): string
    {
        try {

            if (Info::$_dataLounge['API_CLIENT_ACTIVE'] == true) {
                $email = Info::$_dataLounge['API_CLIENT']['PK'];
            } else {
                $email = Session::get('email');
            }

            return $email;

        } catch (Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function ValidateBvn(Param $param)
    {

        try {

            $Validation = new Validation(['bvn', 'required|minlength:11|maxlength:11']);

            if ($Validation->isPassed()) {
                $bvn = $param->form()->bvn;

                $result = (new Bvnverifier())->validate($bvn);

                if ($result->status == true) {

                    $EU = new ExtraUserInfo();
                    $id = $EU->getUserId();

                    $email = $EU->filter('email')->iwhere('id', $id)->get()->email;

                    $User = new User();

                    $User->KYC_DOB = $result->data->formatted_dob;
                    $User->KYC_FULLNAME = $result->data->first_name . " " . $result->data->last_name;
                    $User->KYC_MOBILE = $result->data->mobile;
                    $User->isVerifiedAccount = true;

                    if ($User->where(['email', $email])->update() == 1) {

                        $data = ['msg' => 'Account has been validated', 'success' => true];
                        (new ActivityLog())->Log("KYC: Account Validation Parsed and Updated", "SUCCESS");

                    } else {

                        (new ActivityLog())->Log("KYC: Account Validation Parsed and not Updated", "Partial Success");
                        throw new Exception("Account not verified, ensure that you have correct details on your profile");

                    }

                } else {
                    throw new Exception("BVN is invalid");
                }

            } else {
                throw new Exception($Validation->getErrorString());
            }

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }


    public function VerifyEmail(Param $param)
    {

        try {

            $E = new ExtraUserInfo();

            $id = $E->getUserId();

            $email = (new ExtraUserInfo())->filter('email')->where(['id', $id])->get()->email;

            if (!is_string($email))
                throw new Exception("Undefined account mail, please retry");

            $verificationKey = (new EmailValidationTokenLock())->RequestEmailVerification($email);
            if (!is_null($verificationKey)) {

                $today = date('M d, Y H:i');

                $link = $_SERVER['HTTP_HOST'] . "/verify/account/{$email}/{$verificationKey}";
                $message = "<p style='text-align: center;'> Use below link to verify your email</><p style='text-align: center;'> <a href='$link'>{$link}</a></p>";

                (new MailService())->sendMail($email, "Naijasub: Email Verification ({$today})", $message);

                $data = ['msg' => "Verification link has been sent to your mail, please check your mailbox to continue", 'success' => true];

            } else {
                throw new Exception("Fail to request verification link for user, please retry");
            }

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }


    public function GetAccountPlanLevels(Param $param)
    {

        try {
            $data = ['msg' => (new Membership_plan())->all()->get('VERBOSE'), 'success' => true];
        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    public function GetHotkeys(Param $param)
    {

        try {

            if (!(in_array($param->url()->type, ['RULE', 'EXCHANGE', 'SERVICE_CHARGE', 'REWARD', 'NETWORK_PROVIDER', 'rule', 'exchange', 'service_charge', 'reward', 'network_provider'])))
                throw new Exception("Unknown hot key type, try rule,exchange,reward,network_provider or service_charge");

            if (strtoupper($param->url()->type) == 'RULE') {
                $data = ['msg' => (new Settings())->getRules(), 'success' => true];
            } elseif (strtoupper($param->url()->type) == 'EXCHANGE') {
                $data = ['msg' => (new Settings())->getExchangeRates(), 'success' => true];
            } elseif (strtoupper($param->url()->type) == 'SERVICE_CHARGE') {
                $data = ['msg' => (new Settings())->getServiceCharges(), 'success' => true];
            } elseif (strtoupper($param->url()->type) == 'REWARD') {
                $data = ['msg' => (new Settings())->getRewards(), 'success' => true];
            } elseif (strtoupper($param->url()->type) == 'NETWORK_PROVIDER') {
                $data = ['msg' => (new Settings())->getNetworkProviders(), 'success' => true];
            }

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }


    // Account details
    public function Passport(Param $param)
    {

        try {


            $email = $this->getUserEmail();

            $account = (new User())->getAccountDetails($email);

            $data = ['msg' => array_merge((array)$account[0], (array)$account[1], (array)$account[2]), 'success' => true];

        } catch (Throwable $t) {

            $data = ['msg' => $t->getMessage(), 'success' => false];

        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function PassportViaWallet(Param $param)
    {

        try {
            // raw public key

            $public_key = strip_tags($param->url()->wallet_key);

            if (Wallet::filter('owned_by')->where(['public_key', $public_key])->count() == 1) {

                $user_id = Wallet::filter('owned_by')->where(['public_key', $public_key])->get()->owned_by;
                $email = ExtraUserInfo::filter('email')->where(['id', $user_id])->get()->email;

                $account = (new User())->getAccountDetails($email);
                $account = (array)$account;

                $data = ['msg' => array_merge((array)$account[0], (array)$account[1]), 'success' => true];

            } else {
                $data = ['msg' => [], 'success' => false];
            }

        } catch (Throwable $t) {

            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function PreviousPlans(Param $param)
    {

        try {

            $data = ['msg' => (new User())->getPreviousPlans(), 'success' => true];

        } catch (Throwable $t) {

            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function Subscribe(Param $param)
    {
        try {
            $email = strip_tags($param->url()->email);

            $data = ['msg' => (new MailingList())->subscribe($email), 'success' => true];

        } catch (Throwable $t) {

            $data = ['msg' => $t->getMessage(), 'success' => false];

        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function Unsubscribe(Param $param)
    {
        try {
            $email = strip_tags($param->url()->email);

            $data = ['msg' => (new MailingList())->unsubscribe($email), 'success' => true];

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function Referrals(Param $param)
    {
        try {

            $data = ['msg' => ['referral' => (new User())->getReferrals(), 'reward' => (new User())->getTotalReferralReward()], 'success' => true];

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }


    // Mutate profile pic.
    public function ChangeProfilePic(Param $param)
    {
        try {
            $response = (new DashboardService())->uploadProfilePic($_FILES['file']);

            if ($response['status'] == true) {
                $data = ['photosource' => $response['picsrc'], 'success' => true];

                $user = new User();
                if (!$user->updateProfilePic($response['uploadPath'], $response['photoname'])) {
                    $data = ['msg' => "Couldn't complete file upload, retry", 'success' => false];
                }

            } else {
                $data = ['msg' => $response['error'], 'success' => false];
            }

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }


    }

    // Mutate profile details
    public function ChangeProfileDetails(Param $param)
    {
        try {
            $email = trim($param->form()->email);
            $name = trim($param->form()->name);
            $phone = trim($param->form()->phone);
            $acc_no = trim($param->form()->acc_no);
            $acc_name = trim($param->form()->acc_name);
            $bank = trim($param->form()->bank);

            $Pemail = $this->getUserEmail();

            if (User::filter('id')->where(['email', $Pemail], ['trans_lock', 0])->count() == 1) {

                $Validation = new Validation(['email', 'email|required'], ['name', 'required'], ['phone', 'required|minlength:11']);

                if ($Validation->isPassed()) {


                    $User = new User();

                    if ($User->isExists(['email', $email]) && $Pemail != $email)
                        throw new Exception("Email is already in use");

                    $User->email = $email;
                    $User->mobile = $phone;
                    $User->isEmailVerified = NULL;
                    $User->where(['email', $Pemail])->update();

                    if (Info::getRouteType() == 'api' && isset(Info::$_dataLounge['API_CLIENT'])) {

                        $AuthReckon = new Authtoken();
                        $AuthReckon->claim = $email;
                        $AuthReckon->expires_at = $AuthReckon->getExpirationTimeStamp();
                        $AuthReckon->where(['token', Info::$_dataLounge['API_CLIENT']['TOKEN']])->update();

                        Info::$_dataLounge['API_CLIENT']['PK'] = $email;

                    }


                    $MainUser = new ExtraUserInfo();

                    $Usr = $MainUser->filter('id')->where(['email', $Pemail])->get();

                    $MainUser->email = $email;
                    $MainUser->phone = $phone;
                    $MainUser->name = $name;
                    $MainUser->where(['email', $Pemail])->update();


                    $Wallet = new Wallet();
                    $Wallet->acc_no = $acc_no;
                    $Wallet->acc_name = $acc_name;
                    $Wallet->bank = $bank;
                    $Wallet->where(['owned_by', $Usr->id])->update();

                    $Ml = new MailingList;
                    $Ml->email = $email;
                    $Ml->iwhere('email', $Pemail)->update();

                    /**Reset web user session */
                    (new Session())->build('email', $email);


                    $data = ['msg' => "Details updated", "user_short_credentials" => ['email' => $email, 'token' => Info::$_dataLounge['API_CLIENT']['TOKEN']], 'success' => true];

                } else {

                    $data = ['msg' => $Validation->getErrorString(), 'success' => false];
                }

            } else {
                $data = ['msg' => "Couldn't edit profile, you have pending transactions", 'success' => false];
            }

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {

            unset($User, $MainUser, $Wallet, $Usr);
            echo Response::fromApi($data, 200);
        }

    }

    // Request password Mutation
    public function RequestPwdChange(Param $param)
    {

        try {

            $password = md5($param->form()->pwd);

            if (ExtraUserInfo::filter('id')->where(['email', Session::get('email')], ['password', $password])->count() == 1) {

                $email = $this->getUserEmail();

                $msg = (new Encryption())->authKey();

                $PwdLock = new PwdMutationLock();

                $PwdLock->where(['email', Session::get('email')])->delete();

                $PwdLock->email = $email;
                $PwdLock->msg = $msg;
                $PwdLock->created_at = Carbon::now();
                $PwdLock->create();


                // Mail to user

                $link = $_SERVER['HTTP_HOST'] . "/account/change/password/{$msg}";
                $msg01 = "<p style='text-align: center;'> Follow the <a href='$link'>link</a> to change your password</p>";


                (new MailService())->sendChangePwdRequest($email, $msg01);


                $data = ['msg' => "Request granted, check your email", 'success' => true];

            } else {
                throw new Exception("Request cancelled, Password incorrect");
            }

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }


    // Upgrade account
    public function UpgradeAccount(Param $param)
    {

        try {

            $plan_level = intval($param->url()->plan_level);

            if (Membership_plan::filter('id')->where(['level', $plan_level])->count() == 0) {

                $data = ['msg' => ['error' => "Application Error: Unknown level selected"], 'success' => false];

                return;
            }

            $Mbp = Membership_plan::filter('id, cost, tag')->where(['level', $plan_level])->get();

            // Check User wallet
            $Wallet = new Wallet;

            if (!$Wallet->isSufficientBalance(doubleval($Mbp->cost))) {

                $data = ['msg' => "Insufficient fund in wallet, please top up wallet", 'success' => false];

                return;
            }

            $Transaction = new Transaction;
            $payment_meta = [
                'type' => $Transaction->getTransactionTypes('ACCOUNT_UPGRADE'),
                'to_address' => $Wallet->getAnyAdminPublickey(),
            ];


            // Pay membership due
            if ($Wallet->pay(floatval($Mbp->cost), $payment_meta)) {

                (new User())->setNewMemberShipLevel($Mbp->id);
                $data = ['msg' => "Congratulations, Account Upgraded", 'success' => true];

                $msg01 = "<p style='text-align: center;'> Congratulations, Account Upgraded to {$Mbp->tag} account at cost of <b>NGN {$Mbp->cost}</b></p>";

                $email = $this->getUserEmail();

                (new MailService())->sendMail($email, 'Account Upgrade', $msg01);

            } else {
                $data = ['msg' => "Couldn't complete membership payment", 'success' => true];
            }

            unset($Wallet, $Mbp);

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }


    public function UseAccount(Param $param)
    {

        try {

            $plan_level = intval($param->url()->plan_level);

            if (Membership_plan::filter('id')->where(['level', $plan_level])->count() == 0) {

                $data = ['msg' => "Application Error: Unknown level selected", 'success' => false];

                return;
            }

            $prev = (new User())->filter('previous_plans')->where(['email', Session::get('email')])->get()->previous_plans;
            if ($prev === NULL || $prev == 'NULL') {
                throw new Exception("This plan has never been subscribed to in the past");
            } else {
                $prev = rtrim($prev, ';');
                if (!in_array($plan_level, explode(';', $prev))) {
                    throw new Exception("This plan has never been subscribed to in the past");
                }
            }

            $Mbp = Membership_plan::filter('id, cost')->where(['level', $plan_level])->get();
            (new User())->setNewMemberShipLevel($Mbp->id, true);
            $data = ['msg' => "Congratulations, Account Switched", 'success' => true];

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }


}

?>
