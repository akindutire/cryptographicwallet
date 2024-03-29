<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addpricetagtodatacard->DataCard []
*/
class addpricetagtodatacard implements Migration{

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

        $schema->destroy('created_at', 'price');
        $schema->build('price')->String();
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
