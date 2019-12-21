<?php
        namespace src\client\migration;


        use zil\factory\Schema;
        use zil\core\interfaces\Migration;

        /**
         *   @Migration:recharge_pin->RechargePin []
        */
        class recharge_pin implements Migration{

            /**
             * Attributes to be created
            *
            * @return void
            */
            public function set(){

                /**
                 * New Schema or Connect to existing schema
                */

                $schema = new Schema('RechargePin');

                $schema->build('id')->Primary()->Integer()->AutoIncrement();
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
