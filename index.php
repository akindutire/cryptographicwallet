<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/System/vendor/autoload.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/System/zil/main.php';

use zil\App;
use src\client\config\Config as clientCFG;
use src\adminhub\config\Config as adminhubCFG;
use src\dark\config\Config as darkCFG;

$cfg = ['0' => new clientCFG, '1' => new adminhubCFG, '2' => new darkCFG,];

$AppSpace = new App($cfg);

/**
 * @params
 *  true - allow all | false - deny all
 */
$AppSpace->start();

