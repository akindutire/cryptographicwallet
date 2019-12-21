<?php
namespace src\client\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addmorefieldstoaffiliate->Affiliate []
*/
class addmorefieldstoaffiliate implements Migration{

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

        $schema->destroy('created_at', 'type', 'location');

         $schema->build('business_name')->Unique()->String();
         $schema->build('business_reg_no')->String();
        $schema->build('full_name')->String();
        $schema->build('email')->String();
        $schema->build('phone')->String();
        $schema->build('home_addr')->String();
        $schema->build('office_addr')->String();
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
