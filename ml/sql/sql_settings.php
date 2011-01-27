<?php


class ML_SqlSettings {
	
	
	private $driver = null;
	private $username = null;
	private $password = null;
	private $host = null;
	private $port = null;
	private $database = null;
	
	
	public function __construct($dsn) {
		$matches = array();
		if (preg_match('/^([^:]+):\/\/([^:]+):(.*)\@([^\/]+)\/([^:]+)$/', $dsn, $matches)) {
			list($dsn, $this->driver, $this->username, $this->password, $this->host, $this->database) = $matches;
		}
		elseif (preg_match('/^(.*):\/\/\/(.*)$/', $dsn, $matches)) {
			list($this->dsn, $this->driver, $this->database) = $matches;
		}
		else {
			throw new ML_SqlException("Unknown DSN ($dsn)");
		}
	}
	
	
	public function getDriver() {
		return $this->driver;
	}
	
	
	public function getUsername() {
		return $this->username;
	}
	
	
	public function getPassword() {
		return $this->password;
	}
	
	
	public function getHost() {
		return $this->host;
	}
	
	
	public function getDatabase() {
		return $this->database;
	}
	
	
	public function getPort() {
		return $this->port;
	}
	
	
	public function clearPassword() {
		$this->password = 'Hidden for security reasons!';
	}
	
	
}

