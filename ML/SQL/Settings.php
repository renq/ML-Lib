<?php

namespace ML\SQL;


class Settings {
	
	
	private $driver = null;
	private $username = null;
	private $password = null;
	private $host = null;
	private $port = null;
	private $database = null;
	
	
	public function __construct($dsn = null) {
		if ($dsn) {
			$matches = array();
			
			$location = parse_url($dsn);
			if ($location) {
				$this->setDriver($location['scheme']);
				$this->setHost(isset($location['host']) ? $location['host'] : null);
				$this->setUsername(isset($location['user']) ? $location['user'] : null);
				$this->setDatabase(isset($location['path']) ? ltrim($location['path'], '/') : null);
				$this->setPort(isset($location['port']) ? $location['port'] : null);
				$this->setPassword(isset($location['pass']) ? urldecode($location['pass']) : null);
			}
			elseif (preg_match('/^(.*):\/\/\/(.*)$/', $dsn, $matches)) {
				list($this->dsn, $this->driver, $this->database) = $matches;
			}
		}
	}
	
	
	public function setDriver($driver) {
		$this->driver = $driver;
	}
	
	
	public function getDriver() {
		return $this->driver;
	}
	
	
	public function setUsername($username) {
		$this->username = $username;
	}
	
	
	public function getUsername() {
		return $this->username;
	}
	
	
	public function getPassword() {
		return $this->password;
	}
	
	
	public function setPassword($password) {
		$this->password = $password;
	}
	
	
	public function getHost() {
		return $this->host;
	}
	
	
	public function setHost($host) {
		$this->host = $host;
	}
	
	
	public function getDatabase() {
		return $this->database;
	}
	
	
	public function setDatabase($database) {
		$this->database = $database;
	}
	
	
	public function getPort() {
		return $this->port;
	}
	
	
	public function setPort($port) {
		$this->port = $port;
	}
	
	
	public function clearPassword() {
		$this->password = 'Hidden for security reasons!';
	}
	
	
}
 
