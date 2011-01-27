<?php


/**
 * 
 * Enter description here ...
 * @author Michał Lipek (michal@lipek.net)
 *
 */
abstract class ML_PdoSqlConnection extends ML_SqlConnection {
	
	protected $handle = null;
	
	
	public function __construct($dsn) {
		parent::__construct($dsn);
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
			$sth = $this->handle->prepare($query);
			$sth->execute(array_values($params));
			
			$numberOfPlaceholders = substr_count($query, '?');
			$numberOfParams = count($params);
			if ($numberOfPlaceholders > $numberOfParams) {
				throw new ML_SqlException("Query error: too few bind parameters; should be $numberOfPlaceholders; $numberOfParams given;\n$query");
			}
		}
		catch (PDOException $e) {
			$query = $this->buildSql($query, $params);
			if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
				throw new ML_SqlException('Query error: '.$e->getMessage().";\n$query", (int)$e->getCode(), $e); 
			}
			else {
				throw new ML_SqlException('Query error: '.$e->getMessage().";\n$query", (int)$e->getCode()); 
			}
		}
		return $sth;
	}
	
	
	public function lastInsertId($table = '') {
		$this->connect();
		return $this->handle->lastInsertId();
	}

	
	public function fetch($sth) {
		$this->connect();
		return $sth->fetch(PDO::FETCH_ASSOC);
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


