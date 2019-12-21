<?php

namespace src\client\model;

use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;
use src\client\service\CurrencyConverter;

class Settings{

	use Model;

	public $id = null;
	public $type = null;
	public $skey = null;
	public $value = null;
	public $created_at = null;


	public static $table = 'Settings';

    public function getBitcoinWithdrawalHugeCriteria() : float {
        try{
            return (float)self::filter('value')->where(['type', 'RULE'], ['skey', "BITCOIN_HUGE_WITHDRAWAL_CRITERIA"])->get()->value;
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

	public function getPhoneResponderNoForDataEPin(string $key) : string {
	    try{
	        $key = strtoupper($key);
	        return (string)self::filter('value')->where(['type', 'RULE'], ['skey', "PHONE_RESPONDER_NUMBER_FOR_DATA_E_PIN_{$key}"])->get()->value;
        } catch (\Throwable $t){
	        new ErrorTracer($t);
        }
    }
	public function getMinUnitForPinCustomization() : int {
	    try{
	        return (int)self::filter('value')->where(['type', 'RULE'], ['skey', 'MIN_UNIT_FOR_E_PIN_CUSTOMIZATION'])->get()->value;
        } catch (\Throwable $t) {

        }
    }

    public function getDataEPinPriceTag(string $network_provider) : float {
        try {
            if (!in_array($network_provider, ['MTN', 'GLO', '9MOBILE', 'AIRTEL']))
                throw new \Exception("Unsupported network provider");

            $key = strtoupper($network_provider) . "_DATA_E_PIN_PRICE_TAG";
            return (float)self::filter('value')->where(['type', 'RULE'], ['skey', $key])->get()->value;
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

	public function getMinimumBalanceRequirementDataCardReseller() {
        return self::filter('value')->where( ['type', 'RULE'], ['skey', 'MINIMUM_BALANCE_REQUIREMENT_DATA_CARD_RESELLER'] )->get()->value;
    }

	public function getDailyTransactionLimitForRestrictedAccount()  {
        return self::filter('value')->where( ['type', 'RULE'], ['skey', 'DAILY_TRANSACTION_LIMIT_FOR_RESTRICTED_ACCOUNT'] )->get()->value;
    }

    public function getReferralBonusValOnTransaction(){
        return self::filter('value')->where( ['type', 'REWARD'], ['skey', 'REFERRAL_BONUS'] )->get()->value;

    }

    public function getDataBundleServiceChargeRate():float{
        return floatval(self::filter('value')->where( ['type', 'SERVICE_CHARGE'], ['skey', 'DATA_BUNDLE_RATE'] )->get()->value);

    }

    /**
    Settings on topup for card
     */
    public function getTopViaCardChargeRate():float{
        return floatval(self::filter('value')->where( ['type', 'SERVICE_CHARGE'], ['skey', 'TOPUP_VIA_CARD_RATE'] )->get()->value);

    }

    public function getTopUpViaSNS_AirtimeChargeRate(?string $provider=null):float{

        return floatval(self::filter('value')->where( ['type', 'SERVICE_CHARGE'], ['skey', 'TOPUP_VIA_AIRTIME_RATE'] )->get()->value);

        // return $this->getAirtimeSaleServiceChargeRate($provider);
    }

    public function getAirtimeSaleServiceChargeRate(string $provider) : float{
        $key = $provider.'_AIRTIME_SELLING_CHARGE_RATE';
        return floatval(self::filter('value')->where( ['type', 'SERVICE_CHARGE'], ['skey', $key] )->get()->value);
    }

    public function getNetworkProviders(): array{
        return $this->filter('value')->where( ['type', 'NETWORK_PROVIDER'] )->get('VERBOSE');
    }

    public function getMinTopUpThroughBank() : float {
        return floatval($this->filter('value')->where( ['type', 'RULE'], ['skey', 'MINIMUM_TOPUP_USING_BANK'] )->get()->value);
    }

    public function getMinTopUp(): float {
        return floatval($this->filter('value')->where( ['type', 'RULE'], ['skey', 'MINIMUM_TOPUP'] )->get()->value);
    }

    public function getPhoneResponderIdForDataTopUp(string $network_provider) : string {

        $np = strtoupper($network_provider);

        return $this->filter('value')->where( ['type', 'RULE'], ['skey', "{$np}_DATA_REQUEST_PHONE_SMS_REDIRECT"] )->get()->value;
    }

    public function getAirtimePurchaseDiscountRate(string $provider): float{
        $key = strtoupper($provider).'_AIRTIME_PURCHASE_DISCOUNT_RATE';

        return floatval($this->filter('value')->where( ['type', 'REWARD'], ['skey', $key] )->get()->value);
    }


    public function getExchangeRates():array{
        return self::filter('skey','value')->where( ['type', 'EXCHANGE_RATE'] )->get('VERBOSE');
    }

    public function getRules() : array{
        return self::filter('skey','value')->where( ['type', 'RULE'] )->get('VERBOSE');
    }

    public function getGiftCardPhone() : string {
        return self::filter('value')->where( ['type', 'RULE'], ['skey', 'GIFTCARD_PHONE_NO'] )->get()->value;
    }

    public function getCompanyBankDetails() : string {
        return self::filter('value')->where( ['type', 'RULE'], ['skey', 'COMPANY_BANK_DETAILS_ACCOUNT'] )->get()->value;
    }

    public function getAirtimeSalePhone() : string {
        return self::filter('value')->where( ['type', 'RULE'], ['skey', 'AIRTIME_SALE_PHONE_NO'] )->get()->value;
    }

    public function getRewards() : array{
        return self::filter('skey','value')->where( ['type', 'REWARD'] )->get('VERBOSE');
    }

    public function getServiceCharges() : array {
        return self::filter('skey','value')->where( ['type', 'SERVICE_CHARGE'] )->get('VERBOSE');
    }

    public function getAirtimeMaxSale():float{
        return floatval(self::filter('value')->where( ['type', 'RULE'], ['skey', 'MAXIMUM_SELLING_OF_AIRTIME'] )->get()->value);
    }

    public function getElectricityBillMinSale():float{
        return floatval(self::filter('value')->where( ['type', 'RULE'], ['skey', 'MINIMUM_PAYMENT_OF_ELECTRICITY_BILL'] )->get()->value);
    }

    public function getElectricityBillMaxSale():float{
        return floatval(self::filter('value')->where( ['type', 'RULE'], ['skey', 'MAXIMUM_PAYMENT_OF_ELECTRICITY_BILL'] )->get()->value);
    }

    public function getAirtimeMinSale():float{
        return floatval(self::filter('value')->where( ['type', 'RULE'], ['skey', 'MINIMUM_SELLING_OF_AIRTIME'] )->get()->value);
    }

    /**
    Settings for Bitcoin
     */

    public function getBTCToUSDRate():float{

        // return floatval(self::filter('value')->where( ['type', 'EXCHANGE_RATE'], ['skey', 'BITCOIN_TO_DOLLAR'] )->get()->value);

        return (new CurrencyConverter())->getConversionRate();

    }

    public function getBitcoinBuyingRate():float{

        return floatval( $this->getBitcoinBuyingRateInNGN() * $this->getBTCToUSDRate() );

    }

    public function getBitcoinSellingRate():float{

        return floatval( $this->getBitcoinSellingRateInNGN() * $this->getBTCToUSDRate() );

    }


    public function getBitcoinBuyingRateInNGN():float{
        return floatval(self::filter('value')->where( ['type', 'EXCHANGE_RATE'], ['skey', 'BITCOIN_BUYING_RATE_IN_NGN'] )->get()->value);

    }

    public function getBitcoinSellingRateInNGN():float{
        return floatval(self::filter('value')->where( ['type', 'EXCHANGE_RATE'], ['skey', 'BITCOIN_SELLING_RATE_IN_NGN'] )->get()->value);

    }

    /**
    Settings for Bills
     */
    public function updateBillApiToken(string $token){
        $this->value = $token;

        $this->where( ['type', 'TOKEN'], ['skey', 'BILL_API_TOKEN'] )->update();
    }

    public function getBillApiToken(): string{
        return $this->filter('value')->where( ['type', 'TOKEN'], ['skey', 'BILL_API_TOKEN'] )->get()->value;
    }

    public function getCableTvBillChargeRate() : float {

        return floatval($this->filter('value')->where( ['type', 'SERVICE_CHARGE'], ['skey', 'TV_BILL_CHARGE_RATE'] )->get()->value);
    }

    public function getMiscBillChargeRate() : float {

        return floatval($this->filter('value')->where( ['type', 'SERVICE_CHARGE'], ['skey', 'MISC_BILL_CHARGE_RATE'] )->get()->value);
    }

    public function getInternetBillChargeRate() : float {

        return floatval($this->filter('value')->where( ['type', 'SERVICE_CHARGE'], ['skey', 'INTERNET_BILL_CHARGE_RATE'] )->get()->value);
    }

    public function getElectricityBillChargeRate() : float {

        return floatval($this->filter('value')->where( ['type', 'SERVICE_CHARGE'], ['skey', 'ELECTRICITY_BILL_CHARGE_RATE'] )->get()->value);
    }

    /**
    Settings for Cashout
     */
    public function getCashOutServiceCharge():float{
        return self::filter('value')->where( ['type', 'SERVICE_CHARGE'] , ['skey', 'CASHOUT_CHARGE'])->get()->value ;

    }


}
?>
