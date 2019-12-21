<?php
namespace src\client\model;
use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;

class Affiliate{

	use Model;

	public $id = null;
	public $user_id = null;
	public $business_name = null;
	public $business_reg_no = null;
	public $full_name = null;
	public $email = null;
	public $phone = null;
	public $home_addr = null;
	public $office_addr = null;
	public $type = null;
	public $created_at = null;

	public static $table = 'Affiliate';


	public function suspend(){

    }

    public function deleteAff(string $id) : bool
    {
        try{
            if( $this->where( ['id', $id] )->delete() == 1 )
                return true;

            return false;

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function viewAffiliateStatus(){

    }

    public function viewAllAssociations() {

    }

    public function isAnAffiliate() : bool {
	    try{

            $id = (new ExtraUserInfo())->getUserId();

            if($this->where( ['user_id', $id] )->count() > 0)
                return true;

            return false;

        } catch (\Throwable $t){
	        new ErrorTracer($t);
        }
    }
}
?>
