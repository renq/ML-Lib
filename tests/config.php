<?php

$config = array(


	'sqlite_db' => __DIR__ . '/temp/sqlite_test.db',

	'mysql_db'  => 'mysql://root:gsub@localhost/mlsql',

	'mysql_dsn' => 'mysql://root:gsub@127.0.0.1/information_schema',

	'pgsql_dsn' => 'pgsql://renq:test@localhost/mlsql',
	
        
);

if (!defined('TEST_PGSQL')) {
    define('TEST_PGSQL', false);
}