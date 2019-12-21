<?php
         
         namespace src\dark\config;

         use \zil\core\interfaces\Config as ConfigInterface;

         /**
          *   @Configuration:dark
          */

         class Config implements ConfigInterface{


             private const DB_DRIVER 	=   'mysql';
             private const DB_HOST 		=   'remotemysql.com';
             private const DB_USER 		=   'xHXeT4DD5L';
             private const DB_PASSWORD 	=   'p2kpZUxNFQ';
             private const DB_NAME 		=   'xHXeT4DD5L';
             private const DB_PORT 		=    3306;
             private const DB_ENGINE	=   'MyISAM';
             private const DB_CHARSET	=   'latin1';

             private const APP_NAME	= "dark";


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
                     'driver'	=>	self::DB_DRIVER,
                     'host'		=>	self::DB_HOST,
                     'user'		=>	self::DB_USER,
                     'password'	=>	self::DB_PASSWORD,
                     'database'	=>	self::DB_NAME,
                     'port'		=>	self::DB_PORT,
                     'engine'	=>	self::DB_ENGINE,
                     'charset'	=>	self::DB_CHARSET
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
                     'appGuardClass' => ''
                 ];

             }

         }


