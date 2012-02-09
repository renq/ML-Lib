<?php


use ML\SQL\Settings;
use ML\SQL\Strategy_MySQL;

require_once __DIR__ . '/../../../ML/loader.php';


class SqlStrategyMySQLTest extends PHPUnit_Framework_TestCase {

	
	public function testLimit() {
		$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection', array(), array($settings));
    	$strategy = new Strategy_MySQL($connection);
    	$query = "SELECT * FROM dual";
    	$queryWithLimit = $strategy->limit($query, 20, 100);
    	$queryWithLimit = preg_replace('/[\s]+/', ' ', $queryWithLimit);
    	
    	$this->assertEquals("$query LIMIT 100, 20", $queryWithLimit);
    }


}

