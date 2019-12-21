<?php

namespace src\client\model;

use Carbon\Carbon;
use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;
use zil\security\Encryption;

class EmailValidationTokenLock
{

    use Model;

    public $email = null;
    public $token = null;
    public $expires_on = null;


    public static $table = 'EmailValidationTokenLock';

    public function EmailMutationGC()
    {

        $collections = (new self())::all()->get();

        if ((new self())::all()->count() == 0) {

            return;

        } else if ((new self())::all()->count() == 1) {

            if ( (new Carbon())->greaterThanOrEqualTo($collections->expires_on) )
                $this->where(['email', $collections->email])->delete();

        } else {

            foreach ($collections as $verification_request) {

                if ((new Carbon())->greaterThanOrEqualTo($verification_request->expires_on) )
                    $this->where(['email', $verification_request->email])->delete();
            }

        }

        unset($collections);
    }

    public function RequestEmailVerification(string $email) : ?string {

        try{

            $key = sha1((new Encryption())->authKey()."$".$email."$".time());
            $this->email = $email;
            $this->token = $key;
            $this->expires_on = (new Carbon())->addHours(12);;

            if( $this->create() )
                return $key;

            return null;

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }
}
?>
