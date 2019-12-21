<?php
namespace src\adminhub\config;

use \zil\core\interfaces\Config as ConfigInf;

use src\adminhub\route\Web;
use src\adminhub\route\Api;


/**
 *   App Configuration
 */

class Config implements ConfigInf{

	private const DB_DRIVER 	=   'mysql';
	private const DB_HOST 		=   'localhost';
	private const DB_USER 		=   'root';
	private const DB_PASSWORD 	=   '';
	private const DB_NAME 		=   'cryptographicwallet';
	private const DB_PORT 		=    3306;
	private const DB_ENGINE		=   'MyISAM';
	private const DB_CHARSET	=   'latin1';

	private const APP_NAME  = 'adminhub';


	public function __construct(){  }

	/**
	 * Specify app name and expected to be unique
	 *
	 * @return string
	 */
	public function getAppName(): string{

		return self::APP_NAME;
	}


	
	/**
	 * Database Info
	 *
	 * @return array
	 */
	public function getDatabaseParams(): array{
		return [
			'driver'=>self::DB_DRIVER,
			'host'=>self::DB_HOST,
			'user'=>self::DB_USER,
			'password'=>self::DB_PASSWORD,
			'database'=>self::DB_NAME,
			'port'=>self::DB_PORT,
			'engine'=>self::DB_ENGINE,
			'charset'=>self::DB_CHARSET
		];
	}
	
	/**
	 * Other configuration options
	 *
	 * @return array
	 */
	public function options(): array{

		return [
			'pageLoadStategy' => 'async',
			'projectBasePath' => '/',
			'appGuardClass' => '\src\adminhub\service\GuardAdminLogin'
		];

	}


	public function getUploadPath(){
		return  $_SERVER['DOCUMENT_ROOT'].'/'.$this->options()['projectBasePath'].'/src/'.self::APP_NAME.'/asset/uresource/uploads';
	}

	public function getUrlofUpload(string $file){
		$scheme = 'http://';
		if( isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] )
			$scheme = 'https://';

		$n = self::APP_NAME;
		return  $scheme.$_SERVER['HTTP_HOST'].$this->options()['projectBasePath']."/src/{$n}/asset/uresource/uploads/{$file}";
	}
}



?>
