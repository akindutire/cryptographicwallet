<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_wallet->Wallet []
        */
        class rename_wallet implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('wallet');

                $schema->renameSchema('Wallet');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
