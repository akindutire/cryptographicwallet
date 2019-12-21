<?php

namespace src\adminhub\model;


use src\client\model\ExtraUserInfo;
use src\client\model\Transaction as TTransaction;
use zil\core\tracer\ErrorTracer;

class Transaction extends TTransaction
{

    /**
     * @param string $client_username
     * @return object
     */
    public function getSpecificClientTransactionHistory(string $client_username): array
    {

        try {


            $U = (new ExtraUserInfo())->as('ex')->with('Wallet AS wl', 'ex.id = wl.owned_by')->filter('wl.public_key')
                ->where(
                    ['ex.username', $client_username]
                )->get();

            $pk = $U->public_key;

            $binaryPk = sodium_hex2bin($pk);

            $inTransaction = self::all()->where(['ifrom', $binaryPk, 'OR'], ['ito', $binaryPk])->desc()->get('VERBOSE');
            $TerminalEntry = (new TopupRequest())->where(['bearer_address', $pk])->get('VERBOSE');
            $TerminalExit = (new CashoutRequest())->where(['receiver_address', $pk])->get('VERBOSE');

            $container = [];

            foreach ((array)$inTransaction as $transaction) {

                $transaction = (array)$transaction;

                $transaction = array_map(function ($entry) {


                    if (!mb_detect_encoding($entry, 'ASCII', true))
                        return sodium_bin2hex($entry);
                    else
                        return $entry;

                }, $transaction);

                array_push($container, $transaction);
            }

            return [
                'inTrans' => $container,
                'entryTrans' => $TerminalEntry,
                'exitTrans' => $TerminalExit,
                'holder' => $client_username,
                'holder_public_key' => $pk
            ];

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }
}


?>
