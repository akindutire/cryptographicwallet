<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:removeserialnofromdatacard->DataCard []
*/
class removeserialnofromdatacard implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('DataCard');

        $schema->destroy('serial_no', 'retailer');

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
