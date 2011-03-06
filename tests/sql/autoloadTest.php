<?php

require_once __DIR__ . '/../../ml/ml.php';


class SqlAutoloadTest extends PHPUnit_Framework_TestCase {
	
	
    public function testAutoload() {
        $this->assertTrue(class_exists('ml\sql\BaseConnection'));
        $this->assertTrue(class_exists('ml\sql\Exception'));
        $this->assertTrue(class_exists('ml\sql\Settings'));
        $this->assertTrue(class_exists('ml\sql\SQL'));
        $this->assertTrue(class_exists('ml\sql\Strategy'));
        $this->assertTrue(class_exists('ml\sql\Connection_MySQL'));
        $this->assertTrue(class_exists('ml\sql\Connection_PDO'));
        $this->assertTrue(class_exists('ml\sql\Connection_PDO_MySQL'));
        $this->assertTrue(class_exists('ml\sql\Connection_PDO_PostgreSQL'));
        $this->assertTrue(class_exists('ml\sql\Connection_PDO_Sqlite'));
        $this->assertTrue(class_exists('ml\sql\Strategy_MySQL'));
        $this->assertTrue(class_exists('ml\sql\Strategy_PostgreSQL'));
        $this->assertTrue(class_exists('ml\sql\Strategy_Sqlite'));
    }
    
        
    
}

