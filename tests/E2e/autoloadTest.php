<?php

require_once __DIR__ . '/../../ML/loader.php';


class E2eAutoloadTest extends PHPUnit_Framework_TestCase {
	
	
    public function testAutoload() {
        $this->assertTrue(class_exists('ML\E2e\Exception'));
        $this->assertTrue(class_exists('ML\E2e\WarningException'));
        $this->assertTrue(class_exists('ML\E2e\UserWarningException'));
        $this->assertTrue(class_exists('ML\E2e\NoticeException'));
        $this->assertTrue(class_exists('ML\E2e\UserNoticeException'));
        $this->assertTrue(class_exists('ML\E2e\StrictException'));
        $this->assertTrue(class_exists('ML\E2e\DeprecatedException'));
        $this->assertTrue(class_exists('ML\E2e\ErrorHandler'));
    }
    
        
    
}

