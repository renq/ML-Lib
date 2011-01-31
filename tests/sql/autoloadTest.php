<?php

require_once dirname(__FILE__) . '/../../ml/ml.php';


class SqlAutoloadTest extends PHPUnit_Framework_TestCase {
	
	
    public function testAutoload() {
        $this->assertTrue(class_exists('ml\sql\Connection'));
        $this->assertTrue(class_exists('ml\sql\Exception'));
        $this->assertTrue(class_exists('ml\sql\Settings'));
        $this->assertTrue(class_exists('ml\sql\SQL'));
        $this->assertTrue(class_exists('ml\sql\Strategy'));
        $this->assertTrue(class_exists('ml\sql\Connection_MSSQL'));
        $this->assertTrue(class_exists('ml\sql\Connection_MySQL'));
        $this->assertTrue(class_exists('ml\sql\Connection_PDO'));
        $this->assertTrue(class_exists('ml\sql\Connection_PDO_MySQL'));
        $this->assertTrue(class_exists('ml\sql\Connection_PDO_PostgreSQL'));
        $this->assertTrue(class_exists('ml\sql\Connection_PDO_Sqlite'));
        $this->assertTrue(class_exists('ml\sql\Strategy_MSSQL'));
        $this->assertTrue(class_exists('ml\sql\Strategy_MySQL'));
        $this->assertTrue(class_exists('ml\sql\Strategy_MySQLLike'));
        $this->assertTrue(class_exists('ml\sql\Strategy_PostgreSQL'));
        $this->assertTrue(class_exists('ml\sql\Strategy_Sqlite'));
    }
    
        
    
}

