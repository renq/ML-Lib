<?php


use ml\sql\Strategy_PostgreSQL;
use ml\sql\Settings;

require_once dirname(__FILE__) . '../../../../ml/ml.php';


class SqlStrategyPostgreSQLTest extends PHPUnit_Framework_TestCase {

	
	public function testLimit() {
		$settings = $this->getMock('ml\sql\Settings');
    	$connection = $this->getMock('ml\sql\Connection', array(), array($settings));
    	$strategy = new Strategy_PostgreSQL($connection);
    	$query = "SELECT * FROM dual";
    	$queryWithLimit = $strategy->limit($query, 20, 100);
    	$queryWithLimit = preg_replace('/[\s]+/', ' ', $queryWithLimit);
    	
    	$this->assertEquals("$query LIMIT 20 OFFSET 100", $queryWithLimit);
    }


}

