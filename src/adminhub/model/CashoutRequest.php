<?php

namespace src\adminhub\model;


use src\client\model\CashoutRequest as CCashoutRequest;

class CashoutRequest extends CCashoutRequest
{


    public function getPaidCashouts()
    {

        return (new self())->as('ctq')->with('Wallet as wl', 'ctq.receiver_address = wl.public_key')->with('ExtraUserInfo as ex', 'ex.id = wl.owned_by')->with('User as us', 'us.email = ex.email')->filter('ctq.request_hash', 'ctq.id', 'ctq.amount', 'ctq.created_at', 'wl.acc_no', 'wl.balance', 'wl.acc_name', 'wl.bank', 'ex.phone', 'ex.email', 'ex.name', 'us.photo')->where(['paid', '<>', 0])->desc()->get('VERBOSE');

    }

    public function getUnpaidCashouts()
    {

        return (new self())->as('ctq')->with('Wallet as wl', 'ctq.receiver_address = wl.public_key')->with('ExtraUserInfo as ex', 'ex.id = wl.owned_by')->with('User as us', 'us.email = ex.email')->filter('ctq.request_hash', 'ctq.id', 'ctq.amount', 'ctq.created_at', 'wl.acc_no', 'wl.balance', 'wl.acc_name', 'wl.bank', 'ex.phone', 'ex.email', 'ex.name', 'us.photo')->where(['paid', false])->desc()->get('VERBOSE');

    }

    public function confirmCashoutAsPaid(float $amount, int $req_id): bool
    {


        $Wallet = new Wallet;

        $public_key = $Wallet->getPublickey();
        $raw_user_public_key = self::filter('receiver_address')->where(['id', $req_id])->get()->receiver_address;

        // Check if payee has sufficient balance

        $payee_balance = $Wallet->filter('balance')->where(['public_key', $raw_user_public_key])->get()->balance;

        $cashoutservicecharge = (new Settings())->getCashOutServiceCharge();
        if (($payee_balance >= ($amount + $cashoutservicecharge)) && !$this->isTotalProspectCashOutExceedsBalance($amount)) {

            if ($Wallet->cashOut(sodium_hex2bin($raw_user_public_key), $amount)) {

                $this->markRequestAsPaid($req_id);

                $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where(['public_key', $raw_user_public_key])->get();

                $Transaction = new Transaction;
                $payment_meta_info = [
                    'type' => $Transaction->getTransactionTypes('CASH_OUT_SERVICE_CHARGE'),
                    'to_address' => $Wallet->getPublickey(),
                    'from_address' =>
                        [
                            'pubk' => sodium_hex2bin($from_prime_pk->public_key),
                            'prik' => sodium_hex2bin($from_prime_pk->private_key)
                        ],
                    'trusted' => true,
                    'freeze' => false
                ];

                // transfer between prime wallet and ordinary without system event - NO_SESSION REQ.
                $Trans = $Transaction->addTransferTrans(
                    $payment_meta_info['type'],
                    'CONFIRMED',
                    [
                        $payment_meta_info['from_address']['pubk'],
                        $payment_meta_info['from_address']['prik']
                    ],
                    $payment_meta_info['to_address'],
                    (new Settings())->getCashOutServiceCharge(),
                    $payment_meta_info['trusted'],
                    $payment_meta_info['freeze']
                );


                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }


    }


}


?>
