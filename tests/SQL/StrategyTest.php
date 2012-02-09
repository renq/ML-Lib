<?php


use ML\SQL\Strategy_MySQL;
require_once __DIR__ . '/../../ML/loader.php';


class StrategyTest extends PHPUnit_Framework_TestCase {
	
	
	private $strategy;
	
	
	public function setUp() {
		$settings = $this->getMock('ML\SQL\Settings');
		$connection = $this->getMock('ML\SQL\Connection', array(), array($settings));
		$this->strategy = new Strategy_MySQL($connection); 
	}
	

    public function testOne() {
    	$this->assertEquals("query", $this->strategy->one("query"));
    }
    
    
    private function removeDoubleSpaces($string) {
    	return trim(preg_replace('/[\s]+/', ' ', $string));
    }
    
    
	public function testById() {
		$e = $this->strategy->getEscapeIdentifierCharacter();
		$byId = $this->removeDoubleSpaces($this->strategy->byId('table', 'table_id'));
    	$this->assertEquals("SELECT * FROM {$e}table{$e} WHERE {$e}table_id{$e} = ?", $byId);
    }
    
    
	public function testInsert() {
		$e = $this->strategy->getEscapeIdentifierCharacter();
		$params = array(
			'name' => 'My Name',
			'city' => 'Warsaw',
			'when' => '2010-02-20',
		);
		$query = $this->removeDoubleSpaces($this->strategy->insert('table', $params));
		$expected = "INSERT INTO {$e}table{$e} ({$e}name{$e}, {$e}city{$e}, {$e}when{$e}) VALUES (?, ?, ?)";
    	$this->assertEquals($expected, $query);
    }
    
    
	public function testUpdate() {
		$e = $this->strategy->getEscapeIdentifierCharacter();
		$params = array(
			'name' => 'My Name',
			'city' => 'Warsaw',
			'when' => '2010-02-20',
		);
		$query = $this->removeDoubleSpaces($this->strategy->update('table', $params, 'id'));
		$expected = "UPDATE {$e}table{$e} SET {$e}name{$e} = ?, {$e}city{$e} = ?, {$e}when{$e} = ? WHERE {$e}id{$e} = ?";
    	$this->assertEquals($expected, $query);
    }
    
    
	public function testDelete() {
		$e = $this->strategy->getEscapeIdentifierCharacter();
		$query = $this->removeDoubleSpaces($this->strategy->delete('table', 'id'));
		$expected = "DELETE FROM {$e}table{$e} WHERE {$e}id{$e} = ?";
    	$this->assertEquals($expected, $query);
    }
    
    
    private function getStrategyMock() {
    	$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMockForAbstractClass('ML\SQL\Connection_PDO', array($settings));
    	return $this->getMock('ML\SQL\Strategy_MySQL', array('escapeIdentifier'), array($connection));    	
    }
    
    
    public function testByIdUsingEscapeIdentifier() {
		$strategy = $this->getStrategyMock();
    	$strategy->expects($this->any())->method('escapeIdentifier');
    	$strategy->byId('table', 1);
    }
    
    
    public function testInsertEscapeIdentifier() {
		$strategy = $this->getStrategyMock();
    	$strategy->expects($this->any())->method('escapeIdentifier');
    	$strategy->insert('table', array('a' => 'b'));
    }
    
    
    public function testUpdateUsingEscapeIdentifier() {
		$strategy = $this->getStrategyMock();
    	$strategy->expects($this->any())->method('escapeIdentifier');
    	$strategy->update('table', array('a' => 'b'), 1);
    }
    
    
    public function testDeleteUsingEscapeIdentifier() {
		$strategy = $this->getStrategyMock();
    	$strategy->expects($this->any())->method('escapeIdentifier');
    	$strategy->delete('table', 1);
    }
    
    
	public function testDescribe() {
		$query = $this->removeDoubleSpaces($this->strategy->describe());
		$expected = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = ? AND table_name = ?";
    	$this->assertEquals($expected, $query);
    }
    
    
    public function testEscapeIdentifierSetterAndGetter() {
    	$e = '\\';
    	$this->strategy->setEscapeIdentifierCharacter($e);
    	$this->assertEquals($e, $this->strategy->getEscapeIdentifierCharacter());
    }
    
    
    public function testEscapeIdentifier() {
    	$this->strategy->setEscapeIdentifierCharacter('"');
    	$this->assertEquals('"table"', $this->strategy->escapeIdentifier('table'));
    	$this->assertEquals('"database"."table"', $this->strategy->escapeIdentifier('database.table'));
    	$this->assertEquals('"database"."table"."field"', $this->strategy->escapeIdentifier('database.table.field'));
    	$this->strategy->setEscapeIdentifierCharacter('%^%');
    	$this->assertEquals('%^%database%^%.%^%table%^%.%^%field%^%', $this->strategy->escapeIdentifier('database.table.field'));
    }
    
    
}
