<?php
        namespace src\client\controller;

        use \zil\core\server\Param;
        use \zil\core\server\Response;
        use zil\factory\Redirect;
        use \zil\factory\View;
        use \zil\core\facades\helpers\Notifier;
        use \zil\core\facades\helpers\Navigator;
        use \zil\core\facades\decorators\Hooks;

        use src\client\Config;

        /**
         *  @Controller:Migration []
        */

        class Migration{

            use Notifier, Navigator, Hooks;


            public function Scaffold(Param $param){
                $t = $param->url('t');
                $m = $param->url('m');

                print( shell_exec("php zil scaffold $m -t {$t}") );
            }

			public function Run(Param $param){

			    $migration_name = $param->url()->migration_name;

                if ($param->url()->rollback == '0')
                    $r = '';

                if ($param->url()->rollback == '1')
			        $r = '--rollback';

			    if($param->url()->migration_name == '0'){

                    print( shell_exec("php zil migrate --all") );

                }else{
                    print( shell_exec("php zil migrate {$migration_name}") );
                }

//			    var_dump($migration_name, $r);


                print( shell_exec("composer dump-autoload -o") );

            }



            public function __construct(){}
            public function onInit(Param $param){}
            public function onAuth(Param $param){}

            public function onDispose(Param $param){}

        }
