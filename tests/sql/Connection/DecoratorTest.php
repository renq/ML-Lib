<?php

use ml\sql\Connection_Decorator;
use ml\sql\Settings;


require_once dirname(__FILE__) . '../../../../ml/ml.php';


class SqlConnectionDecoratorTest extends PHPUnit_Framework_TestCase {

	
	protected $connection;
	protected $decorated;
	
	
	protected function setUp() {
		$settings = new Settings();
		$this->connection = $this->getMock('ml\sql\Connection_PDO_Sqlite', array(), array($settings));
		$this->decorated = new Connection_Decorator($this->connection);
	}
	
	
	public function testConnect() {
		$this->connection->expects($this->once())->method('connect');
		$this->decorated->connect();
	}
	
	
	public function testDisconnect() {
		$this->connection->expects($this->once())->method('disconnect');
		$this->decorated->disconnect();
	}
	
	
	public function testEscape() {
		$this->connection->expects($this->once())->method('escape');
		$this->decorated->escape('aaa');
	}
	
	
	public function testFetch() {
		$this->connection->expects($this->once())->method('fetch');
		$this->decorated->fetch(null);
	}
	
	
	public function testBeginTransaction() {
		$this->connection->expects($this->once())->method('beginTransaction');
		$this->decorated->beginTransaction();
	}
	
	
	public function testCommit() {
		$this->connection->expects($this->once())->method('commit');
		$this->decorated->commit();
	}
	
	

	public function testRollback() {
		$this->connection->expects($this->once())->method('rollback');
		$this->decorated->rollback();
	}
	
	
	public function testLastInsertId() {
		$this->connection->expects($this->once())->method('lastInsertId');
		$this->decorated->lastInsertId();
	}
	

	public function testGetHandle() {
		$this->connection->expects($this->once())->method('getHandle');
		$this->decorated->getHandle();
	}
	
	
	public function testSetHandle() {
		$handle = 'anything';
		$this->connection->expects($this->once())->method('setHandle')->with($this->equalTo($handle));
		$this->decorated->setHandle($handle);
	}
	
	
	public function testQuery() {
		$this->connection->expects($this->once())->method('query');
		$this->decorated->query("QUERY", array());
	}
	

	public function testGetAffectedRows() {
		$this->connection->expects($this->once())->method('getAffectedRows');
		$this->decorated->getAffectedRows();
	}
    

}

