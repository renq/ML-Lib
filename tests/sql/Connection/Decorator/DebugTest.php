<?php


use ml\sql\Connection_Decorator_Debug;
use ml\sql\Connection_Decorator;
use ml\sql\Settings;


require_once dirname(__FILE__) . '../../../../../ml/ml.php';


class SqlConnectionDecoratorDebugTest extends PHPUnit_Framework_TestCase {

	
	public function testDebug() {
		$settings = new Settings();
		$connection = $this->getMock('ml\sql\Connection_PDO_Sqlite', array(), array($settings));
		$decorator = new Connection_Decorator_Debug($connection);
		$connection->expects($this->any())->method('fetch')->will($this->returnValue(15));
		
		$this->assertEquals(array(), $decorator->getDebug());
		$decorator->query("SOME QUERY");
		$this->assertEquals(array("SOME QUERY"), $decorator->getDebug());
		$decorator->query("NEXT QUERY ?", array(15));
		$this->assertEquals(array("SOME QUERY", "NEXT QUERY 15"), $decorator->getDebug());
	}
	

}

