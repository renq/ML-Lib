<?php

namespace ML\SQL;


class Connection_Decorator implements Connection {
	
	
	private $decorated;

	
	public function __construct(Connection $connection) {
		$this->decorated = $connection;
	}
	
	
	public function connect() {
		return $this->decorated->connect();
	}
	
	
	public function disconnect() {
		return $this->decorated->disconnect();
	}
	
	
	public function fetch($queryResult) {
		return $this->decorated->fetch($queryResult);
	}
	
	
	public function beginTransaction() {
		return $this->decorated->beginTransaction();
	}
	
	
	public function commit() {
		return $this->decorated->commit();
	}
	
	

	public function rollback() {
		return $this->decorated->rollback();
	}
	
	
	public function lastInsertId($table = '', $idColumn = '') {
		return $this->decorated->lastInsertId($table, $idColumn);
	}
	

	public function getHandle() {
		return $this->decorated->getHandle();
	}
	
	
	public function setHandle($handle) {
		$this->decorated->setHandle($handle);
	}
	
	
	public function escape($variable) {
		return $this->decorated->escape($variable);
	}
	
	
	public function query($query, array $params = array()) {
		return $this->decorated->query($query, $params);
	}
	

	public function getAffectedRows() {
		return $this->decorated->getAffectedRows();
	}
	
	
	public function getSettings() {
		return $this->decorated->getSettings();
	}
	
	
	public function setSettings(Settings $settings) {
	    $this->decorated->setSettings($settings);
	}
	
	
}
