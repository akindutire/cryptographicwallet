<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:renamefieldsofauthtoken->Authtoken []
*/
class renamefieldsofauthtoken implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('Authtoken');

        $schema->destroy('zxtok_flask', 'zxtok_flask_user_pk');

        $schema->build( 'token')->String(1000)->Unique();
        $schema->build('claim')->String()->Unique();

        $schema->destroy('expires_at');

        $schema->build('expires_at')->Timestamp();

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
