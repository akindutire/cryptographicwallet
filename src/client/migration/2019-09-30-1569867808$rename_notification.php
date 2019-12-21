<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_notification->Notification []
        */
        class rename_notification implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('notification');

                $schema->renameSchema('Notification');
            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
