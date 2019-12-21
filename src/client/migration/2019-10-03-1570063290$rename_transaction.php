<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_transaction->Transaction []
        */
        class rename_transaction implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('transaction');

                $schema->renameSchema('Transaction');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
