<?php


namespace src\adminhub\model;


use zil\core\tracer\ErrorTracer;

class ActivityLog extends \src\client\model\ActivityLog
{



    public function allUserActivities(string $email) : array {

        try{
            $Prs = $this->key('id')->filter('process_session_key', 'status', 'history', 'created_at as date')->where( ['UserEmail', $email] )->groupBy('process_session_key')->orderBy('created_at')->desc()->get('VERBOSE');

            if(sizeof($Prs) == 0)
                return [];

            return $Prs;

        } catch (\Throwable $t) {
          new ErrorTracer($t);
        }
    }
}
