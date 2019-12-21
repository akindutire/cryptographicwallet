<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:createstoredepins->WalletAssetStoredEPisn []
*/
class createstoredepins implements Migration{

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

        $schema->build('id')->Primary()->Integer()->AutoIncrement();
        $schema->build('wallet_key')->String()->Unique()->Foreign('Wallet', 'public_key');
        $schema->build('asset_value')->String();
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
