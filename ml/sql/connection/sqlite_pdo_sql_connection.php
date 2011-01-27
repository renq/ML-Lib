<?php


class ML_SqlitePdoSqlConnection extends ML_PdoSqlConnection {
	
	
	
	public function __construct($dsn) {
		parent::__construct($dsn);
	}
	
	
	public function connect() {
		if ($this->handle instanceof PDO) return false;
		try {
			$driver = $this->settings->getDriver();
			$database = $this->settings->getDatabase();
			
			$this->handle = new PDO("{$driver}:{$database}");
			$this->handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return true;
		}
		catch (PDOException $e) {
			throw new ML_SqlException('Can\'t open database: '.$e->getMessage()); 
		}
	}

	
	
} 


