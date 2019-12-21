<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:data_cards->DataCard []
        */
        class data_cards implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('DataCard');

                $schema->build('id')->Primary()->Integer()->AutoIncrement();
                $schema->build('created_at')->Timestamp();

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
