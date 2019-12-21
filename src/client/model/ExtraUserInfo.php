<?php

namespace src\client\model;


use zil\core\scrapper\Info;
use zil\factory\Logger;
use \zil\factory\Model;
use zil\factory\Redirect;
use \zil\factory\Session;
use \zil\core\tracer\ErrorTracer;

class ExtraUserInfo {

    use Model;

    public $id = null;
    public $name = null;
    public $username = null;
    public $password = null;
    public $email = null;
    public $phone = null;

    public static $table = 'ExtraUserInfo';

    public function __construct()
    {
        self::$key = 'id';
    }

    public function createReferalId(string $username){
        return $username;
    }

    public function getReferalLink(string $username){
        return $_SERVER['HTTP_HOST']."/register/".$username;
    }

    public function getUserId(){
        try{

            if(isset(Info::$_dataLounge['API_CLIENT_ACTIVE']) || isset( Info::$_dataLounge['API_CLIENT']['PK'] ) ){
                $email = Info::$_dataLounge['API_CLIENT']['PK'];
            }elseif( Session::get('email') !== null ){
                $email = Session::get('email');
            }else{
                if(Info::getRouteType() == 'api')
                    throw new \Exception("Unique resource not found, email not active");
                else
                    new Redirect('login');
            }

            return (new self())->filter('id')->where( ['email', $email ] )->get()->id;


        }catch(\Throwable $t){
            new ErrorTracer($t);
        }

    }

    public function getSpecificUserId(string $email){
        return (new self())->filter('id')->where( ['email', $email ] )->get()->id;
    }



    public function isUsernameValid(string $username){

        if ( self::filter('id')->where( ['username', $username ] )->count() == 1)
            return true;
        else
            return false;
    }

}
?>
