<?php

namespace src\adminhub\model;


use src\client\model\SalesPoint as SSalesPoint;


class SalesPoint extends SSalesPoint
{


    public function getTrade(string $trade_key)
    {

        $pk = sodium_bin2hex((new Wallet())->getPublickey());

        return self::all()->where(
            ['trade_key', $trade_key],
            [
                ['ito_address', $pk, 'OR'],
                ['ifrom_address', $pk]

            ]
        )->get('VERBOSE');
    }

    public function getTradeByType(string $trade_type)
    {

        self::$key = 'id';

        $pk = sodium_bin2hex((new Wallet())->getPublickey());

        $ST = (new self())->as('s')->with('Transaction as t', 't.trans_hash=s.trade_key')->filter('s.id', 's.ifrom_address', 's.ito_address', 's.trade_key', 's.trade_type', 's.valueorqtyexchanged', 's.rawamt', 's.icurrency', 's.proofoftrade', 's.status', 's.tradehistory', 't.note', 't.status as tst', 's.created_at', 's.updated_at')->where(
            ['s.trade_type', $trade_type],
            [
                ['ito_address', $pk, 'OR'],
                ['ifrom_address', $pk]
            ]
        )->orderBy('created_at')->desc()->get('VERBOSE');

        return $ST;
    }

}


?>
