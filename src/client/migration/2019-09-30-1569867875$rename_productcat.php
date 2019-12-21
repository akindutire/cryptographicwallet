<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_productcat->Product_cat []
        */
        class rename_productcat implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('product_cat');
                $schema->renameSchema('Product_cat');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
