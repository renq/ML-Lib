<?php

use ml\e2e\ErrorHandler;
require_once dirname(__FILE__) . '/../../ml/ml.php';


class ErrorHandlerTest extends PHPUnit_Framework_TestCase {
	
	
    public function testEnabledIfInstanceIsRunOrStop() {
    	$handler = ml\e2e\ErrorHandler::getInstance();
    	$this->assertFalse($handler->isEnabled());
    	$handler = ml\e2e\ErrorHandler::run();
    	$this->assertTrue($handler->isEnabled());
    	$handler = ml\e2e\ErrorHandler::stop();
    	$this->assertFalse($handler->isEnabled());
    }
    
    
    public function testThrowNoticeException() {
    	error_reporting(E_ALL);
    	ml\e2e\ErrorHandler::run();
    	$this->setExpectedException('ml\e2e\NoticeException');
    	$array = array('kitty', 'cat');
    	$array[12];
    }
    
    
    public function testThrowUserNoticeException() {
    	error_reporting(E_ALL);
    	ml\e2e\ErrorHandler::stop(); // PHPUnit has own error_handler. This force ours. :)
    	ml\e2e\ErrorHandler::run();
    	$this->setExpectedException('ml\e2e\UserNoticeException');
    	trigger_error("Custom user notice", E_USER_NOTICE);
    }
    
    
    public function testThrowWarningException() {
    	error_reporting(E_ALL);
    	ml\e2e\ErrorHandler::run();
    	$this->setExpectedException('ml\e2e\WarningException');
    	1/0;
    }
    
    
    public function testThrowUserWarningException() {
    	error_reporting(E_ALL);
    	ml\e2e\ErrorHandler::run();
    	$this->setExpectedException('ml\e2e\UserWarningException');
    	trigger_error("Custom user warning", E_USER_WARNING);
    }
    
    
    public function testNotThrowException() {
    	error_reporting(0);
    	$array = array(1);
    	$array[666];
    	1/0;
    	$this->assertTrue(true);
    }

    
    public function testThrowWarningAndNotNoticeException() {
    	$this->setExpectedException('ml\e2e\WarningException');
    	error_reporting(E_WARNING);
    	$array = array(1);
    	$array[666];
    	1/0;
    }
    
	public function testClone() {
		error_reporting(-1);
    	$this->setExpectedException('BadMethodCallException');
    	$instance = ErrorHandler::getInstance();
    	$clone = clone $instance;
    }
    
    
	public function testUnknownException() {
		error_reporting(-1);
    	$this->setExpectedException('ml\e2e\UnknownException');
    	ErrorHandler::handler(666, "Fake error", __FILE__, __LINE__, '');
    }     
   
 
    
    // These errors are triggered in complie time. It's no way to catch them.
    /*
    public function testThrowDeprecatedException() {
    	ml\e2e\ErrorHandler::run();
    	$this->setExpectedException('ml\e2e\DeprecatedException');
    	$obj =& new ArrayObject(array(1,2,3));
    }
    
    
    public function testThrowStrictException() {
    	
    }
    */
    


}
