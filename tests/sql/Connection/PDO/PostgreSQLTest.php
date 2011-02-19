<?php

use ml\sql\Connection_PDO_PostgreSQL;
use ml\sql\Connection_PDO_MySQL;
use ml\sql\Settings;


require_once dirname(__FILE__) . '../../../../../ml/ml.php';


class SqlConnectionPDOPostgreSQLTest extends PHPUnit_Framework_TestCase {

	
	protected $connection;
	
	
	protected function setUp() {
		include(__DIR__ . '/../../../config.php');
		$dsn = $config['pgsql_dsn'];
		$settings = new Settings($dsn);
		$this->connection = new Connection_PDO_PostgreSQL($settings);
	}
	
	
	public function testConnect() {
		$this->connection->connect();
		$this->assertTrue($this->connection->getHandle() instanceof \PDO);
	}
	
	
	public function testConnectFail() {
		$this->setExpectedException('ml\sql\Exception');
		$settings = new Settings('pgsql://super_mega_user:pass@localhost/fake_db');
		$connection = new Connection_PDO_PostgreSQL($settings);
		$connection->connect();
	}


}
