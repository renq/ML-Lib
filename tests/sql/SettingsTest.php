<?php

use ml\sql\Settings;

require_once dirname(__FILE__) . '/../../ml/ml.php';


class SettingsTest extends PHPUnit_Framework_TestCase {
	
	
	/*
    public function testDsn() {
    	$dsnList = array(
    			'mysql://root:myFancyPassw0rd@localhost/dbname',
	    	'mysql://localhost/dbname',
	    	'sqlite:///dbname?mode=0666',
    		'sqlite:///dbname/foo/bar/baz.db',
	    	'mysql://root:myFancyPassw0rd@localhost:33306/dbname',
	    	'mysql://:withoutLogin@localhost/dbname',
	    	'mysql://:withoutLogin@some.host:2222/dbname',
	    	'pgsql://pglogin:pa%40&*(%^%3a@host/schema',
	    		'pgsql://pglogin:p%40a&*(%^%3a@host:32311/schema',
    	);
    	
    	foreach ($dsnList as $dsn) {
    		$settings = new Settings($dsn);
    	}
    }    */

    
    public function testProtoUserPassHostPortName() {
    	$connections = array(
    		array('mysql', 'root', 'pass', 'localhost', '3306', 'dbname'),
    		array('mysql', 'user', '&*3jxsS', 'my-mysql.com.pl', '3307', 'database_name'),
    	);
    	foreach ($connections as $connection) {
    		list($driver, $user, $password, $host, $port, $dbname) = $connection;
    		$dsn = "$driver://$user:$password@$host:$port/$dbname";
    		$settings = new Settings($dsn);
    		
    		$this->assertEquals($settings->getDriver(), $driver);
    		$this->assertEquals($settings->getUsername(), $user);
    		$this->assertEquals($settings->getPassword(), $password);
    		$this->assertEquals($settings->getHost(), $host);
    		$this->assertEquals($settings->getPort(), $port);
    		$this->assertEquals($settings->getDatabase(), $dbname);
    	}
    }
    
    
    public function testProtoUserPassHostName() {
    	$connections = array(
    		array('mysql', 'root', 'pass', 'localhost', 'dbname'),
    		array('mysql', 'user', '&*3jxsS', 'my-mysql.com.pl', 'database_name'),
    		array('mysql', 'user', '', '127.0.0.1', 'database_name'),
    	);
    	foreach ($connections as $connection) {
    		list($driver, $user, $password, $host, $dbname) = $connection;
    		$dsn = "$driver://$user:$password@$host/$dbname";
    		$settings = new Settings($dsn);
    		
    		$this->assertEquals($settings->getDriver(), $driver);
    		$this->assertEquals($settings->getUsername(), $user);
    		$this->assertEquals($settings->getPassword(), $password);
    		$this->assertEquals($settings->getHost(), $host);
    		$this->assertEquals($settings->getDatabase(), $dbname);
    	}
    }
    
    
    public function testProtoUserHostName() {
    	$connections = array(
    		array('mysql', 'root', 'localhost', 'dbname'),
    		array('mysql', 'user', 'my-mysql.com.pl', 'database_name'),
    		array('mysql', 'user', '127.0.0.1', 'database_name'),
    	);
    	foreach ($connections as $connection) {
    		list($driver, $user, $host, $dbname) = $connection;
    		$dsn = "$driver://$user@$host/$dbname";
    		$settings = new Settings($dsn);
    		
    		$this->assertEquals($settings->getDriver(), $driver);
    		$this->assertEquals($settings->getUsername(), $user);
    		$this->assertEquals($settings->getHost(), $host);
    		$this->assertEquals($settings->getDatabase(), $dbname);
    	}
    }
    
    
    public function testProtoHostName() {
    	$connections = array(
    		array('mysql', 'localhost', 'dbname'),
    		array('mysql', 'my-mysql.com.pl', 'database_name'),
    		array('mysql', '127.0.0.1', 'database_name'),
    	);
    	foreach ($connections as $connection) {
    		list($driver, $host, $dbname) = $connection;
    		$dsn = "$driver://$host/$dbname";
    		$settings = new Settings($dsn);
    		
    		$this->assertEquals($settings->getDriver(), $driver);
    		$this->assertEquals($settings->getHost(), $host);
    		$this->assertEquals($settings->getDatabase(), $dbname);
    	}
    }
    
    
    public function testProtoHost() {
    	$connections = array(
    		array('mysql', '127.0.0.2'),
    		array('pgsql', 'localhost'),
    	);
    	foreach ($connections as $connection) {
    		list($driver, $host) = $connection;
    		$dsn = "$driver://$host";
    		$settings = new Settings($dsn);
    		
    		$this->assertEquals($settings->getDriver(), $driver, "Parse: $dsn");
    		$this->assertEquals($settings->getHost(), $host, "Parse: $dsn");
    	}
    }
    
    
    public function testProtoDatabase() {
    	$connections = array(
    		array('mysql', 'dbname'),
    		array('sqlite', 'database_name'),
    		array('sqlite', '/home/www/db/my_site.db'),
    		array('sqlite', '/home/www/db/my_site.db?mode=666'),
    	);
    	foreach ($connections as $connection) {
    		list($driver, $dbname) = $connection;
    		$dsn = "$driver:///$dbname";
    		$settings = new Settings($dsn);
    		
    		$this->assertEquals($settings->getDriver(), $driver, "Parse: $dsn");
    		$this->assertEquals($settings->getDatabase(), $dbname, "Parse: $dsn");
    	}
    }
    
    
    public function testEscapeSpecialChars() {
    	$password = 'mx@""::#$:%^&*()';
    	$encodedPassword = urlencode($password);
    	$dsn = "mysql://root:$encodedPassword@localhost/sqldb";
    	$settings = new Settings($dsn);
    	
    	$this->assertEquals($settings->getPassword(), $password);
    }


}
