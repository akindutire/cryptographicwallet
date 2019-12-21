<?php
namespace src\client\model;
use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;

class Product_cat{

	use Model;

	public $id = null;
	public $type_id = null;
	public $cat = null;
	public $is_disable = null;

	public static $table = 'Product_cat';

    public function getCategories(int $type) : array {
		try {
			return $this->all()->where(['type_id', $type])->get('VERBOSE');
		} catch (\Throwable $t) {
			new ErrorTracer($t);
		}
    }

    public function disableCat(int $cat_id) : bool {
		try {
			$this->is_disable = 1;

			if ($this->where(['id', $cat_id])->update() == 1)
				return true;

			return false;

		} catch (\Throwable $t) {
			new ErrorTracer($t);
		}
	}

	public function enableCat(int $cat_id) : bool {
		try {
			$this->is_disable = 0;

			if ($this->where(['id', $cat_id])->update() == 1)
				return true;

			return false;
		} catch (\Throwable $t){
			new ErrorTracer($t);
		}
	}

}
?>
