<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:adddisableflagtoproduct->Product []
*/
class adddisableflagtoproduct implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('Product');

        $schema->build('is_disable')->Boolean()->After('pdiscount');

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
