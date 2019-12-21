<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_accverificationkeylock->AccVerificationKeyLock []
        */
        class rename_accverificationkeylock implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('AccVerificationKeyLock');

                $schema->renameSchema('EmailValidationTokenLock');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
