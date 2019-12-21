<?php
namespace src\client\migration;

use zil\factory\Schema;
use zil\core\interfaces\Migration;

/**
 *   @Migration:addphonerespondertosettings->Settings []
*/
class addphonerespondertosettings implements Migration{

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
        $schema->rawQuery("INSERT INTO Settings VALUES (NULL, 'RULE', 'PHONE_RESPONDER_NUMBER_FOR_DATA_E_PIN_MTN_1GB', '09066959895', '$today'), (NULL, 'RULE', 'PHONE_RESPONDER_NUMBER_FOR_DATA_E_PIN_MTN_2GB', '07064549487', '$today')");

    }

    /**
     * Rollback directives
    *
    * @return void
    */
    public function unset(){


    }
}
