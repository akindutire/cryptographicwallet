<?php
namespace src\client\model;
use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;

class DataCard{

    use Model;

	public $id = null;
	public $pin_code = null;
	public $status_code = null;
	public $number_credited = null;
	public $batch_tag = null;
	public $created_at = null;
	public $network_provider = null;
	public $serial = null;
	public $product = null;
	public $price = null;

	public static $table = 'DataCardEPins';


    public function __construct() {}

    public function allCards(bool $desc = null) : array {
        try{
            if($desc)
                return $this->all()->key('id')->desc()->get('VERBOSE');
            return $this->all()->get('VERBOSE');
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function buy(int $units, string $network_provider, string $product) : array {
        try{
            $unit_sold = 0;
            $PinSold = [];
            $PinString = '';


            $rs = $this->filter('pin_code', 'status_code', 'serial', 'price')->where(  ['network_provider', $network_provider], ['product', $product], ['status_code', 0]  )->take($units)->get('VERBOSE');
            foreach( $rs as $dataCard ){

                if( $this->filter('id')->where( ['pin_code', $dataCard->pin_code], ['status_code', 0] )->count() == 1){

                    $this->status_code = 1;
                    $this->where(['pin_code', $dataCard->pin_code])->update();

                    $unit_sold += 1;

                    /**
                     * @Caution: Careful, these products are conditional to change of change in future, 1GB maybe renamed and wont conform.
                     */
                    $product = strtoupper($product);

                    $key = "{$network_provider}_{$product}";
                    $to = (new Settings())->getPhoneResponderNoForDataEPin($key);

                    if(trim($product) == '1GB')
                        $instruction = "SMS *1*PIN*PHONE# to {$to}";
                    elseif (trim($product) == '2GB')
                        $instruction = "SMS *2*PIN*PHONE# to {$to}";

                    $PinSold[$dataCard->pin_code] = ['instruction' => $instruction, 'serial' => $dataCard->serial,'pin' => $dataCard->pin_code, 'network' => $network_provider, 'product' => $product, 'price' => $dataCard->price ];
                    $PinString .= "<b>{$unit_sold}.</b>&nbsp;{$network_provider} : {$product} <br> Pin: {$dataCard->pin_code} <br> Serial: {$dataCard->serial} <br> Amount: NGN {$dataCard->price}<br>--------------------------<br>";

                }

            }

            return [
              'pin_string' => $PinString,
              'pin_sold' => $PinSold,
              'unit_sold' => $unit_sold
            ];

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function allFreeCards() : array {
        try{
            // 0: means free
            // 1: means Used
            return $this->all()->where( ['status_code', 0] )->get('VERBOSE');
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }
    public function allUsedCards() : array {
        try{
            // 0: means free
            // 1: means Used
            return $this->where( ['status_code', 1] )->get('VERBOSE');
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }
}





