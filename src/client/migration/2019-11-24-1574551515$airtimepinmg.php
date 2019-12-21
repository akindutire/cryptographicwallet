<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:airtimepinmg->AirtimeEPins []
*/
class airtimepinmg implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('AirtimeEPins');

        $schema->build('id')->Primary()->Integer()->AutoIncrement();
        $schema->build('product')->String();
        $schema->build('pin_code')->String()->Unique();
        $schema->build('serial_no')->String();
        $schema->build('status_code')->String();
        $schema->build('date')->String();
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
