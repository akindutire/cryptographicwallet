<?php
        namespace src\dark\route;

        use \zil\core\interfaces\Route;
        use \zil\core\server\Resource;

        /**
         *   @Route:Web
        */

        class Web implements Route{

            use \zil\core\facades\decorators\Route_D1;

            /**
             * Web routes
            *
            * @return array
            */
            public function route(): array{


                return [];
            }

        }

         