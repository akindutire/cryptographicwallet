<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_product_type->Product_type []
        */
        class rename_product_type implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('product_type');

                $schema->renameSchema('Product_type');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
