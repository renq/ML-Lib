<?php

namespace ML\SQL;


class Connection_PDO_Sqlite extends Connection_PDO {
	
	
	
	public function __construct(Settings $settings) {
		parent::__construct($settings);
	}
	
	
	public function connect() {
		if ($this->handle instanceof \PDO) return false;
		try {
			$driver = $this->settings->getDriver();
			$database = $this->settings->getDatabase();
			
			$this->handle = new \PDO("{$driver}:{$database}");
			$this->handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			return true;
		}
		catch (\PDOException $e) {
			throw new Exception('Can\'t open database: '.$e->getMessage()); 
		}
	}

	
	
} 


