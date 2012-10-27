<?php

use ML\SQL\Connection_PDO_MySQL;
use ML\SQL\Connection_MySQL;
use ML\SQL\Settings;


require_once __DIR__ . '/../../../ML/loader.php';


class SqlConnectionMySQLTest extends PHPUnit_Framework_TestCase {

	
	protected $connection;
	
	
	protected function setUp() {
		include(__DIR__ . '/../../config.php');
		$db  = $config['mysql_db'];
		$settings = new Settings($db);
		$database = $settings->getDatabase();
		$settings->setDatabase(null);
		$this->connection = new Connection_MySQL($settings);
		$this->connection->query("DROP DATABASE IF EXISTS $database;"); 
		$this->connection->query("CREATE DATABASE $database;");
		$this->connection->query("USE $database;");
		$settings->setDatabase($database);
		$this->connection->query("CREATE TABLE cat (
			id INTEGER PRIMARY KEY AUTO_INCREMENT,
			name VARCHAR(50),
			colour VARCHAR(50)
		) ENGINE=InnoDB");
		$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array("Simon's Cat", 'black'));
		$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array("Garfield", 'ginger'));
	}
	
	
	protected function tearDown() {
		$database = $this->connection->getSettings()->getDatabase();
		$this->connection->query("DROP DATABASE $database");
	}
	
	
	public function testConnectFail() {
		$this->setExpectedException('ML\SQL\Exception');
		$settings = new Settings('mysql://root:fake_pass@localhost/fake_db');
		$connection = new Connection_MySQL($settings);
    	$connection->connect();
    }
	
	
	public function testDisconnect() {
		include(__DIR__ . '/../../config.php');
		$db  = $config['mysql_db'];
		$settings = new Settings($db);
		$connection = new Connection_MySQL($settings);
    	$this->assertNull($connection->getHandle());
    	$connection->query("SELECT NOW()");
    	$this->assertTrue(is_resource($connection->getHandle()), "After connect handle should be resource, but it is: ".gettype($connection->getHandle()));
    	$connection->disconnect();
    	$this->assertNull($connection->getHandle());
    	
    	$this->setUp(); // because tearDown makes an error.
    }
    
    
    public function testQuery() {
		$statement = $this->connection->query("SELECT * FROM cat");
		$row = $this->connection->fetch($statement);
		$this->assertEquals("Simon's Cat", $row['name']);
		$row = $this->connection->fetch($statement);
		$this->assertEquals("Garfield", $row['name']);
    }
    
    
    public function testQueryTooFewBindFail() {
    	$this->setExpectedException('ML\SQL\BindException');
    	$this->connection->query("SELECT * FROM cat WHERE name = ?", array());
    }
    
    
	public function testQueryTooMuchBindFail() {
    	$this->setExpectedException('ML\SQL\BindException');
    	$this->connection->query("SELECT * FROM cat WHERE name = ?", array('Simon\'s', 'Cat'));
    }
    
    
    public function testEscape() {
    	$this->assertEquals("'Simon\\'s Cat'", $this->connection->escape("Simon's Cat"));
    	$this->assertEquals(12, $this->connection->escape(12));
    	$this->assertEquals("'0012'", $this->connection->escape('0012'));
    	$this->assertEquals('NULL', strtoupper($this->connection->escape(null)));
    	$this->assertEquals("''", strtoupper($this->connection->escape('')));
    	$this->assertEquals(1, $this->connection->escape(true));
    }
    
    
	public function testQuerySqlFail() {
    	$this->setExpectedException('ML\SQL\Exception');
    	$this->connection->query("SELECT * FROM ninja_table");
    }
    
    
	public function testLastInsertId() {
    	$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
    	$this->assertEquals(3, $this->connection->lastInsertId('cat'));
    	$this->assertEquals(3, $this->connection->lastInsertId());
    	$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Misia', 'white-black-gray stripes'));
    	$this->assertEquals(4, $this->connection->lastInsertId());
    }
    
    
    public function testTransactionRollback() {
    	$this->connection->beginTransaction();
    	$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
    	$this->connection->rollback();
    	$statement = $this->connection->query("SELECT count(*) AS c FROM cat");
    	$row = $this->connection->fetch($statement);
    	$count = array_shift($row);
    	$this->assertEquals(2, $count);
    }
    
    
	public function testTransactionCommit() {
    	$this->connection->beginTransaction();
    	$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
    	$this->connection->commit();
    	$statement = $this->connection->query("SELECT count(*) AS c FROM cat");
    	$row = $this->connection->fetch($statement);
    	$count = array_shift($row);
    	$this->assertEquals(3, $count);
    }
    
    
	public function testFetchEOF() {
    	$statement = $this->connection->query("SELECT * FROM cat");
    	$this->connection->fetch($statement); // Simon's Cat
    	$this->connection->fetch($statement); // Garfield
    	$this->assertFalse($this->connection->fetch($statement));
    }
    
    
    public function testGetAffectedRowsUpdate() {
    	$res = $this->connection->query("SELECT count(*) FROM cat");
		$statement = $this->connection->query("UPDATE cat SET colour = ?", array('white'));
		$this->assertEquals(2 , $this->connection->getAffectedRows());
    }
    
    
	public function testGetAffectedRowsInsert() {
		$statement = $this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
		$this->assertEquals(1 , $this->connection->getAffectedRows());
    }
    
    
	public function testGetAffectedRowsDelete() {
		$statement = $this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
		$statement = $this->connection->query("DELETE FROM cat WHERE id = ?", array(1));
		$this->assertEquals(1 , $this->connection->getAffectedRows());
    }
    

    public function testGetAffectedNoQuery() {
    	include(__DIR__ . '/../../config.php');
		$db  = $config['mysql_db'];
		$settings = new Settings($db);
		$connection = new Connection_MySQL($settings);
		$this->setExpectedException('\ml\sql\Exception');
    	$connection->getAffectedRows();
    }
    
    
    public function testSetHandle() {
    	$settings = $this->getMock('ML\SQL\Settings', array(), array('dsn'), 'MockSettings', false);
    	$connection = new Connection_MySQL($settings);
    	$handle = 'some handle';
    	$connection->setHandle($handle);
    	$this->assertEquals($handle, $connection->getHandle());
    }


}

