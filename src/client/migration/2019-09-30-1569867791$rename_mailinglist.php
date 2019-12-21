<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:rename_mailinglist->Mailinglist []
        */
        class rename_mailinglist implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('mailinglist');

                $schema->renameSchema('MailingList');
            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
