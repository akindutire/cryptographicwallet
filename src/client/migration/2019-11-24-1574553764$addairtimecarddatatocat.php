<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addairtimecarddatatocat->Product_cat []
*/
class addairtimecarddatatocat implements Migration{

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

        $schema->rawQuery("INSERT INTO Product_cat VALUES (NULL, '4', 'MTN', NULL), (NULL, '4', 'GLO', NULL), (NULL, '4', 'AIRTEL', NULL), (NULL, '4', '9MOBILE', NULL)");

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
