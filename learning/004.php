<?php

use ml\sql\SQL;

include('../ml/ml.php');
$dsn = 'pgsql://postgres:mlsni2@localhost/postgres';

$sql = SQL::createByDSN($dsn);
$sql->query("TRUNCATE TABLE cat");
/*$conn = $sql->getConnection();
$conn->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
var_dump($conn->lastInsertId('cat', 'id'));
*/
var_dump($sql->save('cat', array('name' => 'Nennek', 'colour' => 'black')));