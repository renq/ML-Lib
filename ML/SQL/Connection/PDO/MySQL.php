<?php

namespace ML\SQL;


class Connection_PDO_MySQL extends Connection_PDO {
	
	
	
	public function __construct(Settings $settings) {
		parent::__construct($settings);
	}
	
	
	public function connect() {
		if ($this->handle instanceof \PDO) return false;
		try {
			$driver = $this->settings->getDriver();
			$host = $this->settings->getHost();
			$port = ($port = $this->settings->getPort())?$port:3306;
			$username = $this->settings->getUsername();
			$password = $this->settings->getPassword();
			$database = $this->settings->getDatabase();
			
			$this->handle = new \PDO("{$driver}:host={$host};port=$port;dbname={$database}", $username, $password);
			$this->handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->handle->query("SET character_set_client=utf8");
			$this->handle->query("SET character_set_results=utf8");
			$this->handle->query("SET collation_connection=utf8_bin");
			$this->handle->query("SET SESSION query_cache_type = ON");
			$this->settings->clearPassword();
			return true;
		}
		catch (\PDOException $e) {
			throw new Exception('Can\'t open database: '.$e->getMessage()); 
		}
	}
	
	
} 

