<?php

require_once __DIR__ . '/../../ML/loader.php';


class FormsAutoloadTest extends PHPUnit_Framework_TestCase {
	
	
    public function testAutoload() {
        $this->assertTrue(class_exists('ML\Forms\Element\Base'));
        $this->assertTrue(class_exists('ML\Forms\Element\Element'));
        $this->assertTrue(class_exists('ML\Forms\Element\Form'));
        $this->assertTrue(class_exists('ML\Forms\Element\Textarea'));
        $this->assertTrue(class_exists('ML\Forms\Element\Text'));
        $this->assertTrue(class_exists('ML\Forms\Element\Select'));
    }
    
        
    
}

