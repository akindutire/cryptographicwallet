<?php
        namespace src\client\controller;

        use src\client\middleware\Date;
        use src\client\middleware\SecureApi;
        use src\client\model\Notification;
        use \zil\core\server\Param;
        use \zil\core\server\Response;
        use \zil\factory\View;
        use \zil\core\facades\helpers\Notifier;
        use \zil\core\facades\helpers\Navigator;
        use \zil\core\facades\decorators\Hooks;

        use src\client\Config;

        /**
         *  @Controller:NotificationApiController []
        */

        class NotificationApiController{

            use Notifier, Navigator, Hooks;


            public function GetAllNotification(Param $param) {
                try{
                    $data = [ 'msg' => (new Notification())->all()->get('VERBOSE'), 'success' => true ];
                } catch (\Throwable $t){
                    $data = [ 'msg' => $t->getMessage(), 'success' => false ];
                } finally {
                    echo Response::fromApi($data, 200);
                }

            }

            public function GetUnReadNotificationCount(Param $param) {

                try{
                    $data = [ 'msg' => (new Notification())->numberOfUnreadNotif(), 'success' => true ];
                } catch (\Throwable $t){
                    $data = [ 'msg' => $t->getMessage(), 'success' => false ];
                } finally {
                    echo Response::fromApi($data, 200);
                }
            }

            /** Read Notification  */
            public function ReadNotification(Param $param) {

                try{
                    if(!is_null($param->url()->notification_hash)){
                        $data = [ 'msg' => (new Notification())->getNotif($param->url()->notification_hash), 'success' => true ];
                    } else {
                        throw  new \Exception('Couldn\'t open notification');
                    }
                } catch (\Throwable $t){
                    $data = [ 'msg' => $t->getMessage(), 'success' => false ];
                }finally {
                    echo Response::fromApi($data, 200);
                }
            }

            public function __construct(){
                header('Access-Control-Allow-Origin: *');
                header('Content-Type: application/json');
                header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
                header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
            }

            public function onInit(Param $param) {
                new Date($param);
            }

            public function onAuth(Param $param)
            {
                new SecureApi($param);

            }
        }
