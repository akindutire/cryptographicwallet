<?php
        namespace src\dark\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:createUserFields->Users []
        */
        class createUserFields implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('Users');

                $schema->build('id')->Primary()->Integer()->AutoIncrement();
                $schema->build('user_id')->String()->Unique();
                $schema->build('logged_in_devices')->Text();
                $schema->build('created_at')->Timestamp();

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){

                $schema = new Schema('Users');
                $schema->destroySchema();

            }
        }
