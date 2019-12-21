<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:add_kyc_mobile_field->User []
        */
        class add_kyc_mobile_field implements Migration{

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

                Schema::destroy('KYC_FULLNAME');
                $schema->build('KYC_FULLNAME')->String();
                $schema->build('KYC_MOBILE')->String();

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
