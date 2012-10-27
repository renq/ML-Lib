<?php

use ML\SQL\Connection_PDO_Sqlite;
use ML\SQL\Settings;


require_once __DIR__ . '/../../../ML/loader.php';;


class SqlConnectionPDOTest extends PHPUnit_Framework_TestCase {

	
	protected $connection;
	
	
	protected function setUp() {
		include(__DIR__ . '/../../config.php');
		$db  = $config['sqlite_db'];
		if (file_exists($db)) {
			unlink($db);
		}
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
    	$this->setExpectedException('ML\SQL\BindException');
    	$this->connection->query("SELECT * FROM cat WHERE name = ?", array());
    }
    
    
	public function testQueryTooMuchBindFail() {
    	$this->createTable();
    	$this->setExpectedException('ML\SQL\BindException');
    	$this->connection->query("SELECT * FROM cat WHERE name = ?", array('Simon\'s', 'Cat'));
    }
    
    
    public function testEscape() {
    	$this->assertEquals("'Simon''s Cat'", $this->connection->escape("Simon's Cat"));
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
    
    
    public function testGetAffectedRowsUpdate() {
    	$this->createTable();
		$statement = $this->connection->query("UPDATE cat SET colour = ?", array('white'));
		$this->assertEquals(2 , $this->connection->getAffectedRows());
    }
    
    
	public function testGetAffectedRowsInsert() {
    	$this->createTable();
		$statement = $this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
		$this->assertEquals(1 , $this->connection->getAffectedRows());
    }
    
    
	public function testGetAffectedRowsDelete() {
    	$this->createTable();
		$statement = $this->connection->query("INSERT INTO cat (name, colour) VALUES (?, ?)", array('Nennek', 'black'));
		$statement = $this->connection->query("DELETE FROM cat WHERE id = ?", array(1));
		$this->assertEquals(1 , $this->connection->getAffectedRows());
    }
    
    
	public function testGetAffectedNoQuery() {
		$this->setExpectedException('\ml\sql\Exception');
    	$this->connection->getAffectedRows();
    }
    
    
    public function testSetHandle() {
    	$settings = $this->getMock('ML\SQL\Settings', array(), array('dsn'), 'MockPDOSettings', false);
    	$connection = $this->getMockForAbstractClass('ML\SQL\Connection_PDO', array($settings), 'Connection_PDOMock');
    	$handle = 'some handle';
    	$connection->setHandle($handle);
    	$this->assertEquals($handle, $connection->getHandle());
    }
    

}
