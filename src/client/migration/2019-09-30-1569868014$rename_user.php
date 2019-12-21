<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_user->User []
        */
        class rename_user implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('user');

                $schema->renameSchema('User');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
