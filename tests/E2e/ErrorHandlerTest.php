<?php

use ML\E2e\ErrorHandler;

require_once __DIR__ . '/../../ML/loader.php';


class ErrorHandlerTest extends PHPUnit_Framework_TestCase {
	
	
    public function testEnabledIfInstanceIsRunOrStop() {
    	$handler = ML\E2e\ErrorHandler::getInstance();
    	$this->assertFalse($handler->isEnabled());
    	$handler = ML\E2e\ErrorHandler::run();
    	$this->assertTrue($handler->isEnabled());
    	$handler = ML\E2e\ErrorHandler::stop();
    	$this->assertFalse($handler->isEnabled());
    }
    
    
    public function testThrowNoticeException() {
    	error_reporting(E_ALL);
    	ML\E2e\ErrorHandler::run();
    	$this->setExpectedException('ML\E2e\NoticeException');
    	$array = array('kitty', 'cat');
    	$array[12];
    }
    
    
    public function testThrowUserNoticeException() {
    	error_reporting(E_ALL);
    	ML\E2e\ErrorHandler::stop(); // PHPUnit has own error_handler. This force ours. :)
    	ML\E2e\ErrorHandler::run();
    	$this->setExpectedException('ML\E2e\UserNoticeException');
    	trigger_error("Custom user notice", E_USER_NOTICE);
    }
    
    
    public function testThrowWarningException() {
    	error_reporting(E_ALL);
    	ML\E2e\ErrorHandler::run();
    	$this->setExpectedException('ML\E2e\WarningException');
    	1/0;
    }
    
    
    public function testThrowUserWarningException() {
    	error_reporting(E_ALL);
    	ML\E2e\ErrorHandler::run();
    	$this->setExpectedException('ML\E2e\UserWarningException');
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
    	$this->setExpectedException('ML\E2e\WarningException');
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
    
    
	public function testRecoverableErrorException() {
		$this->setExpectedException('ML\E2e\RecoverableErrorException');
		$function = function(ArrayObject $a) {};
    	$function(new stdClass());
    }
    
    
	public function testUnknownException() {
		error_reporting(-1);
    	$this->setExpectedException('ML\E2e\UnknownException');
    	ErrorHandler::handler(666, "Fake error", __FILE__, __LINE__, '');
    }
    
    
    public function testThrowDeprecatedException() {
    	error_reporting(-1);
    	ML\E2e\ErrorHandler::run();
    	$this->setExpectedException('ML\E2e\DeprecatedException');
    	eregi("^[a-z0-9_-]+[a-z0-9_.-]*@[a-z0-9_-]+[a-z0-9_.-]*\.[a-z]{2,5}$", 'example@example.com');
    }
   
 
    /**
     * Strict errors are triggered in compile time, so we just included class
     * with strict errors later than ErrorHandler...
     */
    public function testThrowStrictException() {
    	error_reporting(-1);
    	ML\E2e\ErrorHandler::run();
    	$this->setExpectedException('ML\E2e\StrictException');
    	include(__DIR__ . '/strict.php');
    }


}
