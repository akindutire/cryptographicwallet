<?php

namespace src\client\controller;

use Carbon\Carbon;
use Exception;
use src\client\controller\api\UserController;
use src\client\middleware\Date;
use src\client\middleware\SecureApi;
use src\client\model\ActivityLog;
use src\client\model\CashoutRequest;
use src\client\model\ExtraUserInfo;
use src\client\model\SalesPoint;
use src\client\model\Settings;
use src\client\model\TopupRequest;
use src\client\model\Transaction;
use src\client\model\User;
use src\client\model\Wallet;
use src\client\service\MailService;
use src\client\service\RaveAdapter;
use Throwable;
use zil\core\facades\decorators\Hooks;
use zil\core\facades\helpers\Navigator;
use zil\core\facades\helpers\Notifier;
use zil\core\server\Param;
use zil\core\server\Response;
use zil\security\Encryption;
use zil\security\Sanitize;
use zil\security\Validation;

/**
 * @Controller:WalletApiController []
 */
class WalletApiController
{

    use Notifier, Navigator, Hooks;

    // Wallet details, including key,  balance...
    public function WalletDetails(Param $param)
    {

        try {

            $userId = (new ExtraUserInfo())->getUserId();

            $public_key = (new Wallet())->filter('public_key AS pk')->where(['owned_by', $userId])->get()->pk;


            $details = (new Wallet())->getWalletDetails($public_key);
            $details = (array)$details;

            $details = array_map(function ($entry) {

                if (!mb_detect_encoding($entry, 'ASCII', true))
                    return sodium_bin2hex($entry);
                else
                    return $entry;

            }, $details);

            $data = ['msg' => $details, 'success' => true];

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    // Wallet balance
    public function WalletBalance(Param $param)
    {

        try {

            $userId = (new ExtraUserInfo())->getUserId();

            $public_key = (new Wallet())->filter('public_key AS pk')->where(['owned_by', $userId])->get()->pk;

            $data = ['msg' => ['balance' => (new Wallet())->getBalance($public_key)], 'success' => true];

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }


    // Request Wallet top up via sharensell
    public function RequestTopUpViaShareNSell(Param $param)
    {

        try {

            $Validation = new Validation(['phone', 'text|minlength:11|maxlength:11|required'], ['amount', 'number|required|min:100'], ['network_provider', 'required']);

            if ($Validation->isPassed()) {

                $Pr = (new ActivityLog())->Log("[TOPUP-REQUEST: SHARE_N_SELL] Validation Passed", 'SUCCESS');
                if (is_null($Pr))
                    throw new Exception("An error occurred, process stream broken, please retry");

                $email = (new UserController())->getUserEmail();

                $providers = [];
                foreach ((new Settings())->getNetworkProviders() as $provider) {
                    array_push($providers, $provider->value);
                }

                if (!in_array($param->form()->network_provider, $providers))
                    throw new Exception("Network Provider not recognized from this domain");


                $msg = $param->form()->message !== null ? $param->form()->message : 'Top up request';
                $msg .= "<br> <b>Network Provider: </b>{$param->form()->network_provider},<br> Phone: {$param->form()->phone}";

                list($msg) = (new Sanitize())->clean([$msg]);

                $TopUpRequest = new TopupRequest;


                $service_charge = ((new Settings())->getTopUpViaSNS_AirtimeChargeRate($param->form()->network_provider) / 100) * $param->form()->amount;
                $amount = $param->form()->amount;


                $Rhash = (new Encryption())->generateShortHash();
                $TopUpRequest->request_hash = $Rhash;
                $TopUpRequest->bearer_address = sodium_bin2hex((new Wallet())->getPublickey());
                $TopUpRequest->mode = $TopUpRequest->availableModes('SHARE_N_SELL');
                $TopUpRequest->amount = floatval($amount);
                $TopUpRequest->slipidororderid = $TopUpRequest->request_hash;
                $TopUpRequest->voucherpinorairtimepin = $TopUpRequest->request_hash;
                $TopUpRequest->note = $msg;
                $TopUpRequest->service_charge = $service_charge;
                $TopUpRequest->status = $TopUpRequest->availableStatus('PENDING');

                if ($TopUpRequest->create()) {
                    // Mail to user


                    $msg01 = "<p style='text-align: center;'><b>Request id:{$Rhash}</b></p><br><p style='text-align : center;'><b>[SHARE N SELL]</b></p><br><p style='text-align: center;'> Top up request of order <b>{$Rhash}</b> is being processed, <b>NGN {$amount}</b> of <b>{$param->form()->network_provider}</b> value would be remitted to your wallet after confirmation</p><br><p style='color: red;'><b>Status: PENDING</b></p>";

                    (new MailService())->sendTopUpRequest($email, $msg01);

                    (new ActivityLog())->updateLog($Pr, "[TOPUP-REQUEST: SHARE_N_SELL]: {$msg01}", 'SUCCESS');

                    $data = ['msg' => "Wait while your request is being processed", 'success' => true];
                } else {
                    throw new Exception("Couldn't complete request, please retry");
                }

            } else {
                $data = ['msg' => $Validation->getErrorString(), 'success' => false];
            }


        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
            if (!is_null($Pr))
                (new ActivityLog())->updateLog($Pr, "[TOPUP-REQUEST: SHARE_N_SELL] {$data['msg']}", 'FAIL');

        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    // Request Wallet top up via airtime pin
    public function RequestTopUpViaAirtimePin(Param $param)
    {

        try {

            $Validation = new Validation(['airtime_pin', 'text|required'], ['amount', 'number|required|min:100'], ['network_provider', 'required']);

            if ($Validation->isPassed()) {

                $Pr = (new ActivityLog())->Log("[TOPUP-REQUEST: AIRTIME] Validation Passed", 'SUCCESS');
                if (is_null($Pr))
                    throw new Exception("An error occurred, process stream broken, please retry");

                $providers = [];
                foreach ((new Settings())->getNetworkProviders() as $provider) {
                    array_push($providers, $provider->value);
                }

                if (!in_array($param->form()->network_provider, $providers))
                    throw new Exception("Network Provider not recognized from this domain");


                $msg = $param->form()->message !== null ? $param->form()->message : 'Top up request';
                $msg .= "<br> <b>Network Provider: </b>{$param->form()->network_provider}";

                list($airtime_pin, $msg) = (new Sanitize())->clean([$param->form()->airtime_pin, $msg]);
                $TopUpRequest = new TopupRequest;

                if ($TopUpRequest->isExists(['voucherpinorairtimepin', $airtime_pin], ['mode', 'AIRTIME_PIN']))
                    throw new Exception("Pin already Exist");


                $service_charge = ((new Settings())->getTopUpViaSNS_AirtimeChargeRate($param->form()->network_provider) / 100) * $param->form()->amount;
                $amount = $param->form()->amount;

                $Rhash = (new Encryption())->generateShortHash();
                $TopUpRequest->request_hash = $Rhash;
                $TopUpRequest->bearer_address = sodium_bin2hex((new Wallet())->getPublickey());
                $TopUpRequest->mode = $TopUpRequest->availableModes('AIRTIME_PIN');
                $TopUpRequest->amount = floatval($amount);
                $TopUpRequest->service_charge = $service_charge;
                $TopUpRequest->voucherpinorairtimepin = $airtime_pin;
                $TopUpRequest->note = $msg;
                $TopUpRequest->status = $TopUpRequest->availableStatus('PENDING');

                if ($TopUpRequest->create()) {
                    // Mail to user


                    $msg01 = "<p style='text-align: center;'><b>Request id:{$Rhash}</b></p><br><p style='text-align : center;'><b>[AIRTIME PIN: $airtime_pin}</b></p><p style='text-align: center;'> Top up request is being processed, NGN {$param->form()->amount} of {$param->form()->network_provider} value would be remitted to your wallet after confirmation</p><br><p style='color: red;'><b>Status: PENDING</b></p>";

                    (new MailService())->sendTopUpRequest((new UserController())->getUserEmail(), $msg01);

                    (new ActivityLog())->updateLog($Pr, "[TOPUP-REQUEST: AIRTIME]: {$msg01}", 'SUCCESS');

                    $data = ['msg' => "Wait while your request is being processed", 'success' => true];
                } else {
                    throw new Exception("Couldn't complete request, please retry");
                }

            } else {
                $data = ['msg' => $Validation->getErrorString(), 'success' => false];
            }


        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
            if (!is_null($Pr))
                (new ActivityLog())->updateLog($Pr, "[TOPUP-REQUEST: AIRTIME] {$data['msg']}", 'FAIL');

        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    // Request Wallet top up via bank
    public function RequestTopUpViaBank(Param $param)
    {

        try {

            $infimum = (new Settings())->getMinTopUpThroughBank();
            $Validation = new Validation(['transaction_id', 'text|required'], ['amount', "number|required|min:{$infimum}"], ['payee_name', 'text|required'], ['bank_paid_to', 'text|required']);

            if ($Validation->isPassed()) {

                $Pr = (new ActivityLog())->Log("[TOPUP-REQUEST: BANK] Validation Passed", 'SUCCESS');
                if (is_null($Pr))
                    throw new Exception("An error occurred, process stream broken, please retry");


                $msg = 'Top up request<br>';

                $message0 = "";
                if (isset($param->form()->message))
                    $message0 = $param->form()->message;

                list($transaction_id, $payee_name, $message) = (new Sanitize())->clean([$param->form()->transaction_id, $param->form()->payee_name, $message0]);

                $TopUpRequest = new TopupRequest;

                if ($TopUpRequest->isExists(['slipidororderid', $transaction_id], ['mode', 'BANK']))
                    throw new Exception("Transaction already Exist");


                if ($TopUpRequest->isExists(['bearer_address', sodium_bin2hex((new Wallet())->getPublickey())], ['status', $TopUpRequest->availableStatus('PENDING')], ['mode', $TopUpRequest->availableModes('BANK')]))
                    throw new Exception("You still have pending topup through bank, Cancel existing topup first");


                $Rhash = (new Encryption())->generateShortHash();
                $TopUpRequest->request_hash = $Rhash;
                $TopUpRequest->bearer_address = sodium_bin2hex((new Wallet())->getPublickey());
                $TopUpRequest->mode = $TopUpRequest->availableModes('BANK');
                $TopUpRequest->amount = floatval($param->form()->amount);
                $TopUpRequest->service_charge = 0.00;
                $TopUpRequest->slipidororderid = $transaction_id;
                $TopUpRequest->bearer = $payee_name;
                $TopUpRequest->note = "Paid through " . $param->form()->bank_paid_to . "<br>{$msg}<br>{$message}";
                $TopUpRequest->status = $TopUpRequest->availableStatus('PENDING');


                if ($TopUpRequest->create()) {

                    // Mail to user
                    $msg01 = "<p style='text-align: center;'><b>Request id:{$Rhash}</b></p><br><p style='text-align: center;'><b>Slip no: $transaction_id</b></p><p style='text-align: center;'> Top-up request via bank mode is being processed, NGN {$param->form()->amount} would be remitted to your wallet after confirmation</p><br><p style='color: red;'><b>Status: PENDING</b></p>";

                    (new ActivityLog())->updateLog($Pr, "[TOPUP-REQUEST: AIRTIME]: {$msg01}", 'SUCCESS');

                    $data = ['msg' => "Wait while your request is being processed", 'success' => true];

                    $email = (new UserController())->getUserEmail();
                    (new MailService())->sendTopUpRequest($email, $msg01);

                } else {
                    throw new Exception("Couldn't complete request, please retry");
                }

            } else {
                $data = ['msg' => $Validation->getErrorString(), 'success' => false];
            }

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];

            if (!is_null($Pr))
                (new ActivityLog())->updateLog($Pr, "[TOPUP-REQUEST: BANK] {$data['msg']}", 'FAIL');

        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    public function TopUpViaCard(Param $param)
    {
        try {

            $min = (new Settings())->getMinTopUp();

            if (floatval($param->url()->amount) >= $min) {

                $Pr = (new ActivityLog())->Log("[TOPUP-REQUEST: CARD] Validation Passed", 'SUCCESS');
                if (is_null($Pr))
                    throw new Exception("An error occurred, process stream broken, please retry");

                $TopUpRequest = new TopupRequest();


                // Confirm if transaction is already done
                $ReqCount = $TopUpRequest->filter('id', 'slipidororderid', 'amount')->where(['slipidororderid', $param->url()->transactionRef], ['mode', 'CARD'], ['status', 'CONFIRMED'])->count();

                if ($ReqCount > 0) {
                    $data = ['msg' => "Request already confirmed, and account has been balanced, NGN{$param->url()->amount} has been credited", 'success' => true];

                    return;
                }


                // Verify Payment

                $PaymentVerificationResult = (new RaveAdapter())->confirmCardPaymentToRave($param->url()->transactionRef);

                $status = "PENDING";
                if ($PaymentVerificationResult->status == "success") {
                    $status = "CONFIRMED";
                }

                // if($PaymentVerificationResult->data->amount != $param->url()->amount)
                // 	throw new \Exception("Transaction amount is not same as amount passed, expecting {$PaymentVerificationResult->data->amount}");


                $Rhash = (new Encryption())->generateShortHash();
                $TopUpRequest->request_hash = $Rhash;
                $TopUpRequest->bearer_address = sodium_bin2hex((new Wallet())->getPublickey());
                $TopUpRequest->mode = $TopUpRequest->availableModes('CARD');
                $TopUpRequest->amount = $param->url()->amount;
                $TopUpRequest->status = $TopUpRequest->availableStatus($status);
                $TopUpRequest->service_charge = (new Settings())->getTopViaCardChargeRate();
                $TopUpRequest->slipidororderid = $param->url()->transactionRef;
                $TopUpRequest->note = "[CARD]-Transaction-Ref: {$param->url()->transactionRef}";
                $TopUpRequest->created_at = Carbon::now();

                if ($TopUpRequest->create()) {

                    (new ActivityLog())->updateLog($Pr, "[TOPUP-REQUEST: CARD]: {$Rhash} has been successful, {$param->url()->transactionRef}", 'SUCCESS');

                    if ($status == "CONFIRMED") {

                        if ((new TopupRequest())->confirmCardTopupAsPaid($param->url()->amount, $TopUpRequest->lastInsert())) {


                            $data = ['msg' => "Request {$status}, and account has been balanced, NGN{$param->url()->amount} has been credited", 'success' => true];

                            $msg01 = "<p style='text-align: center;'><b>Request id:{$Rhash}</b></p><br><p style='text-align: center;'>Top-up request via card mode has been processed, NGN {$param->url()->amount} has been remitted to your wallet</p><br><p style='color: green; text-align: center;'><b>Status: {$status}</b></p>";

                            $email = (new UserController())->getUserEmail();

                            (new MailService())->sendTopUpRequest($email, $msg01);

                        } else {
                            throw new Exception("Couldn't complete confirmation");
                        }


                    } else {
                        throw new Exception("Transaction doesn't occur for transaction {$param->url()->transactionRef}, wallet not credited");
                    }
                } else {
                    throw new Exception("Couldn't complete request, please contact admin");
                }

            } else {
                $data = ['msg' => "Unsupported amount, amount must be at least NGN{$min}", 'success' => false];
            }


        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];

            if (!is_null($Pr))
                (new ActivityLog())->updateLog($Pr, "[TOPUP-REQUEST: CARD] {$data['msg']}", 'FAIL');

        } finally {
            echo Response::fromApi($data, 200);
        }
    }


    public function TopUps(Param $param)
    {
        try {
            $data = ['msg' => (new TopupRequest())->getTopUps(), 'success' => true];

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);

        }

    }

    public function CashOuts(Param $param)
    {
        try {
            $data = ['msg' => (new CashoutRequest())->getCashouts(), 'success' => true];

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }


    public function CashOutFund(Param $param)
    {
        try {

            if ((new User())->isTransactionLocked())
                throw new Exception("Sorry, you still have pending transaction");


            $public_key = $param->url()->wallet_key;

            $amount = floatval($param->form()->amount);
            if ($amount > 0) {

                $CashOutReq = new CashoutRequest;
                if ((new Wallet())->isSufficientBalance($amount) && !$CashOutReq->isTotalProspectCashOutExceedsBalance($amount)) {

                    if (!(new Wallet())->isBankDetailsAvailable($public_key))
                        throw new Exception("Cash out request not granted, please update your bank details");


                    if ($CashOutReq->request($amount)) {
                        $data = ['msg' => 'Cash out request granted, wait for confirmation', 'success' => true];
                        /**Mail sent within Cash out Request model on success */

                    } else {
                        throw new Exception("Cash out request not granted, please retry");
                    }
                } else {
                    throw new Exception("Error: Insufficient fund / Total prospect cash out exceeds wallet");
                }
            } else {
                throw new Exception("Error: Can\'t cash out NGN{$amount}");

            }

        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    /**
     * Cancellation
     */

    public function CancelCashoutRequest(Param $param)
    {

        if (isset($param->url()->cashout_id)) {
            (new CashoutRequest())->cancelReq($param->url()->cashout_id);
        }

        $this->goBack();

    }

    public function CancelTopupRequest(Param $param)
    {
        if (isset($param->url()->topup_id)) {
            (new TopupRequest())->cancelReq($param->url()->topup_id);
        }

        $this->goBack();
    }

    public function ExCancelCashoutRequest(Param $param)
    {
        try {
            if (isset($param->url()->cashout_id)) {

                if ((new CashoutRequest())->cancelReq($param->url()->cashout_id))
                    $data = ['msg' => 'Cashout Request Cancelled', 'success' => true];
                else
                    throw new Exception("Confirmed topup cannot be cancelled or revoked");

            } else {
                throw new Exception("Invalid request id");
            }
        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function ExCancelTopupRequest(Param $param)
    {
        try {
            if (isset($param->url()->topup_id)) {
                if ((new TopupRequest())->cancelReq($param->url()->topup_id)) {
                    $data = ['msg' => "Topup has been cancelled", 'success' => true];
                } else {
                    throw new Exception("Confirmed topup cannot be cancelled or revoked");
                }
            } else {
                throw new Exception("Invalid request id");
            }
        } catch (Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

        $this->goBack();
    }

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
//                header('Content-Type: application/json');
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
        (new SalesPoint())->SalesGC();
        (new Transaction())->TransactionGC();
    }

}
