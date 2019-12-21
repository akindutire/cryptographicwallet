<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:add_fields_to_data_card->DataCard []
        */
        class add_fields_to_data_card implements Migration{

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

                $schema->build('pin_code')->String()->Unique();
                $schema->build('serial_no')->String()->Unique();
                $schema->build('status_code')->String();
                $schema->build('number_credited')->String();
                $schema->build('retailer')->String();
                $schema->build('date')->String();
            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
