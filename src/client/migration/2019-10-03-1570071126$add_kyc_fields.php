<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:add_kyc_fields->User []
        */
        class add_kyc_fields implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('User');

                $schema->build('KYC_DOB')->Boolean();
                $schema->build('KYC_FULLNAME')->Boolean();

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
