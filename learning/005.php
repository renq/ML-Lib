<?php

use ml\sql\SQL;

date_default_timezone_set('Europe/Warsaw');

include('../ml/ml.php');
$dsn = 'pgsql://postgres:mlsni2@localhost/postgres';

error_reporting(E_ALL);
ini_set('display_errors', true);



$pdo = new \PDO("pgsql:host=localhost;port=5432;dbname=postgres", 'postgres', 'mlsni2');
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

$connection = new ml\sql\Connection_PDO_PostgreSQL(new ml\sql\Settings());
$connection->setHandle($pdo);
$strategy = new ml\sql\Strategy_PostgreSQL($connection);

$sql = new SQL($connection, $strategy);


print_r($sql->get("SELECT * FROM cats"));

