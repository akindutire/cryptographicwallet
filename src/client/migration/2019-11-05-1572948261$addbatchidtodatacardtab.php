<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addbatchidtodatacardtab->DataCard []
*/
class addbatchidtodatacardtab implements Migration{

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

        $schema->build('batch_tag')->String();

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
