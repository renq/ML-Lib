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
	private $lastStatement = null;
	
	
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
	
	
	public function escape($value) {
		$this->connect();
		if (is_null($value)) {
			return 'NULL';
		}
		elseif ($value == '') {
			return "''";
		}
		elseif ($value{0} != '0' && (is_int($value) || is_float($value))) {
			return (string)$value;
		}
		elseif (is_bool($value)) {
			return (string)((int)$value);
		}
		else {
			return $this->handle->quote($value);
		}
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
		$this->lastStatement = $sth;
		return $sth;
	}
	
	
	public function getAffectedRows() {
		if ($this->lastStatement instanceof \PDOStatement) {
			return $this->lastStatement->rowCount();
		}
		else {
			throw new SqlException("No query was executed, so method getRowsAffected is pointless in this moment.");
		}
	}
	
	
	public function lastInsertId($table = '', $idColumn = '') {
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


