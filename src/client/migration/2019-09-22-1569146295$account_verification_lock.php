<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:account_verification_lock->AccVerificationKeyLock []
        */
        class account_verification_lock implements Migration{

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

                $schema->build('email')->String();
                $schema->build('token')->String(512)->Unique();
                $schema->build('expires_on')->String();

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
