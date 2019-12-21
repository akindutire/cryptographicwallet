<?php

namespace src\client\model;

use zil\core\tracer\ErrorTracer;
use \zil\factory\Model;

class MailingList {

	use Model;

	public $email = null;
	public $isEnabled = null;


	public static $table = 'MailingList';

		public function isSubscribed(string $email): bool{

			if ( self::filter('email')->where( ['email', $email], ['isEnabled', 1] )->count() == 1)
				return true;
			else
				return false;
		}

		public function subscribe(string $email) : bool{

			if ( self::filter('email')->where( ['email', $email] )->count() == 0){
			
				$this->email = $email;
				$this->isEnabled = 1;
				$this->create();
				
				return true;
			}

			return false;
		}
		
		public function subscriptionLink(string $email) : string{

			return $_SERVER['HTTP_HOST'].'/user/subscribe/mail/'.$email;
		}
		
		public function unsubscriptionLink(string $email) : string{

			return $_SERVER['HTTP_HOST'].'/user/unsubscribe/mail/'.$email;
		}

		public function unsubscribe(string $email) : bool{

			if ( self::filter('email')->where( ['email', $email] )->count() == 1){
			
				$this->isEnabled = 0;
				$this->where( ['email', $email] )->update();
				
				return true;
			}

			return false;
		}

		public function allMails() : array {
			try{

				return $this->as('m')->with('ExtraUserInfo as ex', 'm.email = ex.email')->filter('m.email', 'ex.name')->where(['m.isEnabled', 1])->get('VERBOSE');

			} catch (\Throwable $t){
				new ErrorTracer($t);
			}
		}

		
	}
?>
