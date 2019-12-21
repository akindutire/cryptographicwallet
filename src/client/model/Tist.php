<?php
namespace src\client\model;
use \zil\factory\Model;

class Tist{

	use Model;

    public $id = null;
    public $process_session_key = null;
    public $s = null;

    public static $table = 'Tist';
}
