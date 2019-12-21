<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:removedatefromdatacardtab->DataCard []
*/
class removedatefromdatacardtab implements Migration{

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
        $schema->destroy('date');

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){

        $schema = new Schema('DataCard');

    }
}
