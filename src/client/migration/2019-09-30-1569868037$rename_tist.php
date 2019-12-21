<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_tist->Tist []
        */
        class rename_tist implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('tist');

                $schema->renameSchema('Tist');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
