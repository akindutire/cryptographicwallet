<?php
        namespace src\dark\controller;

        use \zil\core\server\Param;
        use \zil\core\server\Response;
        use \zil\factory\View;
        use \zil\core\facades\helpers\Notifier;
        use \zil\core\facades\helpers\Navigator;
        use \zil\core\facades\decorators\Hooks;

        use src\dark\Config;

        /**
         *  @Controller:Home []
        */

        class Home{

            use Notifier, Navigator, Hooks;


			public function Index(Param $param){

				$OutputData = [];

				#render the desired interface inside the view folder

				View::render("Home/Index.php", $OutputData);
			}

            public function __construct(){}
            public function onInit(Param $param){}
            public function onAuth(Param $param){}
            public function onDispose(Param $param){}

        }
         