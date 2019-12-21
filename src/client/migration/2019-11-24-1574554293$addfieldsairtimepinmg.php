<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addfieldsairtimepinmg->AirtimeEPins []
*/
class addfieldsairtimepinmg implements Migration{

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

        $schema->build('network_provider')->String()->After('id');

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
