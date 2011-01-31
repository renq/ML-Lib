<?php

namespace ml\sql;


/**
 * 
 * @author Michał Lipek
 * @TODO
 * Działają tylko zapytania SELECT, i to też nie do końca wiadomo, czy wszystkie
 * 
 *
 */
class Connection_MSSQL extends Connection {
	
	protected $handle = null;
	
	
	public function __construct($dsn) {
		parent::__construct($dsn);
	}
	
	
	static public function useCurrent($handle) {
		$new = null;
		$new = new Connection_Mssql('mssql:///');
		$new->handle = $handle;
		return $new;
	}
	
	
	public function connect() {
		if (is_resource($this->handle)) return false;

		$host = $this->settings->getHost();
		$username = $this->settings->getUsername();
		$password = $this->settings->getPassword();
		$database = $this->settings->getDatabase();
		
		$this->handle = mssql_connect($host, $username, $password);
		if (!$this->handle) {
			$msg = error_get_last();
			throw new Exception('Can\'t open database: '.$msg['message']);
		}
		mssql_select_db($database, $this->handle);
		mssql_query("SET ANSI_NULLS on;");
		mssql_query("SET ANSI_WARNINGS on;");
		$this->settings->clearPassword();
	}
	
	
	public function getHandle() {
		return $this->handle;
	}
	
	
	public function disconnect() {
		mssql_close($this->handle);
		$this->handle = null;
	}
	
	
	public function escape($string) {
		return $this->ms_escape_string($string);
	}
	
	
	/**
	 * From Code Ingniter
	 * @param string $data
	 */
	private function ms_escape_string($data) {
		if (!isset($data) or empty($data)) return '';
		if (is_numeric($data)) return $data;
	
		$non_displayables = array(
			'/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
			'/%1[0-9a-f]/',             // url encoded 16-31
			'/[\x00-\x08]/',            // 00-08
			'/\x0b/',                   // 11
			'/\x0c/',                   // 12
			'/[\x0e-\x1f]/'             // 14-31
		);
		foreach ( $non_displayables as $regex ) {
			$data = preg_replace( $regex, '', $data );
		}
		$data = str_replace("'", "''", $data );
		return $data;
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
		
		$result = mssql_query($query, $this->handle);
		if (!is_resource($result)) {
			$msg = error_get_last();
			throw new Exception("Query error: $query\n\n" . $msg);
		}
		return $result;
	}
	
	
	private function escapeParams($params) {
		$result = array();
		foreach ($params as $key => $value) {
			if (is_null($value)) {
				$result[$key] = 'NULL';
			}
			else {
				$result[$key] = str_replace('\"', '"', "'".$this->escape($value)."'");
			}
		}
		return $result;
	}
	
	
	public function lastInsertId($table = '') {
		$this->connect();
		$query = mssql_query("SEELCT scope_identity()");
		$tmp = mssql_fetch_row($query);
		return array_shift($tmp);
	}

	
	public function fetch($result) {
		return mssql_fetch_assoc($result);
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


?>