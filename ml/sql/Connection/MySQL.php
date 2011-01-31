<?php

namespace ml\sql;


class Connection_MySQL extends Connection {
	
	protected $handle = null;
	
	
	public function __construct($dsn) {
		parent::__construct($dsn);
	}
	
	
	static public function useCurrent($handle) {
		$new = null;
		$new = new Connection_Mysql('mysql:///');
		$new->handle = $handle;
		return $new;
	}
	
	
	public function connect() {
		if (is_resource($this->handle)) return false;
		
		$host = $this->settings->getHost();
		$port = $this->settings->getPort();
		$username = $this->settings->getUsername();
		$password = $this->settings->getPassword();
		$database = $this->settings->getDatabase();
		
		$this->handle = mysql_connect($host, $username, $password);
		if (!$this->handle) {
			throw new Exception('Can\'t open database: '.mysql_error());
		}			
		mysql_query("USE {$database}", $this->handle);
		mysql_query("SET character_set_client=utf8", $this->handle);
		mysql_query("SET character_set_results=utf8", $this->handle);
		mysql_query("SET collation_connection=utf8_bin", $this->handle);
		mysql_query("SET SESSION query_cache_type = ON", $this->handle);
		$this->settings->clearPassword();
	}
	
	
	public function getHandle() {
		return $this->handle;
	}
	
	
	public function disconnect() {
		mysql_close($this->handle);
		$this->handle = null;
	}
	
	
	public function escape($string) {
		$this->connect();
		return mysql_real_escape_string($string, $this->handle);
	}
	
	
	public static function escapeParams($params) {
		$result = array();
		foreach ($params as $key => $value) {
			if (is_null($value)) {
				$result[$key] = 'NULL';
			}
			elseif (is_int($value) || is_float($value)) {
				$result[$key] = (string)$value;
			}
			elseif (is_bool($value)) {
				$result[$key] = (int)$value;
			}
			else {
				$result[$key] = str_replace('\"', '"', "'".mysql_real_escape_string($value)."'");
			}
		}
		return $result;
	}
	
	
	public function query($query, array $params = array()) {
		parent::query($query, $params);
		$this->connect();
				
		$n = substr_count($query, '?');
		$k = count($params);
		if ($n != $k) {
			throw new Exception("Query error: to few bind parameters; should be $n; $k given;\n$query");
		}
		$query = str_replace(array('%', '?'), array('%%', '%s'), $query, $count);
		$params = $this->escapeParams($params);
		$query = vsprintf($query, $params);
		
		$result = mysql_query($query, $this->handle);
		if (mysql_errno()) {
			throw new Exception("Query error: $query\n\n" . mysql_errno($this->handle) . ": " . mysql_error($this->handle));
		}
		return $result;
	}
	
	
	public function lastInsertId($table = '') {
		$this->connect();
		return mysql_insert_id($this->handle);
	}

	
	public function fetch($result) {
		return mysql_fetch_assoc($result);
	}
	
	
	public function beginTransaction() {
		$this->connect();
		$this->query("START TRANSACTION");
	}
	
	
	public function commit() {
		$this->connect();
		$this->query("COMMIT");
	}
	
	
	public function rollback() {
		$this->connect();
		$this->query("ROLLBACK");
	}

	
	
} 


