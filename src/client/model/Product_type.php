<?php

namespace src\client\model;

use \zil\factory\Model;

class Product_type{

	use Model;

	public $id = null;
	public $type = null;


	public static $table = 'Product_type';
    public static $key = '';

    public function getTypes(){
        return $this->all()->get('VERBOSE');
    }

    public function isValidType( int $type_id ) : bool {

        if ( $this->filter('id')->where( ['id', $type_id] )->count() == 1 )
            return true;
        else
            return false;

    }

    public function tag( int $type_id ) : string {

        return $this->filter('type')->where( ['id', $type_id] )->get()->type;

    }



}
?>
