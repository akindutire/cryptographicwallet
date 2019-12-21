<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:adddisableflagtoproductcat->Product_cat []
*/
class adddisableflagtoproductcat implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('Product_cat');

        $schema->build('is_disable')->Boolean();

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
