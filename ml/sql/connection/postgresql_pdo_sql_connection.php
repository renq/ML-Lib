<?php


class ML_PostgresqlPdoSqlConnection extends ML_PdoSqlConnection {
	
	
	
	public function __construct($dsn) {
		parent::__construct($dsn);
	}
	
	
	public function connect() {
		if ($this->handle instanceof PDO) return false;
		try {
			$driver = $this->settings->getDriver();
			$host = $this->settings->getHost();
			$port = ($port = $this->settings->getPort())?$port:5432;
			$username = $this->settings->getUsername();
			$password = $this->settings->getPassword();
			$database = $this->settings->getDatabase();
			
			$this->handle = new PDO("{$driver}:host={$host};port=$port;dbname={$database}", $username, $password);
			$this->handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->settings->clearPassword();
			return true;
		}
		catch (PDOException $e) {
			throw new ML_SqlException('Can\'t open database: '.$e->getMessage()); 
		}
	}
	
	
} 


