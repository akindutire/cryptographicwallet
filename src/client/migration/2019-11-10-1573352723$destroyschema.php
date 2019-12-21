<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:destroyschema->WalletAssetStoredEPisn []
*/
class destroyschema implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('WalletAssetStoredEPisn');

        $schema->destroySchema(true);
    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
