<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_product->Product []
        */
        class rename_product implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('product');

                $schema->renameSchema('Product');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
