<?php
namespace src\client\model;
use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;

class DataCardCustomer{

    use Model;

    public $id = null;
    public $customer_id = null;
    public $created_at = null;

    public static $table = 'DataCardCustomer';

    public function __construct(){}
    
    public function isCustomerRecognized() : bool {
        try{

            $id = (new ExtraUserInfo())->getUserId();

            if ( $this->where( ['customer_id', $id] )->count() == 1 )
                return true;

            return false;

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }
}
    
