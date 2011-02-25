<?php


use ml\sql\Settings;
use ml\sql\Strategy_Sqlite;

require_once dirname(__FILE__) . '../../../../ml/ml.php';


class SqlStrategySqliteTest extends PHPUnit_Framework_TestCase {

	
	public function testLimit() {
		$settings = $this->getMock('ml\sql\Settings');
    	$connection = $this->getMock('ml\sql\Connection', array(), array($settings));
    	$strategy = new Strategy_Sqlite($connection);
    	$query = "SELECT * FROM dual";
    	$queryWithLimit = $strategy->limit($query, 20, 100);
    	$queryWithLimit = preg_replace('/[\s]+/', ' ', $queryWithLimit);
    	
    	$this->assertEquals("$query LIMIT 100, 20", $queryWithLimit);
    }


}

