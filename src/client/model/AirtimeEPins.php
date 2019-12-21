<?php
namespace src\client\model;
use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;

class AirtimeEPins{

    use Model;

	public $id = null;
	public $batch_tag = null;
	public $network_provider = null;
	public $product = null;
	public $pin_code = null;
	public $serial_no = null;
	public $status_code = null;
	public $date = null;
	public $created_at = null;

	public static $table = 'AirtimeEPins';


    public function __construct(){}

    public function allPins(bool $desc = null) : array {
        try{
            if($desc)
                return $this->all()->key('id')->desc()->get('VERBOSE');
            return $this->all()->get('VERBOSE');
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function buy(int $units, string $network_provider, string $product, $unitPrice) : array {
        try{
            $unit_sold = 0;
            $PinSold = [];
            $PinString = '';


            $rs = $this->filter('pin_code', 'status_code', 'serial_no')->where(  ['network_provider', $network_provider], ['product', $product], ['status_code', 0]  )->take($units)->get('VERBOSE');
            foreach( $rs as $card ){

                if( $this->filter('id')->where( ['pin_code', $card->pin_code], ['status_code', 0] )->count() == 1){

                    $this->status_code = 1;
                    $this->where(['pin_code', $card->pin_code])->update();

                    $unit_sold += 1;
                    $PinSold[$card->pin_code] = [ 'serial' => $card->serial_no, 'pin' => $card->pin_code, 'network' => $network_provider, 'product' => $product, 'price' => $unitPrice ];
                    $PinString .= "<b>{$unit_sold}.</b>&nbsp;{$network_provider} : {$product} <br> Pin: {$card->pin_code} <br> Serial: {$card->serial_no}<br>--------------------------<br>";

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
}
    





