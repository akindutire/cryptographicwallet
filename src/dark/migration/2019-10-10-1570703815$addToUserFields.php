<?php
        namespace src\dark\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:addToUserFields->Users []
        */
        class addToUserFields implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('Users');

                $schema->build('firstname')->After('logged_in_devices')->String();
                $schema->build('lastname')->After('firstname')->String();

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){

                $schema = new Schema('Users');
                $schema->destroy('lastname');
            }
        }
