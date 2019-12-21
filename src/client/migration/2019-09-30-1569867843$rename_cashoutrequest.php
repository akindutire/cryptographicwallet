<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_cashoutrequest->Cashoutrequest []
        */
        class rename_cashoutrequest implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('cashoutrequest');

                $schema->renameSchema('CashoutRequest');
            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
