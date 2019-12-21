<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addbitcoinhugecriteria->Settings []
*/
class addbitcoinhugecriteria implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('Settings');
        $today = NULL;
        $schema->rawQuery("INSERT INTO Settings VALUES (NULL, 'RULE', 'BITCOIN_HUGE_WITHDRAWAL_CRITERIA', '500', '$today')");

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
