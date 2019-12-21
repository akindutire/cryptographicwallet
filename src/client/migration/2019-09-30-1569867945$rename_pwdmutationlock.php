<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_pwdmutationlock->Pwdmutationlock []
        */
        class rename_pwdmutationlock implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('pwdmutationlock');

                $schema->renameSchema('PwdMutationLock');
            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
