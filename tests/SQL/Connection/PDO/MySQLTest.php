<?php

use ML\SQL\Connection_PDO_MySQL;
use ML\SQL\Settings;


require_once __DIR__ . '/../../../../ML/loader.php';


class SqlConnectionPDOMySQLTest extends PHPUnit_Framework_TestCase {

	
	protected $connection;
	
	
	protected function setUp() {
		include(__DIR__ . '/../../../config.php');
		$dsn = $config['mysql_dsn'];
		$settings = new Settings($dsn);
		$this->connection = new Connection_PDO_MySQL($settings);
	}
	
	
	public function testConnect() {
		$this->connection->connect();
		$this->assertTrue($this->connection->getHandle() instanceof \PDO);
	}
	
	
	public function testConnectFail() {
		$this->setExpectedException('ML\SQL\Exception');
		$settings = new Settings('mysql://super_mega_user@localhost/fake_db');
		$connection = new Connection_PDO_MySQL($settings);
		$connection->connect();
	}
	
	
    public function testPasswordHide() {
    	$password = $this->connection->getSettings()->getPassword();
    	$this->connection->connect();
    	$hiddenPassword = $this->connection->getSettings()->getPassword();
    	$this->assertFalse($password == $hiddenPassword, 'After connect password should be removed from the settings object for security reasons.');
    }


}
