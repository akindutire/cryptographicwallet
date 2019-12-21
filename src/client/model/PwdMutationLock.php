<?php

namespace src\client\model;

use src\client\config\Config;

use zil\factory\Model;

use Carbon\Carbon;

class PwdMutationLock {

	use Model;

	public $email = null;
	public $msg = null;
	public $created_at = null;


	public static $table = 'PwdMutationLock';

		public function PwdMutationGC(){
			
			$collections = (new self())::all()->get();

			if((new self())::all()->count() == 0){
				
				return;
				
			}else if((new self())::all()->count() == 1){
				
				if( (new Carbon())->diffInHours($collections->created_at) > 24)
					$this->where( ['email', $collections->email] )->delete();

			}else{

				foreach($collections as $pwd_request){
			
					if( (new Carbon())->diffInHours($pwd_request->created_at) > 24)
						$this->where( ['email', $pwd_request->email] )->delete();
				}
			
			}

			unset($collections);
		}
		
	}
?>
