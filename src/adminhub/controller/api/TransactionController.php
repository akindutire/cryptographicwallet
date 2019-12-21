<?php
namespace src\adminhub\controller\api;

use Carbon\Carbon;
use src\adminhub\middleware\Date;
use src\adminhub\middleware\SecureAdmin;
use src\adminhub\model\CashoutRequest;
use src\adminhub\model\TopupRequest;
use src\adminhub\model\Transaction;
use src\adminhub\model\Wallet;
use src\client\model\ExtraUserInfo;
use src\client\model\SalesPoint;
use src\client\service\MailService;
use zil\core\facades\decorators\Hooks;
use zil\core\facades\helpers\Navigator;
use zil\core\facades\helpers\Notifier;
use zil\core\server\Param;
use zil\core\server\Response;


class TransactionController
{

    use Notifier, Navigator, Hooks;


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

    public function CancelTransaction(Param $param)
    {
        try {

            $trans_hash = $param->url()->trans_hash;

            $trans_cancelled = (new Transaction())->cancelTransaction($trans_hash);

            if ($trans_cancelled == true)
                $data = ['msg' => 'Transaction cancelled', 'success' => true];
            else
                throw new \Exception("Transaction not cancelled");

        } catch (\Throwable $t) {
            $data = ["msg" => $t->getMessage(), "success" => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function RollbackTransaction(Param $param)
    {
        try {

            $trans_hash = $param->url()->trans_hash;
            $trans_note = $param->form()->note;


            $fmr_trans = (new Transaction())->filter('ifrom', 'ito', 'amt_exchanged', 'status', 'updated_at')->where(['trans_hash', sodium_hex2bin($trans_hash)])->get();

            $Wallet = new Wallet;
            $balance = $Wallet->getCredit(sodium_bin2hex($fmr_trans->ito)) + $Wallet->getDebit(sodium_bin2hex($fmr_trans->ito));


            if ($balance < $fmr_trans->amt_exchanged) {
                throw new \Exception("Receiver doesn't have enough fund to charge back, Only {$balance} available. \n Transaction {$trans_hash} roll back not successful");
            } else {

                if ((new Transaction())->rollbackTransaction($trans_hash, $trans_note)) {
                    $data = ['msg' => "Transaction {$trans_hash} rolled back successfully", "success" => true];
                } else {
                    throw new \Exception("Transaction {$trans_hash} roll back not successful");
                }

            }

        } catch (\Throwable $t) {
            $data = ["msg" => $t->getMessage(), "success" => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function RejectTopup(Param $param)
    {
        try {

            $request_id = $param->url()->request_id;

            if ((new TopupRequest())::filter('id')->where(['id', $request_id], ['status', 'REJECTED'])->count() == 1) {
                $data = ['msg' => 'Request rejected, and account has been previously balanced', 'success' => true];
                return;
            }


            if ((new TopupRequest())->RejectTopup($request_id)) {
                $data = ['msg' => 'Request rejected, and account has been balanced', 'success' => true];

                $TpReq = (new TopupRequest())::filter('request_hash', 'amount', 'bearer_address')->where(['id', $request_id])->get();

                $msg01 = "<p style='text-align: center;'><b>Request id:{$TpReq->request_hash}</b></p><p style='text-align: center;'> Top up by <b>NGN {$TpReq->amount}</b> has been rejected</p><p style='color: red;'><b>Status: REJECTED</b></p>";


                $Receiver_Uid = Wallet::filter('owned_by')->where(['public_key', $TpReq->bearer_address])->get()->owned_by;
                $email = ExtraUserInfo::filter('email')->where(['id', $Receiver_Uid])->get()->email;

                (new MailService())->sendTopUpRequest($email, $msg01);


            } else {
                throw new \Exception("Couldn't complete rejection");
            }
        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function GetSpecificClientTransactionHistory(Param $param)
    {
        try {

            $client_username = isset($param->form()->username) ? $param->form()->username : $param->url()->username;

            $TransactionObj = new Transaction;


            $data = ['msg' => $TransactionObj->getSpecificClientTransactionHistory($client_username), 'success' => true];


        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }


    public function UserTransactions(Param $param)
    {

        try {

            $trans_state = $param->url()->transaction_state;

            $TransactionObj = new Transaction;

            if ($trans_state == 'pending') {

                $transactions = $TransactionObj->getPendingTransactions();

            } else if ($trans_state == 'confirmed') {

                $transactions = $TransactionObj->getconfirmedTransactions();
            } else if ($trans_state == 'rolledback') {

                $transactions = $TransactionObj->getRollbackTransactions();
            } else {
                //all
                $transactions = $TransactionObj->getAllTransactions();
            }

            $container = [];

            foreach ((array)$transactions as $transaction) {

                $transaction = (array)$transaction;
                $transaction = array_map(function ($entry) {

                    if (!mb_detect_encoding($entry, 'ASCII', true))
                        return sodium_bin2hex($entry);
                    else
                        return $entry;

                }, $transaction);

                array_push($container, $transaction);
            }

            $data = ['msg' => $container, 'success' => true];
        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }


    }

    public function UserIncomingTransactions(Param $param)
    {

        try {
            $trans_state = $param->url()->transaction_state;


            $TransactionObj = new Transaction;

            if ($trans_state == 'pending') {

                $transactions = $TransactionObj->getIncomingPendingTransactions();

            } else if ($trans_state == 'confirmed') {

                $transactions = $TransactionObj->getConfirmedTransactions();
            } else if ($trans_state == 'rollback') {

                $transactions = $TransactionObj->getRollbackTransactions();
            } else {
                //all
                $transactions = $TransactionObj->getIncomingTransactions();
            }

            $container = [];

            foreach ((array)$transactions as $transaction) {

                $transaction = (array)$transaction;

                $transaction = array_map(function ($entry) {


                    if (!mb_detect_encoding($entry, 'ASCII', true))
                        return sodium_bin2hex($entry);
                    else
                        return $entry;

                }, $transaction);

                array_push($container, $transaction);
            }

            $data = ['msg' => $container, 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    public function UserOutgoingTransactions(Param $param)
    {

        try {
            $trans_state = $param->url()->transaction_state;

            $TransactionObj = new Transaction;

            if ($trans_state == 'pending') {

                $transactions = $TransactionObj->getOutgoingPendingTransactions();

            } else if ($trans_state == 'confirmed') {

                $transactions = $TransactionObj->getConfirmedTransactions();
            } else if ($trans_state == 'rolledback') {

                $transactions = $TransactionObj->getRollbackTransactions();
            } else {
                //all
                $transactions = $TransactionObj->getOutgoingTransactions();
            }

            $container = [];

            foreach ((array)$transactions as $transaction) {

                $transaction = (array)$transaction;
                $transaction = array_map(function ($entry) {

                    if (!mb_detect_encoding($entry, 'ASCII', true))
                        return sodium_bin2hex($entry);
                    else
                        return $entry;

                }, $transaction);

                array_push($container, $transaction);
            }

            $data = ['msg' => $container, 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    public function ConfirmTransactions(Param $param)
    {

        try {
            $trans_hash = $param->url()->transaction_hash;

            if ((new Transaction())->confirmTransaction($trans_hash))
                $data = ['msg' => 'Transaction confirmed', 'success' => true];
            else
                throw new \Exception("Couldn't confirm transaction, please retry");

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    public function ConfirmPayout(Param $param)
    {
        try {

            $amount = floatval($param->url()->amount);
            $request_id = $param->url()->request_id;

            if ((new CashoutRequest())::filter('id')->where(['id', $request_id], ['paid', true])->count() == 1) {
                $data = ['msg' => 'Request Confirmed, and account has been balanced', 'success' => true];
                return;
            }

            $CshReq = (new CashoutRequest())::filter('receiver_address', 'request_hash')->where(['id', $request_id])->get();
            $raw_user_public_key = $CshReq->receiver_address;
            if ((new Wallet())::filter('id')->where(['balance', '>=', $amount], ['public_key', $raw_user_public_key])->count() == 0)
                throw new \Exception("Insufficient balance on user wallet");


            if ((new CashoutRequest)->confirmCashoutAsPaid($amount, $request_id)) {

                $data = ['msg' => 'Request Confirmed, and account has been balanced', 'success' => true];

                $msg01 = "<p style='text-align: center;'><b>Request id:{$CshReq->request_hash}</b></p><p style='text-align: center;'> Cash out of <b>NGN {$amount}</b> has been confirmed</p><p style='color: green;'><b>Status: CONFIRMED</b></p>";


                $Receiver_Uid = Wallet::filter('owned_by')->where(['public_key', $raw_user_public_key])->get()->owned_by;
                $email = ExtraUserInfo::filter('email')->where(['id', $Receiver_Uid])->get()->email;

                (new MailService())->sendMail($email, "Cash out(#{$CshReq->request_hash})", $msg01);


            } else {
                throw new \Exception("Couldn't complete pay out");
            }
        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function ConfirmTopup(Param $param)
    {
        try {

            $amount = floatval($param->url()->amount);
            $request_id = $param->url()->request_id;

            if ((new TopupRequest())::filter('id')->where(['id', $request_id], ['status', 'CONFIRMED'])->count() == 1) {
                $data = ['msg' => 'Request Confirmed, and account has been previously balanced', 'success' => true];
                return;
            }

            if ((new TopupRequest())->confirmTopupAsPaid($amount, $request_id)) {
                $data = ['msg' => 'Request Confirmed, and account has been balanced', 'success' => true];

                $TpReq = (new TopupRequest())::filter('request_hash', 'amount', 'bearer_address')->where(['id', $request_id])->get();

                $msg01 = "
					<p style='text-align: center;'>
						<b>Request id:{$TpReq->request_hash}</b>, <b style='color: green;'>Status: CONFIRMED</b>
					</p>
					<p style='text-align: center;'>Your wallet has been top up by <b>NGN {$TpReq->amount}</b></p><br>";


                $Receiver_Uid = Wallet::filter('owned_by')->where(['public_key', $TpReq->bearer_address])->get()->owned_by;
                $email = ExtraUserInfo::filter('email')->where(['id', $Receiver_Uid])->get()->email;

                (new MailService())->sendTopUpRequest($email, $msg01);


            } else {
                throw new \Exception("Couldn't complete confirmation");
            }

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function ConfirmAirtimeTradeDueSelling(Param $param)
    {
        try {


            $request_id = $param->url()->request_id;

            $S = new SalesPoint;
            $Wallet = new Wallet;
            if ($S->filter('trade_key')->where(['id', $request_id], ['status', $S->getTradeStatus('COMPLETED')])->count() == 1) {
                $data = ['msg' => 'Request Confirmed, and account has been balanced', 'success' => true];
                return;
            }


            $SSS = $S->filter('trade_key', 'rawamt', 'extracharge', 'ifrom_address')->where(['id', $request_id], ['status', $S->getTradeStatus('PROGRESS')])->get();


            $T = new Transaction;
            if ($T->passTransaction(sodium_bin2hex($SSS->trade_key))) {

                // Complete trading
                $S->status = $S->getTradeStatus('COMPLETED');
                $S->updated_at = Carbon::now();
                $S->where(['id', $request_id])->update();


                // Initiate Service Charge
                $Service_Charge = $SSS->extracharge;
                if ($Service_Charge > 0) {

                    $TTT = $T->filter('ifrom', 'ito')->where(['trans_hash', $SSS->trade_key])->get();

                    $raw_user_public_key = sodium_bin2hex($TTT->ito);
                    $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where(['public_key', $raw_user_public_key])->get();

                    $payment_meta_info = [
                        'type' => $T->getTransactionTypes('AIRTIME_SERVICE_CHARGE'),
                        'to_address' => $TTT->ifrom,
                        'from_address' =>
                            [
                                'pubk' => sodium_hex2bin($from_prime_pk->public_key),
                                'prik' => sodium_hex2bin($from_prime_pk->private_key)
                            ]
                    ];
                    $T->addServiceTrans(
                        $payment_meta_info['type'],
                        'CONFIRMED',
                        [
                            $payment_meta_info['from_address']['pubk'],
                            $payment_meta_info['from_address']['prik']
                        ],
                        $payment_meta_info['to_address'],
                        $Service_Charge
                    );
                }


                $data = ['msg' => 'Request Confirmed, and account has been balanced', 'success' => true];


                $TransId = sodium_bin2hex($SSS->trade_key);
                $msg01 = "
					<p style='text-align: center;'>
						<b>Transaction id:{$TransId}</b>, <b style='color: green;'>Status: CONFIRMED</b>
					</p>
					<p style='text-align: center;'>Your airtime sale transaction has been confirmed, <br> Amount: <b>NGN {$SSS->rawamt}</b></p><br>";


                $Receiver_Uid = Wallet::filter('owned_by')->where(['public_key', $SSS->ifrom_address])->get()->owned_by;
                $email = ExtraUserInfo::filter('email')->where(['id', $Receiver_Uid])->get()->email;

                (new MailService())->sendTransactionAlertMail($email, $msg01);


            } else {
                throw new \Exception("Couldn't complete confirmation");
            }


        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function ConfirmAirtimeTradeDueBuying(Param $param)
    {

        try {

            $request_id = $param->url()->request_id;

            $S = new SalesPoint;
            $Wallet = new Wallet;
            if ($S->filter('trade_key')->where(['id', $request_id], ['status', $S->getTradeStatus('COMPLETED')])->count() == 1) {
                $data = ['msg' => 'Request Confirmed, and account has been balanced', 'success' => true];
                return;
            }


            $SSS = $S->filter('trade_key', 'rawamt', 'extracharge')->where(['id', $request_id], ['status', $S->getTradeStatus('PROGRESS')])->get();


            $T = new Transaction;
            if ($T->passTransaction(sodium_bin2hex($SSS->trade_key))) {

                // Complete trading
                $S->status = $S->getTradeStatus('COMPLETED');
                $S->updated_at = Carbon::now();
                $S->where(['id', $request_id])->update();


                // Initiate Service Charge
                $Service_Charge = $SSS->extracharge;
                if ($Service_Charge > 0) {

                    $TTT = $T->filter('ifrom', 'ito')->where(['trans_hash', $SSS->trade_key])->get();

                    $raw_user_public_key = sodium_bin2hex($TTT->ito);
                    $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where(['public_key', $raw_user_public_key])->get();

                    $payment_meta_info = [
                        'type' => $T->getTransactionTypes('AIRTIME_SERVICE_CHARGE'),
                        'to_address' => $TTT->ifrom,
                        'from_address' =>
                            [
                                'pubk' => sodium_hex2bin($from_prime_pk->public_key),
                                'prik' => sodium_hex2bin($from_prime_pk->private_key)
                            ]
                    ];
                    $T->addServiceTrans(
                        $payment_meta_info['type'],
                        'CONFIRMED',
                        [
                            $payment_meta_info['from_address']['pubk'],
                            $payment_meta_info['from_address']['prik']
                        ],
                        $payment_meta_info['to_address'],
                        $Service_Charge
                    );
                }

                $data = ['msg' => 'Request Confirmed, and account has been balanced', 'success' => true];


            } else {
                throw new \Exception("Couldn't complete confirmation");
            }


        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function CancelAirtimeTrade(Param $param)
    {

        try {

            $request_id = $param->url()->request_id;
            $S = new SalesPoint;
            if ($S->filter('trade_key')->where(['id', $request_id], ['status', $S->getTradeStatus('PROGRESS')])->count() == 1) {

                $T = new Transaction;

                $SSS = $S->filter('trade_key', 'rawamt', 'ifrom_address')->where(['id', $request_id], ['status', $S->getTradeStatus('PROGRESS')])->get();

                $T->where(['trans_hash', $SSS->trade_key])->delete();
                $S->where(['id', $request_id])->delete();

                $data = ['msg' => 'Request cancelled, and account has been ignored', 'success' => true];


                $TransId = sodium_bin2hex($SSS->trade_key);
                $msg01 = "
					<p style='text-align: center;'>
						<b>Transaction id:{$TransId}</b>, <b style='color: red;'>Status: CANCELLED</b>
					</p>
					<p style='text-align: center;'>Your airtime sale transaction has been cancelled, <br> Amount: <b>NGN {$SSS->rawamt}</b></p><br>";


                $Receiver_Uid = Wallet::filter('owned_by')->where(['public_key', $SSS->ifrom_address])->get()->owned_by;
                $email = ExtraUserInfo::filter('email')->where(['id', $Receiver_Uid])->get()->email;

                (new MailService())->sendTransactionAlertMail($email, $msg01);


                return;
            } else {
                return;
            }

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }
}

?>
