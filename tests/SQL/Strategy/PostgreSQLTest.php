<?php


use ML\SQL\Strategy_PostgreSQL;
use ML\SQL\Settings;

require_once __DIR__ . '/../../../ML/loader.php';


class SqlStrategyPostgreSQLTest extends PHPUnit_Framework_TestCase {

	
	public function testLimit() {
		$settings = $this->getMock('ML\SQL\Settings');
    	$connection = $this->getMock('ML\SQL\Connection', array(), array($settings));
    	$strategy = new Strategy_PostgreSQL($connection);
    	$query = "SELECT * FROM dual";
    	$queryWithLimit = $strategy->limit($query, 20, 100);
    	$queryWithLimit = preg_replace('/[\s]+/', ' ', $queryWithLimit);
    	
    	$this->assertEquals("$query LIMIT 20 OFFSET 100", $queryWithLimit);
    }


}

