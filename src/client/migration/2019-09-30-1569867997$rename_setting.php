<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_setting->Setting []
        */
        class rename_setting implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('settings');

                $schema->renameSchema('Settings');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
