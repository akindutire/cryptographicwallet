<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addairtimecarddata->Product_type []
*/
class addairtimecarddata implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('Product_type');

        $schema->rawQuery("INSERT INTO Product_type VALUES (NULL, 'AIRTIME_CARD')");

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
