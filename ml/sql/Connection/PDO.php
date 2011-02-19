<?php

namespace ml\sql;


/**
 * 
 * Enter description here ...
 * @author Michał Lipek (michal@lipek.net)
 *
 */
abstract class Connection_PDO extends Connection {
	
	protected $handle = null;
	
	
	public function __construct(Settings $settings) {
		parent::__construct($settings);
	}
	
	
	public function disconnect() {
		unset($this->handle);
		$this->handle = null;
	}
	
	
	public function getHandle() {
		return $this->handle;
	}
	
	
	public function escape($string) {
		$this->connect();
		return $this->handle->quote($string);
	}
	
	
	public function query($query, array $params = array()) {
		parent::query($query, $params);
		$this->connect();
		try {			
			$numberOfPlaceholders = substr_count($query, '?');
			$numberOfParams = count($params);
			if ($numberOfPlaceholders != $numberOfParams) {
				throw new BindException("Query error: wrong number of bind parameters; should be $numberOfPlaceholders; $numberOfParams given;\n$query");
			}
			$sth = $this->handle->prepare($query);
			$sth->execute(array_values($params));
		}
		catch (\PDOException $e) {
			$query = $this->buildSql($query, $params);
			throw new SqlException('Query error: '.$e->getMessage().";\n$query", (int)$e->getCode(), $e);
		}
		return $sth;
	}
	
	
	public function lastInsertId($table = '') {
		$this->connect();
		return $this->handle->lastInsertId();
	}

	
	public function fetch($sth) {
		$this->connect();
		return $sth->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	public function beginTransaction() {
		$this->connect();
		$this->handle->beginTransaction();
	}
	
	
	public function commit() {
		$this->connect();
		$this->handle->commit();
	}
	
	
	public function rollback() {
		$this->connect();
		$this->handle->rollback();
	}
	
	
} 


