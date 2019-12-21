<?php
namespace src\adminhub\controller\api;

use src\adminhub\middleware\Date;
use src\adminhub\middleware\SecureAdmin;
use src\adminhub\model\CashoutRequest;
use src\adminhub\model\Notification;
use src\adminhub\model\TopupRequest;
use src\adminhub\model\Transaction;
use src\adminhub\model\User;
use src\adminhub\model\Wallet;
use src\adminhub\service\DashboardService;
use src\client\controller\api\UserController as UUserController;
use src\client\model\ExtraUserInfo;
use src\client\model\SalesPoint;
use src\client\service\MailService;
use zil\core\facades\decorators\Hooks;
use zil\core\server\Param;
use zil\core\server\Response;
use zil\core\tracer\ErrorTracer;
use zil\factory\Session;
use zil\security\Validation;

// use \zil\core\facades\helpers\Notifier;
// use \zil\core\facades\helpers\Navigator;


class UserController extends UUserController
{

    use Hooks;

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
//            header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

    }

    public function onInit(Param $param)
    {


    }

    public function onAuth(Param $param)
    {
        new SecureAdmin($param);
        /**
         * Adjust timezone
         */
        new Date($param);
    }


    public function DeleteNotification(Param $param)
    {
        try {
            if (!is_null($param->url()->notification_hash)) {

                $data = ['msg' => (new Notification())->deleteNotif($param->url()->notification_hash), 'success' => true];

            } else {
                throw new \Exception('Couldn\'t delete notification, unknown notification');
            }

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function GetSpecificNotification(Param $param)
    {
        try {

            if (!is_null($param->url()->notification_hash)) {

                $data = ['msg' => (new Notification())->getNotif($param->url()->notification_hash), 'success' => true];

            } else {
                throw new \Exception('Couldn\'t open notification, unknown notification');
            }

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function GetAllNotification(Param $param)
    {
        try {

            $data = ['msg' => (new Notification())->getAllNotifs(), 'success' => true];

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function SendMailToAllMailingList(Param $param)
    {

        try {

            $Validation = new Validation(['subject', 'required'], ['message', 'required']);
            if ($Validation->isPassed()) {

                $subject = ucwords($param->form()->subject);
                $message = $param->form()->message;

                $Mailer = new MailService;
                $nSent = $Mailer->sendBroadcastMail($subject, $message);

                $data = ['msg' => "Mail Sent to {$nSent} users", 'success' => true];

            } else {
                throw new \Exception('Some fields are missing');
            }


        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    public function SendNotification(Param $param)
    {
        try {

            $Validation = new Validation(['notif_subject', 'required'], ['notif_message', 'required']);
            if ($Validation->isPassed()) {
                $subject = ucwords($param->form()->notif_subject);
                $message = $param->form()->notif_message;
                $is_to_be_published = $param->form()->to_be_published;

                if ((new Notification())->createNotif($subject, $message, $is_to_be_published)) {
                    $data = ['msg' => 'Notification Sent', 'success' => true];
                } else {
                    throw new \Exception('Couldn\'t send notification, retry');
                }
            } else {
                throw new \Exception('Some fields are missing');
            }


        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function UserListAsAllContacts(Param $param)
    {
        try {

            $d = (new User())->getAllContacts();


            $data = ['msg' => $d, 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    // Wallet balance
    public function WalletAmtDetails(Param $param)
    {

        try {

            $public_key = $param->url()->wallet_key;

            $data = ['msg' => (new Wallet())->getAmtDetails($public_key), 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    // Progressive airtime trade(customer wants to sell to naijasub)
    public function AirtimeTradeSellingReq(Param $param)
    {
        try {

            $S = new SalesPoint;
            $T = new Transaction;

            $pk = sodium_bin2hex((new Wallet())->getPublickey());


            $Ss = $S->all()->where(['status', $S->getTradeStatus('PROGRESS')], ['trade_type', $T->getTransactionTypes('AIRTIME_TRADE')], ['ifrom_address', $pk])->get('VERBOSE');

            $container = [];

            foreach ((array)$Ss as $SSS) {

                $SSS = (array)$SSS;
                $SSS = array_map(function ($entry) {

                    if (!mb_detect_encoding($entry, 'ASCII', true))
                        return sodium_bin2hex($entry);
                    else
                        return $entry;

                }, $SSS);

                array_push($container, $SSS);
            }

            $data = ['msg' => $container, 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    // Progressive airtime trade(customer wants to sell to naijasub)
    public function AirtimeTradeSellingCompletedReq(Param $param)
    {
        try {

            $S = new SalesPoint;
            $T = new Transaction;

            $pk = sodium_bin2hex((new Wallet())->getPublickey());


            $Ss = $S->all()->where(['status', $S->getTradeStatus('COMPLETED')], ['trade_type', $T->getTransactionTypes('AIRTIME_TRADE')], ['ifrom_address', $pk])->get('VERBOSE');

            $container = [];

            foreach ((array)$Ss as $SSS) {

                $SSS = (array)$SSS;
                $SSS = array_map(function ($entry) {

                    if (!mb_detect_encoding($entry, 'ASCII', true))
                        return sodium_bin2hex($entry);
                    else
                        return $entry;

                }, $SSS);

                array_push($container, $SSS);
            }

            $data = ['msg' => $container, 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }


    // Progressive airtime trade
    public function AirtimeTradeBuyingReq(Param $param)
    {
        try {

            $S = new SalesPoint;
            $T = new Transaction;

            $pk = sodium_bin2hex((new Wallet())->getPublickey());


            $Ss = $S->all()->where(['status', $S->getTradeStatus('PROGRESS')], ['trade_type', $T->getTransactionTypes('AIRTIME_TRADE')], ['ito_address', $pk])->get('VERBOSE');

            $container = [];

            foreach ((array)$Ss as $SSS) {

                $SSS = (array)$SSS;
                $SSS = array_map(function ($entry) {

                    if (!mb_detect_encoding($entry, 'ASCII', true))
                        return sodium_bin2hex($entry);
                    else
                        return $entry;

                }, $SSS);

                array_push($container, $SSS);
            }

            $data = ['msg' => $container, 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    // Cashout req.
    public function CashoutReq(Param $param)
    {

        try {

            if ($param->url()->status == 'unpaid') {

                $d = (new CashoutRequest())->getUnpaidCashouts();

            } else {
                $d = (new CashoutRequest())->getPaidCashouts();
            }

            $container = [];

            foreach ((array)$d as $x) {

                $x = (array)$x;
                $x = array_map(function ($entry) {

                    if (!mb_detect_encoding($entry, 'ASCII', true))
                        return sodium_bin2hex($entry);
                    else
                        return $entry;

                }, $x);

                array_push($container, $x);
            }

            $data = ['msg' => $container, 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }


    // Topup req.
    public function TopupReq(Param $param)
    {

        try {

            if ($param->url()->status == 'pending') {

                $d = (new TopupRequest())->getPendingTopups();

            } elseif ($param->url()->status == 'confirmed') {

                $d = (new TopupRequest())->getConfirmedTopups();

            } elseif ($param->url()->status == 'rejected') {

                $d = (new TopupRequest())->getRejectedTopups();

            }

            $container = [];

            foreach ((array)$d as $x) {

                $x = (array)$x;
                $x = array_map(function ($entry) {

                    if (!mb_detect_encoding($entry, 'ASCII', true))
                        return sodium_bin2hex($entry);
                    else
                        return $entry;

                }, $x);

                array_push($container, $x);
            }

            $data = ['msg' => $container, 'success' => true];

        } catch (\Throwable $t) {
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

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    // Mutate profile details
    public function ChangeProfileDetails(Param $param)
    {
        try {

            $name = trim($param->form()->fname) . ' ' . trim($param->form()->lname);

            $email = trim($param->form()->email);
            $phone = trim($param->form()->phone);

            $acc_no = trim($param->form()->acc_no);


            $Validation = new Validation(['email', 'email|required'], ['lname', 'required'], ['fname', 'required'], ['phone', 'required|minlength:11']);

            if ($Validation->isPassed()) {

                $CryptoUser = new User();
                $CryptoUser->email = $email;
                $CryptoUser->mobile = $phone;
                $CryptoUser->where(['email', Session::get('email')])->update();


                $MainUser = new ExtraUserInfo();

                $Usr = $MainUser->filter('id')->where(['email', Session::get('email')])->get();

                $MainUser->email = $email;
                $MainUser->phone = $phone;
                $MainUser->name = $name;
                $MainUser->where(['email', Session::get('email')])->update();


                $Wallet = new Wallet();
                $Wallet->acc_no = $acc_no;
                $Wallet->where(['owned_by', $Usr->id])->update();

                unset($CryptoUser, $MainUser, $Wallet, $Usr);

                Session::delete('email');
                $data = ['msg' => "Details updated", 'success' => true];

            } else {

                $data = ['msg' => $Validation->getError(), 'success' => false];
            }


        } catch (\Throwable $t) {
            $data = ['msg' => ['Error' => $t->getMessage()], 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }


    // Transfer fund between wallet
    public function TransferFund(Param $param)
    {

        try {


            $wallet_key = $param->url()->wallet_key;

            $destination_address = $param->form()->des_address;
            $amount = doubleval($param->form()->amount);

            if ($wallet_key == $destination_address) {
                $data = ['msg' => ['error' => "Forbidden: Can't transfer fund to your self"], 'success' => false];
                return;
            }

            $Wallet = new Wallet;
            $User = new User;

            if (!$Wallet->isValid($wallet_key)) {
                $data = ['msg' => ['error' => "Invalid wallet, please ensure you are using your wallet key"], 'success' => false];

                return;
            }


            $Validation = new Validation(['des_address', 'required'], ['amount', 'number|min:0']);

            if ($Validation->isPassed() && !empty($wallet_key)) {

                if ($amount < 0) {

                    $data = ['msg' => ['error' => "Amount must be more than 0.00"], 'success' => false];

                    return;
                }

                if (!$Wallet->isSufficientBalance($amount)) {
                    $data = ['msg' => ['error' => "Insufficient fund in wallet, please top up wallet"], 'success' => false];

                    return;
                }

                if ($Wallet->transfer($amount, $destination_address, true)) {

                    $data = ['msg' => "Fund transfered", 'success' => true];
                } else {

                    $data = ['msg' => ['error' => "Error: Couldn't complete Fund transfer"], 'success' => false];
                }

                unset($Transaction, $Wallet);

            } else {
                $data = ['msg' => $Validation->getError(), 'success' => false];
            }

        } catch (\Throwable $t) {
            $data = ['msg' => ['error' => $t->getMessage() . $t->getFile() . $t->getLine()], 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

}

?>
