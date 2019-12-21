<?php

namespace src\adminhub\model;


use src\client\model\TopupRequest as TTopupRequest;
use zil\core\tracer\ErrorTracer;

class TopupRequest extends TTopupRequest
{


    public function getPendingTopups()
    {
        try {
            return (new self())->as('tq')->with('Wallet as wl', 'tq.bearer_address = wl.public_key')->with('ExtraUserInfo as ex', 'ex.id = wl.owned_by')->with('User as us', 'us.email = ex.email')->filter('tq.request_hash', 'tq.status', 'tq.id', 'tq.amount', 'tq.mode', 'tq.slipidororderid', 'tq.bearer', 'tq.voucherpinorairtimepin', 'tq.note', 'tq.created_at', 'ex.phone', 'ex.email', 'ex.name', 'us.photo')->where(['tq.status', 'PENDING'])->desc()->get('VERBOSE');
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getConfirmedTopups()
    {
        try {
            return (new self())->as('tq')->with('Wallet as wl', 'tq.bearer_address = wl.public_key')->with('ExtraUserInfo as ex', 'ex.id = wl.owned_by')->with('User as us', 'us.email = ex.email')->filter('tq.request_hash', 'tq.id', 'tq.amount', 'tq.mode', 'tq.slipidororderid', 'tq.bearer', 'tq.voucherpinorairtimepin', 'tq.note', 'tq.created_at', 'ex.phone', 'ex.email', 'ex.name', 'us.photo')->where(['tq.status', 'CONFIRMED'])->desc()->get('VERBOSE');
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getRejectedTopups()
    {
        try {
            return (new self())->as('tq')->with('Wallet as wl', 'tq.bearer_address = wl.public_key')->with('ExtraUserInfo as ex', 'ex.id = wl.owned_by')->with('User as us', 'us.email = ex.email')->filter('tq.request_hash', 'tq.status', 'tq.id', 'tq.amount', 'tq.mode', 'tq.slipidororderid', 'tq.bearer', 'tq.voucherpinorairtimepin', 'tq.note', 'tq.created_at', 'ex.phone', 'ex.email', 'ex.name', 'us.photo')->where(['tq.status', 'REJECTED'])->desc()->get('VERBOSE');
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function markRequestAsConfirmed($request_id): bool
    {

        $this->status = 'CONFIRMED';
        if ($this->where(['id', $request_id])->update() == 1)
            return true;

        return false;
    }

    public function confirmTopupAsPaid(float $amount, int $req_id): bool
    {


        $Wallet = new Wallet;

        $public_key = $Wallet->getPublickey();
        $TpRq = self::filter('bearer_address', 'mode', 'service_charge')->where(['id', $req_id])->get();

        $raw_user_public_key = $TpRq->bearer_address;

        $Service_Charge = $TpRq->service_charge;

        if (($TpRq->mode == $this->availableModes('SHARE_N_SELL')) || ($TpRq->mode == $this->availableModes('AIRTIME_PIN'))) {

            $Service_Charge = $TpRq->service_charge;
        }

        if ($Wallet->topUp(sodium_hex2bin($raw_user_public_key), $amount)) {
            $this->markRequestAsConfirmed($req_id);

            if ($Service_Charge > 0) {

                // Initiate Top Service Charge
                $Transaction = new Transaction;

                $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where(['public_key', $raw_user_public_key])->get();

                $payment_meta_info = [
                    'type' => (new Transaction())->getTransactionTypes('TOPUP_SERVICE_CHARGE'),
                    'to_address' => $public_key,
                    'from_address' =>
                        [
                            'pubk' => sodium_hex2bin($from_prime_pk->public_key),
                            'prik' => sodium_hex2bin($from_prime_pk->private_key)
                        ]
                ];
                $Transaction->addServiceTrans(
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

            return true;
        } else {
            return false;
        }


    }

    public function RejectTopup(int $request_id): bool
    {
        try {

            $this->status = $this->availableStatus('REJECTED');
            if (strlen($this->status) == 0 || $this->status == null)
                throw new \Exception("Possible rejection: Unknown event {$this->status}");

            if ($this->where(['id', $request_id])->update() == 1)
                return true;

            return false;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

}


?>
