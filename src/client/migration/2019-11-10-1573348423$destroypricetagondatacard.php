<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:destroypricetagondatacard->DataCard []
*/
class destroypricetagondatacard implements Migration{

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

        $schema->destroy('price');
        $schema->build('network_provider')->Enum('MTN', 'GLO', '9MOBILE', 'AIRTEL');

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
