<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addmorefieldstodata->DataCardEPins []
*/
class addmorefieldstodata implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('DataCardEPins');

        $schema->build('serial')->String();
        $schema->build('product')->String();
        $schema->build('price')->String();


    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
