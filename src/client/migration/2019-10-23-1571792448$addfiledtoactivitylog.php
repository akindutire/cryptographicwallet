<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:addfiledtoactivitylog->ActivityLog []
        */
        class addfiledtoactivitylog implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('ActivityLog');

                $schema->build('process_session_key')->String()->After('id');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
