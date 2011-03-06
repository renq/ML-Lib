<?php

namespace ml\sql;


class Connection_MySQL extends BaseConnection {
	
	private $lastQuery = null;
	
	protected $handle = null;
	
	
	public function __construct(Settings $settings) {
		parent::__construct($settings);
	}
	
	
	static public function useCurrent($handle) {
		$new = new Connection_MySQL(new Settings());
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
		
		$this->handle = @mysql_connect($host, $username, $password);
		if (!$this->handle) {
			throw new SqlException('Can\'t open database: '.mysql_error());
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
			return str_replace('\"', '"', "'".mysql_real_escape_string($value)."'");
		}		
	}
	
	
	private function escapeParams($params) {
		$result = array();
		foreach ($params as $key => $value) {
			$result[$key] = $this->escape($value);
		}
		return $result;
	}
	
	
	public function query($query, array $params = array()) {
		$this->connect();
				
		$n = substr_count($query, '?');
		$k = count($params);
		if ($n != $k) {
			throw new BindException("Query error: wrong number of bind parameters; should be $n; $k given;\n$query");
		}
		$query = str_replace(array('%', '?'), array('%%', '%s'), $query, $count);
		$params = $this->escapeParams($params);
		$query = vsprintf($query, $params);
		
		$result = mysql_query($query, $this->handle);
		if (mysql_errno()) {
			throw new SqlException("Query error: $query\n\n" . mysql_errno($this->handle) . ": " . mysql_error($this->handle));
		}
		$this->lastQuery = $query;
		return $result;
	}
	
	
	public function getAffectedRows() {
		if ($this->lastQuery == null) {
			throw new SqlException("No query was executed, so method getRowsAffected is pointless in this moment.");
		}
		return mysql_affected_rows($this->handle);
	}
	
	
	public function lastInsertId($table = '', $idColumn = '') {
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


