<?php

use ml\sql\Connection_PDO_Sqlite;
use ml\sql\Settings;


require_once dirname(__FILE__) . '../../../../ml/ml.php';


class SqlConnectionPDOTest extends PHPUnit_Framework_TestCase {

	
	protected $connection;
	
	
	protected function setUp() {
		include(__DIR__ . '/../../config.php');
		$db  = $config['sqlite_db'];
		unlink($db);
		$settings = new Settings("sqlite:///$db");
		$this->connection = new Connection_PDO_Sqlite($settings);
	}
	
	
	private function createTable() {
		$this->connection->query("CREATE TABLE cat (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			name VARCHAR(50),
			colour VARCHAR(50)
		)");
		$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array("Simon's Cat", 'black'));
		$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array("Garfield", 'ginger'));
	}
	
	
	public function testDisconnect() {
    	$this->assertNull($this->connection->getHandle());
    	$this->connection->query("SELECT DATE('now')");
    	$this->assertTrue(($handle = $this->connection->getHandle()) instanceof PDO, "After connect handle should be instance of PDO, but it is: ".get_class($handle));
    	$this->connection->disconnect();
    	$this->assertNull($this->connection->getHandle());
    }
    
    
    public function testQuery() {
		$this->createTable();
		$statement = $this->connection->query("SELECT * FROM cat");
		$row = $this->connection->fetch($statement);
		$this->assertEquals("Simon's Cat", $row['name']);
		$row = $this->connection->fetch($statement);
		$this->assertEquals("Garfield", $row['name']);
    }
    
    
    public function testQueryTooFewBindFail() {
    	$this->createTable();
    	$this->setExpectedException('ml\sql\BindException');
    	$this->connection->query("SELECT * FROM cat WHERE name = ?", array());
    }
    
    
	public function testQueryTooMuchBindFail() {
    	$this->createTable();
    	$this->setExpectedException('ml\sql\BindException');
    	$this->connection->query("SELECT * FROM cat WHERE name = ?", array('Simon\'s', 'Cat'));
    }
    
    
    public function testEscape() {
    	$this->assertEquals("'Simon''s Cat'", $this->connection->escape("Simon's Cat"));
    }
    
    
	public function testQuerySqlFail() {
    	$this->setExpectedException('ml\sql\SqlException');
    	$this->connection->query("SELECT * FROM ninja_table");
    }
    
    
	public function testLastInsertId() {
		$this->createTable();
    	$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
    	$this->assertEquals(3, $this->connection->lastInsertId('cat'));
    	$this->assertEquals(3, $this->connection->lastInsertId());
    	$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Misia', 'white-black-gray stripes'));
    	$this->assertEquals(4, $this->connection->lastInsertId());
    }
    
    
    public function testTransactionRollback() {
    	$this->createTable();
    	$this->connection->beginTransaction();
    	$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
    	$this->connection->rollback();
    	$statement = $this->connection->query("SELECT count(*) AS c FROM cat");
    	$row = $this->connection->fetch($statement);
    	$count = array_shift($row);
    	$this->assertEquals(2, $count);
    }
    
    
	public function testTransactionCommit() {
    	$this->createTable();
    	$this->connection->beginTransaction();
    	$this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
    	$this->connection->commit();
    	$statement = $this->connection->query("SELECT count(*) AS c FROM cat");
    	$row = $this->connection->fetch($statement);
    	$count = array_shift($row);
    	$this->assertEquals(3, $count);
    }
    
    
	public function testFetchEOF() {
    	$this->createTable();
    	$statement = $this->connection->query("SELECT * FROM cat");
    	$this->connection->fetch($statement); // Simon's Cat
    	$this->connection->fetch($statement); // Garfield
    	$this->assertFalse($this->connection->fetch($statement));
    }


}
