<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_extrauserinfo->Extrauserinfo []
        */
        class rename_extrauserinfo implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('extrauserinfo');

                $schema->renameSchema('ExtraUserInfo');
            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
