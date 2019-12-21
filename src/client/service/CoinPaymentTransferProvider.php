<?php
namespace src\client\service;

use src\client\model\Settings;
use zil\core\tracer\ErrorTracer;
use \zil\factory\Logger;
use zil\factory\Utility;


/**
 * @Service:CoinPaymentSvc []
*/

class CoinPaymentTransferProvider{


    private $apikey = '';
    private $version = 1;
    private $pub_k = "271b7e77dff54fb4dba01acd63959c6fbb067374d96757a120bcb26cee624832";
    private $priv_k = "19a0c3A4b617FdD63b85Db20990bB415f398870348c3Aba9618Db7c155966605";
    private $ipn = "";

    public function __construct(){

        require_once('dependency/php-coinpayments/coinpayments-php-master/src/CoinpaymentsAPI.php');
        require_once('dependency/php-coinpayments/coinpayments-php-master/src/keys_example.php');

        $this->ipn = Utility::route('api/cptp/ipn');
    }

    public function isBitcoinTransfered(?string $transaction_id, float $expected_amount) : bool{
        try{

            if(is_null($transaction_id))
                return false;

            $CPTP = new \CoinpaymentsAPI($this->priv_k, $this->pub_k, 'json');
            $response = $CPTP->GetTxInfoSingle($transaction_id);

            if($response['error'] == 'ok'){

                if($response['result']['status'] == 1 || $response['result']['status'] == 2)
                    return true;
                else
                    return false;

            }else{
                Logger::Init();
                Logger::Log($response);
                Logger::kill();
                return false;
            }

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }

    }

    public function ProposeBitcoinAcceptance(float $amount, string $currency, string $client_email, string $client_name){

        try {
            $CPTP = new \CoinpaymentsAPI($this->priv_k, $this->pub_k, 'json');

            $address = '';
            $item_name = 'BITCOIN TRADE';
            $item_number = '';
            $custom = '';
            $invoice = '';
            $ipn_url = $this->ipn;

            $response = $CPTP->CreateComplexTransaction($amount, $currency, $currency, $client_email, $address, $client_name, $item_name, $item_number, $invoice, $custom, $ipn_url);

            if($response['error'] == 'ok'){
                return $response['result'];
            }else{
                Logger::Init();
                Logger::ELog($response);
                Logger::kill();
                return false;
            }

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }

    }

    public function ProposeBitcoinWithdrawal( float $amount, string $to_Address, string $currency = 'BTC') : array {
        try{

            $CPTP = new \CoinpaymentsAPI($this->priv_k, $this->pub_k, 'json');

            $criteria = (new Settings())->getBitcoinWithdrawalHugeCriteria();
            $add_tx_fee = 0;
            if($amount >= $criteria) {
                $add_tx_fee = 1;
            }

            $withdrawals = [
                'wd1' => [
                    'amount' => $amount,
                    'add_tx_fee' => $add_tx_fee,
                    'currency' => $currency,
                    'address' => $to_Address,
                    'auto_confirm' => 1,
                    'note' => "Naijasub has transferred to {$to_Address}"
                ]
            ];

            $response = $CPTP->CreateMassWithdrawal($withdrawals);
            Logger::Init();
            Logger::ELog("Special Header, test btc withdrawal",$response);
            Logger::kill();

            if($response['error'] == 'ok'){

                $status = true;
                $note = '';

                foreach ($response['result'] as $single_withdrawal_result => $single_withdrawal_result_array) {
                    if ($single_withdrawal_result_array['error'] == 'ok') {

                        $this_id = $single_withdrawal_result_array['id'];
                        $this_status = $single_withdrawal_result_array['status'];
                        $this_amount = $single_withdrawal_result_array['amount'];

                        $note = "Withdrawal successful";
                        $status = true;

                    } else {
                        Logger::Init();
                        Logger::ELog($single_withdrawal_result_array['error']);
                        Logger::kill();

                        $note = $single_withdrawal_result_array['error']."\n";
                        $status = false;
                    }
                }
                return ['status' => $status, 'note' => $note ];
            }else{
                Logger::Init();
                Logger::ELog($response);
                Logger::kill();

                return ['status' => false, 'note' => "An error occurred, contact admin" ];
            }

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function convert(float $amount, string $from = 'BTC', string $to = 'USD'){
        try{
            $CPTP = new \CoinpaymentsAPI($this->priv_k, $this->pub_k, 'json');
            $conversion_support = $CPTP->GetConversionLimits($from, $to);

            var_dump($conversion_support);

            if ($conversion_support['error'] == 'ok') {
                // See if the amount being attempted for conversion is within the minimum and maximum
                if ($amount >= $conversion_support['result']['min'] && $amount <= $conversion_support['result']['max']) {

                    // Make call to API to create the coin conversion
                    $conversion = $CPTP->ConvertCoins($amount, $from, $to);

                    // Check result of the API call for executing the coin conversion
                    if ($conversion['error'] == 'ok') {

                        Logger::Init();
                        Logger::ELog($conversion['result']);
                        Logger::kill();

                    } else {
                        Logger::Init();
                        Logger::ELog($conversion['result']);
                        Logger::kill();

                       return 0;
                    }
                } else {
                    throw new \Exception('The amount of currency "' . $from . '" is not within the minimum (' . $conversion_support['result']['min'] . ') and maximum (' . $conversion_support['result']['max'] . ') limits for conversion. Please adjust your amount and try again or try a different conversion pairing.');
                }
            }else{
                throw new \Exception("An error occurred while attempting to initiate currency conversion");
            }

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function getBalance() : float {
        try{
            $CPTP = new \CoinpaymentsAPI($this->priv_k, $this->pub_k, 'json');
            $response = $CPTP->GetAllCoinBalances();

//            Logger::Init();
//            Logger::ELog($response['result']);
//            Logger::kill();

            if($response['error'] == 'ok'){
                return $response['result']['BTC']['balancef'];
            }else{
                return 0.00;
            }

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function getDefaultBTCAddress() : string {
        try{
            $CPTP = new \CoinpaymentsAPI($this->priv_k, $this->pub_k, 'json');
            $response = $CPTP->GetDepositAddress('BTC');

            if($response['error'] == 'ok'){
                return $response['result']['address'];
            }else{
                return 'Not found';
            }
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }
}
