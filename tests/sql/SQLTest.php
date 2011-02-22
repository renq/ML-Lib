<?php


use ml\sql\SQL;
require_once dirname(__FILE__) . '/../../ml/ml.php';


class SQLTest extends PHPUnit_Framework_TestCase {


    
    public function testCreateByDSNMySQL() {
    	$dsn = 'mysql://root:pass@localhost/db';
    	$sql = \ml\sql\SQL::createByDSN($dsn);
    	$this->assertTrue($sql instanceof \ml\sql\SQL, 'SQL::createByDSN should returns instance of class SQL');
    	$this->assertTrue($sql->getConnection() instanceof \ml\sql\Connection_PDO_MySQL, 'Connection should be an instance of Connection_PDO_MySQL');
    	$this->assertTrue($sql->getStrategy() instanceof \ml\sql\Strategy_Mysql, 'Strategy should be an instance of Strategy_Mysql');
    }
    
    
	public function testCreateByDSNPostgresql() {
    	$dsn = 'pgsql://root:pass@localhost/db';
    	$sql = \ml\sql\SQL::createByDSN($dsn);
    	$this->assertTrue($sql instanceof \ml\sql\SQL, 'SQL::createByDSN should returns instance of class SQL');
    	$this->assertTrue($sql->getConnection() instanceof \ml\sql\Connection_PDO_PostgreSQL, 'Connection should be an instance of Connection_PDO_PostgreSQL');
    	$this->assertTrue($sql->getStrategy() instanceof \ml\sql\Strategy_PostgreSQL, 'Strategy should be an instance of Strategy_PostgreSQL');
    }
    
    
	public function testCreateByDSNSqlite() {
    	$dsn = 'sqlite:///some/file/';
    	$sql = \ml\sql\SQL::createByDSN($dsn);
    	$this->assertTrue($sql instanceof \ml\sql\SQL, 'SQL::createByDSN should returns instance of class SQL');
    	$this->assertTrue($sql->getConnection() instanceof \ml\sql\Connection_PDO_Sqlite, 'Connection should be an instance of Connection_PDO_PostgreSQL');
    	$this->assertTrue($sql->getStrategy() instanceof \ml\sql\Strategy_Sqlite, 'Strategy should be an instance of Strategy_Sqlite');
    }
    
    
	public function testCreateByDSNUnknownDatabase() {
		$this->setExpectedException('\ml\sql\Exception');
    	$dsn = 'someUnknownDatabase://someUser@someServer/someDtabase/';
    	$sql = \ml\sql\SQL::createByDSN($dsn);
    }
    
    
    private function methodCall($method) {
    	$settings = $this->getMock('ml\sql\Settings');
    	$connection = $this->getMock('ml\sql\Connection_PDO_MySQL', array($method), array($settings));
    	$strategy = $this->getMock('ml\sql\Strategy_MySQL', array(), array($connection));
    	$sql = new SQL($connection, $strategy);
    	
    	$connection->expects($this->once())->method($method);
    	$sql->$method();
    }
    
    
    public function testBeginTransaction() {
		$this->methodCall('beginTransaction');
    }
    
    
	public function testCommit() {
		$this->methodCall('commit');
    }
    
    
	public function testRollback() {
		$this->methodCall('rollback');
    }
    
    
	public function testGetDebug() {
		$this->methodCall('getDebug');
    }
    
    
    private function getSQLWithMocks() {
    	$settings = $this->getMock('ml\sql\Settings');
    	$connection = $this->getMock('ml\sql\Connection_PDO_MySQL', array(), array($settings));
    	$strategy = $this->getMock('ml\sql\Strategy_MySQL', array(), array($connection));
    	return new SQL($connection, $strategy);
    }
    
    
	public function testSaveInvalidTableName() {
		$this->setExpectedException('\InvalidArgumentException');
		$sql = $this->getSQLWithMocks();
    	$sql->save(array(), array());
    }
    
    
	public function testSaveInvalidParams() {
		$this->setExpectedException('\InvalidArgumentException');
		$sql = $this->getSQLWithMocks();
    	$sql->save('table', array('ala', 'ma', 'kota'));
    }
    
    
	public function testSaveEmpryParams() {
		$this->setExpectedException('\InvalidArgumentException');
		$sql = $this->getSQLWithMocks();
		$sql->save('table', array());
    }
    
    
	public function testSaveInsert() {
		$table = 'cat';
		$params = array('name' => 'Nennek', 'colour' => 'black');
		
    	$settings = $this->getMock('ml\sql\Settings');
    	$connection = $this->getMock('ml\sql\Connection_PDO_MySQL', array(), array($settings));
		$connection->expects($this->once())->method('query');
    	$strategy = $this->getMock('ml\sql\Strategy_MySQL', array(), array($connection));
		$strategy->expects($this->once())->method('insert');
    	$sql = new SQL($connection, $strategy);
    	$sql->save($table, $params);
    }
    
    
	public function testSaveUpdate() {
		$table = 'cat';
		$params = array('name' => 'Nennek', 'colour' => 'black');
    	$settings = $this->getMock('ml\sql\Settings');
    	$connection = $this->getMock('ml\sql\Connection_PDO_MySQL', array(), array($settings));
		$connection->expects($this->once())->method('query');
		$connection->expects($this->once())->method('getAffectedRows')->will($this->returnValue(1));
    	$strategy = $this->getMock('ml\sql\Strategy_MySQL', array(), array($connection));
		$strategy->expects($this->once())->method('update');
    	$sql = new SQL($connection, $strategy);
    	$sql->save($table, $params, 5);
    }
    
    
	public function testSaveUpdateWithWrongID() {
		$this->setExpectedException('\ml\sql\SqlException');
		$table = 'cat';
		$params = array('name' => 'Nennek', 'colour' => 'black');
    	$settings = $this->getMock('ml\sql\Settings');
    	$connection = $this->getMock('ml\sql\Connection_PDO_MySQL', array(), array($settings));
		$connection->expects($this->once())->method('query');
		$connection->expects($this->once())->method('getAffectedRows')->will($this->returnValue(0));
    	$strategy = $this->getMock('ml\sql\Strategy_MySQL', array(), array($connection));
    	$sql = new SQL($connection, $strategy);
    	$sql->save($table, $params, 100);
    }
    


}
