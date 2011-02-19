<?php


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
    


}
