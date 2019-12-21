<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_authtoken->Authtoken []
        */
        class rename_authtoken implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('authtoken');

                $schema->renameSchema('Authtoken');
            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
