<?php

require_once dirname(__FILE__) . '/../../ml/ml.php';


class E2eAutoloadTest extends PHPUnit_Framework_TestCase {
	
	
    public function testAutoload() {
        $this->assertTrue(class_exists('ml\e2e\Exception'));
        $this->assertTrue(class_exists('ml\e2e\WarningException'));
        $this->assertTrue(class_exists('ml\e2e\UserWarningException'));
        $this->assertTrue(class_exists('ml\e2e\NoticeException'));
        $this->assertTrue(class_exists('ml\e2e\UserNoticeException'));
        $this->assertTrue(class_exists('ml\e2e\StrictException'));
        $this->assertTrue(class_exists('ml\e2e\DeprecatedException'));
        $this->assertTrue(class_exists('ml\e2e\ErrorHandler'));
    }
    
        
    
}

