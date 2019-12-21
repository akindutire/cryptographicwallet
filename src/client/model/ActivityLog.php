<?php
namespace src\client\model;
    use Carbon\Carbon;
    use zil\core\tracer\ErrorTracer;
    use zil\factory\Logger;
    use \zil\factory\Model;
use zil\security\Encryption;

class ActivityLog {
    use Model;

	public $id = null;
	public $process_session_key = null;
	public $UserEmail = null;
	public $UserWalletKey = null;
	public $UserId = null;
	public $history = null;
	public $created_at = null;
	public $status = null;

	public static $table = 'ActivityLog';


		public function Log(string $activity, string $status) : string {
            try{

                $UserId = (new ExtraUserInfo())->getUserId();
                $UserEmail = ExtraUserInfo::filter('email')->where(['id', $UserId])->get()->email;
                $UserWalletKey = Wallet::filter('public_key')->where(['owned_by', $UserId])->get()->public_key;
                $proc_key = (new Encryption())->generateShortHash().'$'.md5($UserEmail);

                $this->process_session_key = $proc_key;
                $this->UserEmail = $UserEmail;
                $this->UserWalletKey  = $UserWalletKey;
                $this->UserId = $UserId;
                $this->history = $activity;
                $this->created_at = Carbon::now();
                $this->status = $status;
                $this->create();

                return $proc_key;

            } catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function updateLog(string $process_session_key, string $new_activity, string $status) : bool {
            try{
                if($this->filter('id')->where(['process_session_key', $process_session_key])->count() == 1){
                    $prev_act = $this->filter('history')->where(['process_session_key', $process_session_key])->get()->history;
                    $time = Carbon::now();
                    $prev_act .= "\n***[MERGE]@{$time}\t{$new_activity}\n[STATUS]@{$status}\n-------------------\n";
                    $this->history = $prev_act;
                    $this->status = $status;

                    if( $this->where(['process_session_key', $process_session_key])->update() == 1 )
                        return true;
                    else
                        return false;

                }else{
                    return false;
                }
            } catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }
    }
?>
