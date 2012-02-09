<?php


use ML\SQL\SQL;
require_once __DIR__ . '/../../ML/loader.php';


class SQLTest extends PHPUnit_Framework_TestCase {


    
    public function testCreateByDSNMySQL() {
    	$dsn = 'mysql://root:pass@localhost/db';
    	$sql = \ml\sql\SQL::createByDSN($dsn);
    	$this->assertTrue($sql instanceof \ml\sql\SQL, 'SQL::createByDSN should returns instance of class SQL');
    	$this->assertTrue($sql->getConnection() instanceof \ml\sql\Connection_PDO_MySQL, 'Connection should be an instance of Connection_PDO_MySQL');
    	$this->assertTrue($sql->getStrategy() instanceof \ml\sql\Strategy_MySQL, 'Strategy should be an instance of Strategy_MySQL');
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
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_MySQL', array($method), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_MySQL', array(), array($connection));
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
    
    /*
	public function testGetDebug() {
		$this->methodCall('getDebug');
    }*/
    
    
    private function getSQLWithMocks() {
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_MySQL', array(), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_MySQL', array(), array($connection));
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
		
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_MySQL', array(), array($settings));
		$connection->expects($this->once())->method('query');
    	$strategy = $this->getMock('ML\SQL\Strategy_MySQL', array(), array($connection));
		$strategy->expects($this->once())->method('insert');
    	$sql = new SQL($connection, $strategy);
    	$sql->save($table, $params);
    }
    
    
	public function testSaveUpdate() {
		$table = 'cat';
		$params = array('name' => 'Nennek', 'colour' => 'black');
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_MySQL', array(), array($settings));
		$connection->expects($this->once())->method('query');
		$connection->expects($this->once())->method('getAffectedRows')->will($this->returnValue(1));
    	$strategy = $this->getMock('ML\SQL\Strategy_MySQL', array(), array($connection));
		$strategy->expects($this->once())->method('update');
    	$sql = new SQL($connection, $strategy);
    	$sql->save($table, $params, 5);
    }
    
    
	public function testSaveUpdateWithWrongID() {
		$this->setExpectedException('\ml\sql\SqlException');
		$table = 'cat';
		$params = array('name' => 'Nennek', 'colour' => 'black');
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_MySQL', array(), array($settings));
		$connection->expects($this->once())->method('query');
		$connection->expects($this->once())->method('getAffectedRows')->will($this->returnValue(0));
    	$strategy = $this->getMock('ML\SQL\Strategy_MySQL', array(), array($connection));
    	$sql = new SQL($connection, $strategy);
    	$sql->save($table, $params, 100);
    }
    
    
	public function testDelete() {
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
		$connection->expects($this->once())->method('query');
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
		$strategy->expects($this->once())->method('delete');
    	$sql = new SQL($connection, $strategy);
    	$sql->delete('table', 1);
    }
    
    
    public function testQuery() {
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
		$connection->expects($this->once())->method('query');
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$sql = new SQL($connection, $strategy);
    	$sql->query('SELECT * FROM table WHERE id = ?', array(1));
    }
    
    
    public function testOne() {
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$strategy->expects($this->once())->method('one');
    	$sql = new SQL($connection, $strategy);
    	$sql->one('SELECT * FROM table WHERE id = ?', array(1));
    }
    
    
	public function testById() {
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
		$strategy->expects($this->once())->method('byId');
    	$sql = new SQL($connection, $strategy);
    	$sql->byId('table', 1);
    }
    
    
	public function testValue() {
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$sql = $this->getMock('ML\SQL\SQL', array('one'), array($connection, $strategy));
    	$sql->expects($this->any())->method('one')->will($this->returnValue(array('name' => 'Nennek', 'colour' => 'black')));
    	$this->assertEquals('Nennek', $sql->value('SELECT name FROM cat WHERE id = 1'));
    }
    
  
	public function testValueEmpty() {
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$sql = $this->getMock('ML\SQL\SQL', array('one'), array($connection, $strategy));
    	$sql->expects($this->any())->method('one')->will($this->returnValue(array()));
    	$this->assertFalse($sql->value('SELECT name FROM cat WHERE id = 1'));
    	$sql->expects($this->any())->method('one')->will($this->returnValue(false));
    	$this->assertFalse($sql->value('SELECT name FROM cat WHERE id = 1'));
    }
    
    
	public function testFlat() {
		$return = array(
			array('name' => 'Nennek', 'colour' => 'black'),
			array('name' => 'Misia', 'colour' => 'striped'),
			null
		);
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
    	$connection->expects($this->at(0))->method('query')->will($this->returnValue(true));
    	foreach ($return as $k => $item) {
    		$connection->expects($this->at($k+1))->method('fetch')->will($this->returnValue($item));
    	}
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$sql = new SQL($connection, $strategy);
    	$this->assertEquals(array('Nennek', 'Misia'), $sql->flat('SELECT name FROM cat'));
    }
    
    
    public function testGet() {
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$sql = new SQL($connection, $strategy);
    	$strategy->expects($this->never())->method('limit');
    	$sql->get("SELECT * FROM cat WHERE id = ?", array(1));
    	
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$sql = new SQL($connection, $strategy);
    	$strategy->expects($this->once())->method('limit');
    	$sql->get("SELECT * FROM cat WHERE id = ?", array(1), 10, 20);
    }
    
    
    public function testDescribe() {
		$columns = array(
			array('table_catalog'=>null,'table_schema'=>'sql','table_name'=>'table','column_name'=>'id','ordinal_position'=>1,'column_default'=>null,'is_nullable'=>'no','data_type'=>'int','character_maximum_length'=>null,'character_octet_length'=>null,'numeric_precision'=>10,'numeric_scale'=>0,'character_set_name'=>null,'collation_name'=>null,'column_type'=>'int(10) unsigned','column_key'=>'pri','extra'=>'auto_increment','privileges'=>'select,insert,update,references','column_comment'=>''),
			array('table_catalog'=>null,'table_schema'=>'sql','table_name'=>'table','column_name'=>'name','ordinal_position'=>2,'column_default'=>'','is_nullable'=>'no','data_type'=>'varchar','character_maximum_length'=>255,'character_octet_length'=>765,'numeric_precision'=>null,'numeric_scale'=>null,'character_set_name'=>'utf8','collation_name'=>'utf8_general_ci','column_type'=>'varchar(255)','column_key'=>'','extra'=>'','privileges'=>'select,insert,update,references','column_comment'=>''),
			array('table_catalog'=>null,'table_schema'=>'sql','table_name'=>'table','column_name'=>'active','ordinal_position'=>3,'column_default'=>null,'is_nullable'=>'no','data_type'=>'tinyint','character_maximum_length'=>null,'character_octet_length'=>null,'numeric_precision'=>3,'numeric_scale'=>0,'character_set_name'=>null,'collation_name'=>null,'column_type'=>'tinyint(1)','column_key'=>'','extra'=>'','privileges'=>'select,insert,update,references','column_comment'=>'')
		);

    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array('connect'), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$sql = $this->getMock('ML\SQL\SQL', array('get'), array($connection, $strategy));
    	$strategy->expects($this->once())->method('describe');
    	$sql->expects($this->once())->method('get')->will($this->returnValue($columns));
    	$sql->describe('table');
    }
    
    
    public function testDescribeFail() {
		$columns = array(
			array('table_catalog'=>null,'table_schema'=>'sql','table_name'=>'table','column_name'=>'id','ordinal_position'=>1,'column_default'=>null,'is_nullable'=>'no','data_type'=>'int','character_maximum_length'=>null,'character_octet_length'=>null,'numeric_precision'=>10,'numeric_scale'=>0,'character_set_name'=>null,'collation_name'=>null,'column_type'=>'int(10) unsigned','column_key'=>'pri','extra'=>'auto_increment','privileges'=>'select,insert,update,references','column_comment'=>''),
			array('table_catalog'=>null,'table_schema'=>'sql','table_name'=>'table','column_name'=>'name','ordinal_position'=>2,'column_default'=>'','is_nullable'=>'no','data_type'=>'varchar','character_maximum_length'=>255,'character_octet_length'=>765,'numeric_precision'=>null,'numeric_scale'=>null,'character_set_name'=>'utf8','collation_name'=>'utf8_general_ci','column_type'=>'varchar(255)','column_key'=>'','extra'=>'','privileges'=>'select,insert,update,references','column_comment'=>''),
			array('table_catalog'=>null,'table_schema'=>'sql','table_name'=>'table','column_name'=>'active','ordinal_position'=>3,'column_default'=>null,'is_nullable'=>'no','data_type'=>'tinyint','character_maximum_length'=>null,'character_octet_length'=>null,'numeric_precision'=>3,'numeric_scale'=>0,'character_set_name'=>null,'collation_name'=>null,'column_type'=>'tinyint(1)','column_key'=>'','extra'=>'','privileges'=>'select,insert,update,references','column_comment'=>'')
		);
		$this->setExpectedException('ML\SQL\SqlException');

    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array('connect'), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$sql = $this->getMock('ML\SQL\SQL', array('get'), array($connection, $strategy));
    	$strategy->expects($this->once())->method('describe');
    	$sql->expects($this->once())->method('get')->will($this->returnValue(false));
    	$sql->describe('table');
    }
    
    
    public function testSaveFromRequest() {
		$columns = array(
			'id' => true,
			'name' => true,
			'colour' => true,
		);
		$_REQUEST['id'] = 3;
		$_REQUEST['name'] = 'Nennek';
		$_REQUEST['colour'] = 'black';

    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
    	$strategy = $this->getMock('ML\SQL\Strategy_Sqlite', array(), array($connection));
    	$sql = $this->getMock('ML\SQL\SQL', array('describe', 'save'), array($connection, $strategy));
    	$sql->expects($this->once())->method('describe')->will($this->returnValue($columns));
    	$sql->expects($this->once())->method('save')->will($this->returnValue(3));
    	$this->assertEquals(3, $sql->saveFromRequest('table'));
    }
    


}
