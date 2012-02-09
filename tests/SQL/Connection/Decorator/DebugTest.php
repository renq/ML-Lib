<?php


use ML\SQL\Connection_Decorator_Debug;
use ML\SQL\Connection_Decorator;
use ML\SQL\Settings;


require_once __DIR__ . '/../../../../ML/loader.php';


class SqlConnectionDecoratorDebugTest extends PHPUnit_Framework_TestCase {

	
	public function testDebug() {
		$settings = new Settings();
		$connection = $this->getMock('ML\SQL\Connection_PDO_Sqlite', array(), array($settings));
		$decorator = new Connection_Decorator_Debug($connection);
		$connection->expects($this->any())->method('fetch')->will($this->returnValue(15));
		
		$this->assertEquals(array(), $decorator->getDebug());
		$decorator->query("SOME QUERY");
		$this->assertEquals(array("SOME QUERY"), $decorator->getDebug());
		$decorator->query("NEXT QUERY ?", array(15));
		$this->assertEquals(array("SOME QUERY", "NEXT QUERY 15"), $decorator->getDebug());
		
		$decorator->beginTransaction();
		$debug = $decorator->getDebug();
		$this->assertEquals('BEGIN TRANSACTION', end($debug));
		
		$decorator->commit();
		$debug = $decorator->getDebug();
		$this->assertEquals('COMMIT', end($debug));
		
		$decorator->rollback();
		$debug = $decorator->getDebug();
		$this->assertEquals('ROLLBACK', end($debug));
	}
	

}

