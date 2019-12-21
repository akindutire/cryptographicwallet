<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:createdatacardsectionusers->DataCardCustomer []
*/
class createdatacardsectionusers implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('DataCardCustomer');

        $schema->build('id')->Primary()->Integer()->AutoIncrement();
        $schema->build('customer_id')->Integer()->Unique()->Foreign('ExtraUserInfo', 'id');
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
