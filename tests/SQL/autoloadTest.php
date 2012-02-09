<?php

require_once __DIR__ . '/../../ML/loader.php';


class SqlAutoloadTest extends PHPUnit_Framework_TestCase {
	
	
    public function testAutoload() {
        $this->assertTrue(class_exists('ML\SQL\BaseConnection'));
        $this->assertTrue(class_exists('ML\SQL\Exception'));
        $this->assertTrue(class_exists('ML\SQL\Settings'));
        $this->assertTrue(class_exists('ML\SQL\SQL'));
        $this->assertTrue(class_exists('ML\SQL\Strategy'));
        $this->assertTrue(class_exists('ML\SQL\Connection_MySQL'));
        $this->assertTrue(class_exists('ML\SQL\Connection_PDO'));
        $this->assertTrue(class_exists('ML\SQL\Connection_PDO_MySQL'));
        $this->assertTrue(class_exists('ML\SQL\Connection_PDO_PostgreSQL'));
        $this->assertTrue(class_exists('ML\SQL\Connection_PDO_Sqlite'));
        $this->assertTrue(class_exists('ML\SQL\Strategy_MySQL'));
        $this->assertTrue(class_exists('ML\SQL\Strategy_PostgreSQL'));
        $this->assertTrue(class_exists('ML\SQL\Strategy_Sqlite'));
    }
    
        
    
}

