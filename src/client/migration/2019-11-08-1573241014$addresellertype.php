<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addresellertype->Affiliate []
*/
class addresellertype implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('Affiliate');

        $schema->destroy('created_at');

        $schema->build('type')->String();
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
