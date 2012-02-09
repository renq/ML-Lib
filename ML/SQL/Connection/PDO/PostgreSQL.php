<?php

namespace ML\SQL;


class Connection_PDO_PostgreSQL extends Connection_PDO {
	
	
	private $serialSequenceCache = array();
	
	
	public function __construct(Settings $settings) {
		parent::__construct($settings);
	}
	
	
	public function connect() {
		if ($this->handle instanceof \PDO) return false;
		try {
			$driver = $this->settings->getDriver();
			$host = $this->settings->getHost();
			$port = ($port = $this->settings->getPort())?$port:5432;
			$username = $this->settings->getUsername();
			$password = $this->settings->getPassword();
			$database = $this->settings->getDatabase();
			
			$this->handle = new \PDO("{$driver}:host={$host};port=$port;dbname={$database}", $username, $password);
			$this->handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->settings->clearPassword();
			return true;
		}
		catch (\PDOException $e) {
			throw new Exception('Can\'t open database: '.$e->getMessage()); 
		}
	}

	
	public function getSerialSequence($table, $idColumn) {
		if (isset($this->serialSequenceCache[$table][$idColumn])) {
			return $this->serialSequenceCache[$table][$idColumn];
		}
		$sth = $this->query("SELECT pg_get_serial_sequence(?, ?) as seq_name", array($table, $idColumn));
		$row = $this->fetch($sth);
		$this->serialSequenceCache[$table][$idColumn] = $row['seq_name'];
		return $row['seq_name'];
	}
	
	
	public function lastInsertId($table = '', $idColumn = '') {
		$this->connect();
		$sequenceName = $this->getSerialSequence($table, $idColumn);
		return $this->handle->lastInsertId($sequenceName);
	}
	
	
} 


