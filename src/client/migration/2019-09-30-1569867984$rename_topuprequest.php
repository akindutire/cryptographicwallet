<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_topuprequest->Topuprequest []
        */
        class rename_topuprequest implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('topuprequest');

                $schema->renameSchema('TopupRequest');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
