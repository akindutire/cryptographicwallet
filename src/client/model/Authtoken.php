<?php
namespace src\client\model;
use Carbon\Carbon;
use zil\core\tracer\ErrorTracer;
use zil\factory\Logger;
use \zil\factory\Model;

class Authtoken {

	use Model;

	public $token = null;
	public $claim = null;
	public $expires_at = null;

	public static $table = 'Authtoken';


    public function __construct()
    {
        self::$key = 'token';
    }

    public function getCheckSum($timestamp) : string {
        try{

            $id = (new ExtraUserInfo())->getUserId();

            return sha1("{$timestamp}{$id}");

        } catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function isValid(string $token) : bool {
        try {

            if ($this->isExists(['token', $token])) {
                $deaddate = self::find($token)->filter('expires_at AS exp')->get()->exp;

                $deaddate = Carbon::parse($deaddate);
                $now  = Carbon::now();

                if ( $deaddate->gte($now) ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function getPk(string $token) : ?string {
        try{

            if( $this->isExists(['token', $token]) ){

                return $this->where( ['token', $token] )->filter('claim AS pk')->get()->pk;

            }else{
                return null;
            }
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function getExpirationTimeStamp() : string {
        try{
            return (new Carbon())->addDays(30);
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function destroyToken(string $token) : bool {
        try {

            if( $this->iwhere('token', $token)->delete() == 1)
                return true;
            else
                return false;

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function gabageCollector() : void {
        try{

            $Tokens = $this->all()->get('VERBOSE');

            foreach ($Tokens as $TokenRow){
                if( !$this->isValid($TokenRow->token) ){
                    $this->destroyToken($TokenRow->token);
                }
            }

        } catch (\Throwable $t){
            new ErrorTracer($t);
        } finally {

            $this->token = null;

        }
    }

}
?>
