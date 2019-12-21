<?php
namespace src\dark\model;
use zil\core\facades\helpers\Notifier;
use \zil\factory\Model;

class Users{

    use Model, Notifier;

	public $id = null;
	public $user_id = null;
	public $logged_in_devices = null;
	public $firstname = null;
	public $lastname01 = null;
	public $lastname = null;
	public $created_at = null;
	public static $table = 'Users';
    public static $key = 'id';


	public function fgPoll(){
    }
    public function gpPoll(){
        $var = "CV" . "_POLL";
        return $var.'1234';
    }
}
?>
