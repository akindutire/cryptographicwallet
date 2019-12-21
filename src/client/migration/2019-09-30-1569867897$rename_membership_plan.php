<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_membership_plan->Membership_plan []
        */
        class rename_membership_plan implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('membership_plan');

                $schema->renameSchema('Membership_plan');

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
