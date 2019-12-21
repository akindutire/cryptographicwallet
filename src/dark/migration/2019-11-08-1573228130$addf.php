<?php
namespace src\dark\migration;


use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addf->Users []
*/
class addf implements Migration{

    /**
     * Attributes to be created
    *
    * @return void
    */
    public function set(){

        /**
         * New Schema or Connect to existing schema
        */

        $schema = new Schema('Users');

        $schema->build('lastname01')->After('firstname')->String();

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
