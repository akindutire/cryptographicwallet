<?php

namespace src\adminhub\model;


use RangeException;
use src\client\model\ExtraUserInfo;
use src\client\model\Wallet as EWallet;
use Throwable;
use TypeError;
use zil\core\tracer\ErrorTracer;
use zil\factory\Session;


class Wallet extends EWallet
{

    public function getAmtDetails(string $raw_public_key)
    {
        $user_id = self::filter('owned_by')->where(['public_key', $raw_public_key])->get()->owned_by;
        return self::filter('balance', 'credits', 'debits', 'acc_no')->where(['owned_by', $user_id])->get();
    }

    public function getPublickey(): string
    {
        try {

            $user_id = ExtraUserInfo::filter('id')->where(['email', Session::get('email')])->get()->id;

            $pk = (new self())::filter('public_key')->where(['owned_by', $user_id])->get()->public_key;
            return sodium_hex2bin($pk);
        } catch (TypeError $t) {
            new ErrorTracer($t);
        } catch (RangeException $t) {
            new ErrorTracer($t);
        } catch (Throwable $t) {
            new ErrorTracer($t);
        }
    }
}


?>
