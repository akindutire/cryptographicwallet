<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:affiliate->Affiliate []
        */
        class affiliate implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('Affiliate');

                $schema->build('id')->Primary()->Integer()->AutoIncrement();
                $schema->build('user_id')->String();
                $schema->build('type')->String();
                $schema->build('location')->String();
                $schema->build('created_at')->Timestamp();

            }

            /**
             * Rollback directives
            *
            * @return void
            */
            public function unset(){


            }
        }
