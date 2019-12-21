<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_salespoint->Salespoint []
        */
        class rename_salespoint implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('salespoint');
                $schema->renameSchema('SalesPoint');


            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
