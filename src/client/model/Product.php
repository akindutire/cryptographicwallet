<?php
namespace src\client\model;
use \zil\factory\Model;

class Product{

	use Model;

	public $id = null;
	public $ptype = null;
	public $pcat = null;
	public $pname = null;
	public $pcost = null;
	public $is_user_define_price_unit = null;
	public $pcurrency = null;
	public $pdesc = null;
	public $pdiscount = null;
	public $is_disable = null;
	public $created_at = null;

	public static $table = 'Product';

    public function getProductsBasedTypes(int $type){
        return $this->all()->as('p')->with('Product_cat as pc', 'p.pcat = pc.id')->where( [ 'ptype', $type ] )->get('VERBOSE');
    }

    public function getProductsBasedCats(int $cat){
        return $this->all()->where( [ 'pcat', $cat ] )->get('VERBOSE');
    }

	public function disable(int $product_id) : bool {
		$this->is_disable = 1;

		if( $this->where( ['id', $product_id] )->update() == 1)
			return true;

		return false;
	}

	public function enable(int $product_id) : bool {
		$this->is_disable = 0;

		if( $this->where( ['id', $product_id] )->update() == 1)
			return true;

		return false;
	}

}
?>
