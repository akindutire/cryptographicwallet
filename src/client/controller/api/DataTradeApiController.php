<?php
namespace src\client\controller;

use Carbon\Carbon;
use src\client\middleware\Date;
use src\client\middleware\SecureApi;
use src\client\middleware\TransactionDailyCounterAndRestrictionChecker;
use src\client\model\ActivityLog;
use src\client\model\Affiliate;
use src\client\model\Authtoken;
use src\client\model\DataCard;
use src\client\model\ExtraUserInfo;
use src\client\model\Membership_plan;
use src\client\model\Product;
use src\client\model\SalesPoint;
use src\client\model\Settings;
use src\client\model\Transaction;
use src\client\model\User;
use src\client\model\Wallet;
use src\client\service\MailService;
use src\client\service\SmsProvider;
use \zil\core\server\Param;
use \zil\core\server\Response;
use zil\core\tracer\ErrorTracer;
use zil\factory\Logger;
use \zil\factory\View;
use \zil\core\facades\helpers\Notifier;
use \zil\core\facades\helpers\Navigator;
use \zil\core\facades\decorators\Hooks;

use src\client\Config;
use zil\security\Encryption;
use zil\security\Validation;

/**
 *  @Controller:DataBundleApiController []
*/

class DataTradeApiController{

    use Notifier, Navigator, Hooks;


    public function CalculateDataCardUnitPrice(Param $param){
            try{
              $V = new Validation(
                  ['network_provider', 'required'],
                  ['data_products', 'required'],
                  ['units', 'required|min:1']
              )  ;

              if($V->isPassed()){

                  list($networkProviderId, $networkProvider) = explode('+', $param->form('network_provider') );
                  $product = $param->form('data_products');
                  $units = $param->form('units');

                  $DC = new DataCard();


                  if($DC->filter('price')->where(  ['product', $product], ['network_provider', $networkProvider], ['status_code', 0]  )->count() == 0) {

                      $M = new MailService();

                      $mailAccounts = $M->mailAccounts();
                      $to = $mailAccounts['NAIJASUB2'];
                      $date = date('D-M-Y, H:i', time());
                      $message = "{$networkProvider},{$product} Data E-PIN Exhausted, Goto admin and reload Asap";
                      (new MailService())->sendMail($to, "{$date}: {$networkProvider},{$product} Data E-PIN Exhausted", "$message");


                      throw new \Exception("Pin not available");
                  }else {
                      $unitPrice = $DC->filter('price')->where(['product', $product], ['network_provider', $networkProvider], ['status_code', 0])->take(1)->get()->price;
                  }
                  $amount = $unitPrice * $units;

                  $data = [ 'msg' => $amount, 'success'=> true ];

              }else{
                  throw new \Exception($V->getErrorString());
              }
            } catch(\Throwable $t){
                $data = [ 'msg' => $t->getMessage(), 'success' => false ];
            } finally {
                echo Response::fromApi($data, 200);
            }
        }


    /**
     * @param Param $param
     */
    public function LoadEPin(Param $param) {
        try{

            $V = new Validation(
                ['network_provider', 'required'],
                ['data_products', 'required'],
                ['pin', 'required'],
                ['phone', 'required|minlength:11']
            );

            if($V->isPassed()) {

                $timestamp = time();
                $phone = $param->form('phone');
                list($networkProviderId, $networkProvider) = explode('+', $param->form('network_provider') );
                $product = $param->form('data_products');

                $key = "{$networkProvider}_{$product}";
                $to = (new Settings())->getPhoneResponderNoForDataEPin($key);

                if(trim($product) == '1GB') {
                    $message = "*1*{$param->form('pin')}*{$phone}#";
                }else if(trim($product) == '2GB'){
                    $message = "*2*{$param->form('pin')}*{$phone}#";
                }

                $result = (new SmsProvider())->sendSMSViaBulk($to, $message);
                if ($result->status == "success") {
                    $data = ['msg' => 'Data Pin has been processed', 'success' => true];
                } else {
                    throw new \Exception("Data Pin was not processed, try again");
                }

            } else{
                throw new \Exception($V->getErrorString());
            }

        } catch (\Throwable $t){
            $data = [ 'msg' => $t->getMessage(), 'success' => false ];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    /**
     * Process data bundle trade
     * @param Param $param
     */
    public function BuyDataEPin(Param $param){
        try {

            $V = new Validation(
                ['network_provider', 'required'],
                ['data_products', 'required'],
                ['units', 'required|min:1']
            );

            if ($V->isPassed()) {
                /**
                 * Run checksum
                 */
                $checksum = null;
                if (isset($param->form()->timestamp)) {
                    $timestamp = $param->form()->timestamp;

                    $checksum = (new Authtoken())->getCheckSum($timestamp);

                    if ((new Transaction())->isExists(['checksum', $checksum])) {
                        $data = ['msg' => "Transaction already completed", 'success' => true];
                        return;
                    }
                }

                $Pr = (new ActivityLog())->Log("[DATA E-PIN] Validation Passed", 'SUCCESS');
                if (is_null($Pr))
                    throw new \Exception("An error occurred, process stream broken, please retry");


                list($networkProviderId, $networkProvider) = explode('+', $param->form('network_provider'));
                $product = $param->form('data_products');
                $units = $param->form('units');

                $DC = new DataCard();

                if ($DC->filter('price')->where(['product', $product], ['network_provider', $networkProvider], ['status_code', 0])->count() == 0){

                    $M = new MailService();

                    $mailAccounts = $M->mailAccounts();
                    $to = $mailAccounts['NAIJASUB2'];
                    $date = date('D-M-Y, H:i', time());
                    $message = "{$networkProvider},{$product} Data E-PIN Exhausted, Goto admin and reload Asap";
                    (new MailService())->sendMail($to, "{$date}: {$networkProvider},{$product} Data E-PIN Exhausted", "$message");

                    throw new \Exception("Pins not available");
                } else{
                    $unitPrice = $DC->filter('price')->where(['product', $product], ['network_provider', $networkProvider], ['status_code', 0])->take(1)->get()->price;
                }
              $amount = $unitPrice * $units;

                $W = new Wallet();
                $pk = $W->getPublickey();

                $balance = $W->getBalance(sodium_bin2hex($W->getPublickey()));

                if($balance < $amount)
                    throw new \Exception("Insufficient balance, Cost: NGN {$amount}");

                $T = new Transaction();

                  /**
                   * Render service
                   */

              $PinState = (new DataCard())->buy($units, $networkProvider, $product);

              $unit_sold = $PinState['unit_sold'];
              $PinSold = $PinState['pin_sold'];
              $PinString = $PinState['pin_string'];

              /**
               * Successful sale
               */
                $amount = $unit_sold * $unitPrice;


                $message = "{$unit_sold} {$networkProvider} Data E-Pin<br>{$PinString}";
                  /**
                   * Log Activity
                   */
                  (new ActivityLog())->updateLog($Pr,"[DATA E-PIN] Service Rendered \n {$message}", 'SUCCESS');


                  $from_prime_pk = $W->filter('public_key', 'private_key')->where( ['public_key', sodium_bin2hex($pk) ] )->get();

                  $payment_meta_info = [
                      'type' => $T->getTransactionTypes('DATA_E_PIN_TRADE'),
                      'to_address' => $W->getAnyAdminPublickey(),
                      'from_address'=>
                          [
                              'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                              'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                          ]
                  ];

                  sleep(1);

                  $ServiceTrans = $T->addServiceTrans(
                      $payment_meta_info['type'],
                      'CONFIRMED',
                      [
                          $payment_meta_info['from_address']['pubk'],
                          $payment_meta_info['from_address']['prik']
                      ],
                      $payment_meta_info['to_address'],
                      $amount,
                      $message
                  );

                  if($ServiceTrans == true) {


                      $TransLastInsert = $T->lastInsert();
                      /**
                       * Save Checksum
                       */
                      $T->checksum = $checksum;
                      $T->where(['id', $TransLastInsert])->update();


                      $trade_key = (new Transaction())->filter('trans_hash')->where(['id', $TransLastInsert])->get()->trans_hash;

                      (new ActivityLog())->updateLog($Pr, "[DATA E-PIN] Service Paid & Transaction Logged, \n Trade key: {$trade_key}", 'SUCCESS');

                      sleep(2);

                      // Add to Sales
                      $SalesPoint = new SalesPoint();
                      $SalesPoint->trade_key = $trade_key;
                      $SalesPoint->trade_type = $payment_meta_info['type'];
                      $SalesPoint->ifrom_address = sodium_bin2hex($pk);
                      $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                      $SalesPoint->valueorqtyexchanged = $amount;
                      $SalesPoint->extracharge = 0.00;
                      $SalesPoint->rawamt = $amount;
                      $SalesPoint->icurrency = 'DTEP';
                      $SalesPoint->status = $SalesPoint->getTradeStatus('COMPLETED');
                      $SalesPoint->proofoftrade = (new Encryption())->generateShortHash();
                      $SalesPoint->proofoftradeformat = 'CRYPTOGRAPHIC_KEY';
                      $SalesPoint->tradehistory = $message;
                      $SalesPoint->created_at = Carbon::now();
                      $SalesPoint->updated_at = Carbon::now();
                      $SalesPoint->checksum = $checksum;

                      if ($SalesPoint->create()) {

                          (new ActivityLog())->updateLog($Pr, "[DATA E-PIN] Sale Recorded {$trade_key}", 'SUCCESS');
                          $data = [
                              'msg' => "{$networkProvider} Data E-Pin Trade Successful",
                              'pins' => $PinSold,
                              'success' => true
                          ];
                      } else {
                          throw new \Exception("Transaction successful, sales not recorded");
                      }
                  }else{
                      throw new \Exception("Transaction not successful, please retry");
                  }
              } else{
                  throw new \Exception($V->getErrorString());
              }
        } catch(\Throwable $t){

            $data = ['msg' => $t->getMessage(), 'success' => false];
        }finally{
            echo Response::fromApi($data, 200);
        }
    }

    public function BuyDataBundle(Param $param){
        try{

            //@dont't delete
            $id = (new ExtraUserInfo())->getUserId();
            $email = (new ExtraUserInfo())->filter('email')->where( ['id', $id] )->get()->email;

            if( (new Transaction())->isLocked($email) ){
                throw new \Exception("You are not eligible to make transactions at the moment, because of pending transaction\nSuggest: Resolve pending transaction.");
            }


            $Wallet = new Wallet;
            $SMSProvider = new SmsProvider;
            $Transaction = new Transaction;
            $SalesPoint = new SalesPoint;


            $Validation = new Validation( ['phone', 'text|minlength:11|required'], ['product_id', 'number'], ['network_provider', 'text|required'] );

            if( $Validation->isPassed() ){

                /**
                 * Run checksum
                 */
                $checksum = null;
                if(isset($param->form()->timestamp)){
                    $timestamp = $param->form()->timestamp;

                    $checksum = (new Authtoken())->getCheckSum($timestamp);

                    if ( (new Transaction())->isExists( ['checksum', $checksum] ) ){
                        $data = [ 'msg' =>  "Transaction already completed", 'success' => true ];
                        return;
                    }
                }

                $Pr = (new ActivityLog())->Log("[DATA BUNDLE] Validation Passed", 'SUCCESS');
                if(is_null($Pr))
                    throw new \Exception("An error occurred, process stream broken, please retry");


                $providers = ['9Mobile Gifting', '9Mobile', '9MOBILE GIFTING', '9mobile gifting'];
                foreach ((new Settings())->getNetworkProviders() as $provider){
                    array_push($providers, trim($provider->value));
                }


                if( !in_array(trim($param->form()->network_provider),  $providers) )
                    throw new \Exception("Network Provider not recognized from this domain");

                if(!isset( $param->form()->product_id ) )
                    throw new \Exception("Application Error: Unknown product");




                $pid = $param->form()->product_id;
                $PRO = (new Product());
                $PR = $PRO->filter('pcost', 'pname', 'pdiscount')->where( ['id', $pid] )->get();
                $amount = $PR->pcost - $PR->pdiscount;

                if( is_null($PR->pname) )
                    throw new \Exception("Application Error: Unknown product");

                $rewardRate = (new User())->getUserTransactionReward()['DISCOUNT_RATE']['DATA_BUNDLE'];
                $discountedAmount = ( (100 - $rewardRate) / 100 ) * $amount;

                $service_charge =  ( (new Settings())->getDataBundleServiceChargeRate() / 100 ) * $discountedAmount;

                $message = '';

                /**
                 * Inline Validation
                 */

                $amt_to_check = $discountedAmount + $service_charge;

                if( !$Wallet->isSufficientBalance( floatval($amt_to_check) ) )
                    throw new \Exception("Insufficient fund in wallet");


                // Initiate data bundle Trade Transaction
                $NaijaSubPhoneResponderForData = (new Settings())->getPhoneResponderIdForDataTopUp($param->form()->network_provider);

                sleep(5);
                $time = time();

                $netProTag = trim($param->form('network_provider'));
                if($netProTag == 'MTN')
                    $netProTag = 'NTN';

                $sms = "{$netProTag}|{$PR->pname}|{$param->form()->phone}";
                $result = $SMSProvider->sendSMSViaBulk($NaijaSubPhoneResponderForData,  $sms );



                if( $result->status == "success" ){

                    $message .= "
                    Network Provider: {$param->form()->network_provider}<br><br>
                    Data: {$PR->pname}<br><br>
                    Phone: {$param->form()->phone}<br><br>
                    Amount: NGN {$PR->pcost}<br><br>
                    Discount: NGN {$PR->pdiscount}<br><br>
                    Service Charge: NGN {$service_charge}<br><br>
                    ";

                    /**
                     * Log Activity
                     */
                    (new ActivityLog())->updateLog($Pr,"[DATA BUNDLE] Service Rendered \n {$message}", 'SUCCESS');

                    $pk = $Wallet->getPublickey();
                    $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', sodium_bin2hex($pk) ] )->get();

                    $payment_meta_info = [
                        'type' => $Transaction->getTransactionTypes('DATA_BUNDLE_TRADE'),
                        'to_address' => $Wallet->getAnyAdminPublickey(),
                        'from_address'=>
                            [
                                'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                'prik'=> sodium_hex2bin($from_prime_pk->private_key)
                            ]
                    ];

                    sleep(1);

                    $ServiceTrans = $Transaction->addServiceTrans(
                        $payment_meta_info['type'],
                        'CONFIRMED',
                        [
                            $payment_meta_info['from_address']['pubk'],
                            $payment_meta_info['from_address']['prik']
                        ],
                        $payment_meta_info['to_address'],
                        $amt_to_check,
                        $message
                    );

                    if($ServiceTrans != true){
                        (new ActivityLog())->updateLog($Pr,"[DATA BUNDLE] Service Paid & Transaction was not successful,", 'FAIL');

                    }

                    $TransLastInsert = $Transaction->lastInsert();
                    /**
                     * Save Checksum
                     */
                    $Transaction->checksum = $checksum;
                    $Transaction->where(['id', $TransLastInsert])->update();


                    $trade_key = (new Transaction())->filter('trans_hash')->where( ['id', $TransLastInsert] )->get()->trans_hash;

                    (new ActivityLog())->updateLog($Pr,"[DATA BUNDLE] Service Paid & Transaction Logged, \n Trade key: {$trade_key}", 'SUCCESS');

                    sleep(2);

                    // Add to Sales

                    $SalesPoint->trade_key = $trade_key;
                    $SalesPoint->trade_type = $payment_meta_info['type'];
                    $SalesPoint->ifrom_address = sodium_bin2hex($pk);
                    $SalesPoint->ito_address = sodium_bin2hex($payment_meta_info['to_address']);
                    $SalesPoint->valueorqtyexchanged = $amt_to_check;
                    $SalesPoint->extracharge = 0.00;
                    $SalesPoint->rawamt = $amt_to_check;
                    $SalesPoint->icurrency = 'DT';
                    $SalesPoint->status = $SalesPoint->getTradeStatus('COMPLETED');
                    $SalesPoint->proofoftrade = (new Encryption())->generateShortHash();
                    $SalesPoint->proofoftradeformat = 'CRYPTOGRAPHIC_KEY';
                    $SalesPoint->tradehistory = $message;
                    $SalesPoint->created_at = Carbon::now();
                    $SalesPoint->updated_at = Carbon::now();
                    $SalesPoint->checksum = $checksum;

                    if( $SalesPoint->create() ){

                        (new ActivityLog())->updateLog($Pr,"[DATA BUNDLE] Sale Recorded {$trade_key}", 'SUCCESS');


                        // Pay My Referer
                        $id = (new ExtraUserInfo())->getUserId();
                        $email00 = (new ExtraUserInfo())->filter('email')->where( ['id', $id] )->get()->email;

                        $UsrRef = User::filter('referer')->where( ['email', $email00 ] )->get();

                        if( ExtraUserInfo::filter('id','email')->where( ['username', $UsrRef->referer] )->count() == 1 ){

                            $ReferralAccount = ExtraUserInfo::filter('id','email')->where( ['username', $UsrRef->referer] )->get();
                            $ReferralUsrAccount = User::filter('membership_plan_id')->where( ['email', $ReferralAccount->email ] )->get();


                            $membership_plan_id = $ReferralUsrAccount->membership_plan_id;


                            $my_referer_entitlement = 0.00;
                            if( !is_null($membership_plan_id) && $membership_plan_id > 0 ){

                                $entitlement_rate = (new Membership_plan())->getMemberShipRewardRates($membership_plan_id)['REFERRAL_BONUS_ON_DATA_BUNDLES'];

                                $my_referer_entitlement = ($entitlement_rate/100)*$amt_to_check;

                                if( $my_referer_entitlement > 0 ){

                                    $my_referer_pk = $Wallet->filter('public_key')->where( ['owned_by', $ReferralAccount->id] )->get()->public_key;

                                    $pk = $Wallet->getAnyAdminPublickey();
                                    $from_prime_pk = $Wallet->filter('public_key', 'private_key')->where( ['public_key', sodium_bin2hex($pk) ] )->get();

                                    $payment_meta_info = [
                                        'type' => (new Transaction())->getTransactionTypes('REFERRAL_BONUS'),
                                        'to_address' => sodium_hex2bin($my_referer_pk),
                                        'from_address'=>
                                            [
                                                'pubk'=> sodium_hex2bin($from_prime_pk->public_key),
                                                'prik'=> sodium_hex2bin($from_prime_pk->private_key)
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
                                        $my_referer_entitlement
                                    );

                                    (new ActivityLog())->updateLog($Pr,"[DATA BUNDLE] Referral Paid", 'SUCCESS');

                                }
                            }
                        }

                        dispatchPoint:

                        (new MailService())->sendOrderReceipt($message, strtoupper("{$payment_meta_info['type']}(#".sodium_bin2hex($trade_key).")") );

                        $data = [ 'msg' => 'Data bundle trade completed' , 'success' => true ];
                    }else{
                        throw new \Exception("Couldn't complete data bundle trade, please try again later");
                    }


                }
                elseif ($result->status !== "success"){
                    throw new \Exception("REQUEST NOT PROCESSED: Unable to complete data subscription, try again later");
                }else{
                    throw new \Exception("Unable to complete data subscription, try again later");
                }


            }else{
                throw new \Exception($Validation->getErrorString());
            }

        }catch(\Throwable $t){

            $data = [ 'msg' => $t->getMessage(), 'success' => false ];

            if(!is_null($Pr))
                (new ActivityLog())->updateLog($Pr,"[DATA BUNDLE] {$data['msg']}", 'FAIL');

        }finally{

            echo Response::fromApi($data, 200);

            unset($Transaction, $Wallet, $SalesPoint, $SMSProvider);
        }
    }

    /**
     * Enum
     * @param string $affiliateType
     * @return string
     */
    private function AffiliateTypes(string $affiliateType) : string {
        try {
            $types = [
                'DATA_CARD' => 'DATA_CARD'
            ];

            if (isset($types[$affiliateType])) {
                return $types[$affiliateType];
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    /**
     * Application for data card reseller at your area
     * @param Param $param
     */
    public function ApplyAsDataCardReseller(Param $param){

        try{

            $Validation = new Validation(
                ['business_name', 'required'],
                ['full_name', 'required'],
                ['email', 'email|required'],
                ['phone', 'required|minlength:11|maxlength:11'],
                ['home_address', 'required'],
                ['office_address', 'required']
            );

            if($Validation->isPassed()){

                $Affiliate = new Affiliate();

                $Affiliate->user_id = (new ExtraUserInfo())->getUserId();
                $Affiliate->business_name = ucwords($param->form('business_name'));
                $Affiliate->business_reg_no = !is_null($param->form('business_reg_no')) ? $param->form('business_reg_no') : null;
                $Affiliate->full_name = ucwords($param->form('full_name'));
                $Affiliate->email = $param->form('email');
                $Affiliate->phone = $param->form('phone');
                $Affiliate->home_addr = $param->form('home_address');
                $Affiliate->office_addr = $param->form('office_address');
                $Affiliate->type = $this->AffiliateTypes('DATA_CARD');

                if($Affiliate->create())
                    $data = ['msg' => 'Your application has been received. We will contact you within 24hrs. Thank you for chosen NaijaSub!', 'success' => true];
                else
                    throw new \Exception("An error occurred while applying, please retry");

            }else{
                throw new \Exception($Validation->getErrorString());
            }


        }catch(\Throwable $t){
            $data = [ 'msg' => $t->getMessage(), 'success' => false ];
        }finally{
            echo Response::fromApi($data, 200);
        }
    }

    public function __construct(){

        header('Access-Control-Allow-Origin: *');
//            header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

    }

    public function onInit(Param $param){
        new Date($param);
    }

    public function onAuth(Param $param){
        new SecureApi($param);
        new TransactionDailyCounterAndRestrictionChecker($param);
    }

    public function onDispose(Param $param){}

}
