<?php

namespace src\client\model;

use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;

class Notification{

	use Model;

	public $id = null;
	public $notification_hash = null;
	public $sender_id = null;
	public $is_published = null;
	public $subject = null;
	public $message = null;
	public $created_at = null;
	public $updated_at = null;


	public static $table = 'Notification';

    public function __construct()
    {
        self::$key = 'id';
    }

    public function getNotif( string $notif_hash ) : object {
        try{

            $this->markAsRead($notif_hash);
            return $this->where( [ 'notification_hash', $notif_hash ] )->get();

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }


    public function getAllNotifs() : array {
        try{
            return $this->all()->where( [ 'is_published', true ]  )->desc()->get('VERBOSE');
        }catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function isNotifRead(int $notif_id) : bool {

        try{
            $User_Id = (new ExtraUserInfo())->getUserId();
            $my_email = ExtraUserInfo::filter('email')->where( ['id', $User_Id] )->get()->email;
            $read_receipt = User::filter('read_receipt')->where( ['email', $my_email] )->get()->read_receipt;

            if(is_null($read_receipt))
                $read_receipt = serialize([]);

            if(!is_null($read_receipt)){
                $read_receipt = unserialize($read_receipt);


                if( in_array($notif_id, $read_receipt) ) {
                    return true;
                }else{
                    return false;
                }
            }

            return false;
        } catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function numberOfUnreadNotif() : int {
        try{

            $User_Id = (new ExtraUserInfo())->getUserId();
            $my_email = ExtraUserInfo::filter('email')->where( ['id', $User_Id] )->get()->email;
            $read_receipt = User::filter('read_receipt')->where( ['email', $my_email] )->get()->read_receipt;

            if(!is_null($read_receipt)){
                $read_receipt = unserialize($read_receipt);

                $BunchNotifId = $this->filter('id')->get('VERBOSE');

                $BunchNotifArray = array_map( function ($Obj) {
                    return $Obj->id;
                }, $BunchNotifId);

                $read_receipt = array_pad($read_receipt, count($BunchNotifId), null);

                foreach ( $read_receipt as $k => $Notif_Id_V){

                    if( array_search($Notif_Id_V, $BunchNotifArray) === false ){
                        unset($read_receipt[$k]);
                    }
                }

                $read_receipt = array_values($read_receipt);
                $read_receipt = serialize($read_receipt);


                $U = new User();
                $U->read_receipt = $read_receipt;
                $U->where( [ 'email', $my_email ] )->update();


                return $this->all()->count() - count(unserialize($read_receipt));

            }else{
                return $this->all()->count();
            }

        }	catch (\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function markAsRead(string $notif_hash) : bool {

        try{

            $notif_id = $this->filter('id')->where( ['notification_hash', $notif_hash] )->get()->id;

            if( !$this->isNotifRead($notif_id) ){

                $User_Id = (new ExtraUserInfo())->getUserId();
                $my_email = ExtraUserInfo::filter('email')->where( ['id', $User_Id] )->get()->email;
                $read_receipt = User::filter('read_receipt')->where( ['email', $my_email] )->get()->read_receipt;

                if(is_null($read_receipt))
                    $read_receipt = serialize([]);

                if(!is_null($read_receipt)){
                    $read_receipt = unserialize($read_receipt);

                    if( $this->find($notif_id)->count() == 1 ) {
                        array_push($read_receipt, $notif_id);

                        $U = new User();
                        $U->read_receipt = serialize($read_receipt);


                        if ( $U->where( ['email', $my_email] )->update() == 1 )
                            return true;
                        else
                            return false;
                    }
                }else {
                    return false;
                }

            }else{
                return true;
            }

        } catch (\Throwable $t){
            new ErrorTracer($t);
        }

    }

}
?>
