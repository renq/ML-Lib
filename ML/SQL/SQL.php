<?php

namespace ML\SQL;


/**
 *  class.
 * @author Michał Lipek (michal@lipek.net)
 * @version 2.0 2010-11-31
 */
class SQL {


	
	/**
	 * @var Connection
	 */
	protected $connection;
	
	/**
	 * @var SqlStrategy
	 */
	protected $strategy;

	
	/**
	 * The constructor.
	 * @param Connection $connection
	 * @param Strategy $strategy
	 */
	public function __construct(Connection $connection, Strategy $strategy) {
		$this->connection = $connection;
		$this->strategy = $strategy;
	}
	
	
	/**
	 * Create SQL object by DSN.
 	 * @param $dsn string 
 	 * @return SQL
	 */
	public static function createByDSN($dsn) {
		$settings = new Settings($dsn);
		switch ($settings->getDriver()) {
			case 'mysql':
				$connection = new Connection_PDO_MySQL($settings);
				$strategy = new Strategy_MySQL($connection);
				return new SQL($connection, $strategy);
			case 'pgsql':
				$connection = new Connection_PDO_PostgreSQL($settings);
				$strategy = new Strategy_PostgreSQL($connection);
				return new SQL($connection, $strategy);
			case 'sqlite':
				$connection = new Connection_PDO_Sqlite($settings);
				$strategy = new Strategy_Sqlite($connection);
				return new SQL($connection, $strategy);
			default:
				throw new Exception("Don't know what to do with DSN '$dsn'. Undefined driver '" . $settings->getDriver() . "'.");
		}
	}
	
	//--- returns data function 
	
	/**
	 * Returns rows from database.
	 * @param string $query
	 * @param array $params
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function get($query, array $params = array(), $limit = false, $offset = 0) {
		if ($limit) {
			$query = $this->strategy->limit($query, $limit, $offset);
		}
		$return = array();
		$result = $this->connection->query($query, $params);
		if ($result) {
			while ($row = $this->connection->fetch($result)) {
				$return[] = $row;
			}
		}
		return $return;
	}
	
	/**
	 * Return one row.
	 * @param string $query
	 * @param array $params
	 * @return array
	 */
	public function one($query, array $params = array()) {
		$tmp = $this->get($this->strategy->one($query), $params);
		return empty($tmp)?false:$tmp[0];
	}
	
	/**
	 * Return array of values from one (first) column.
	 * @param string $query
	 * @param array $params
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function flat($query, array $params = array(), $limit = false, $offset = 0) {
		$result = $this->get($query, $params, $limit, $offset);
		foreach ($result as &$row) {
			$row = array_shift($row);
		}
		return $result;
	}
	
	/**
	 * Returns row by id.
	 * @param string $table
	 * @param int $id
	 * @param string $idColumn
	 * @return array
	 */
	public function byId($table, $id, $idColumn = 'id') {
		$query = $this->strategy->byId($table, $idColumn);
		return $this->one($query, array($id));
	}
	
	/**
	 * Returns value in first column and first row.
	 * @param string $query
	 * @param array $params
	 * @return string
	 */
	public function value($query, array $params = array()) {
		$tmp = $this->one($query, $params);
		if (!empty($tmp) && is_array($tmp)) {
			return array_shift($tmp);
		} 
		else {
			return false;
		}
	}
	
		
	//--- transakcje
	
	/**
	 * Begin transaction.
	 */
	public function beginTransaction() {
		$this->connection->beginTransaction();
	}
	
	/**
	 * Commit transaction.
	 */
	public function commit() {
		$this->connection->commit();
	}
	
	/**
	 * Rollback transaction.
	 */
	public function rollback() {
		$this->connection->rollback();
	}
	
	//--- modify
	
	/**
	 * Save row to table "$table". $params is a map of values. Key is a table field name. Value is a value.
	 * When id = 0 row is inserted. When id <> 0 then row is updated.
	 * Primary key should'n be inside $params array.
	 * @param $table
	 * @param array $params
	 * @param int $id
	 * @param string $idColumn
	 * @return int
	 */
	public function save($table, array $params, $id = 0, $idColumn = 'id') {
		if (!is_string($table) || !strlen($table)) {
			throw new \InvalidArgumentException("Table name must be a string with length greather than 1.");
		}
		// if array is assoc
		if (!is_array($params) || 0 === count(array_diff_key($params, array_keys(array_keys($params))))) {
			throw new \InvalidArgumentException('Second parameter must be an associative array!');
		}
		$query = '';
		if ($id) {
			$query = $this->strategy->update($table, $params, $idColumn);
			$params[] = $id;
			$this->connection->query($query, $params);
			return $id;
		}
		else {
			$query = $this->strategy->insert($table, $params);
			$this->connection->query($query, $params);
			return $this->connection->lastInsertId($table, $idColumn);
		}
	}
	
	
	public function saveFromArray($table, $array, $idColumn = 'id') {
		$params = array_intersect_key($array, $this->describe($table));
		$id = 0;
		if (isset($params[$idColumn])) {
			$id = $params[$idColumn];
			unset($params[$idColumn]);
		}
		return $this->save($table, $params, $id, $idColumn);
	}
	
	
	/**
	 * @deprecated
	 * @param string $table
	 * @param array $extraParams
	 * @param string|int $idColumn
	 */
	public function saveFromRequest($table, $extraParams = array(), $idColumn = 'id') {
		return $this->saveFromArray($table, array_merge($_REQUEST, $extraParams), $idColumn);
	}
	
	/**
	 * Delete row(s) from table by id. 
	 * @param string $table
	 * @param int $id
	 * @param string $idColumn
	 */
	public function delete($table, $id, $idColumn = 'id') {
		$query = $this->strategy->delete($table, $idColumn);
		$this->connection->query($query, array($id));
	}
	
	
	//--- other
	
	/**
	 * Execute query. 
	 * @param string $query
	 * @param array $params
	 * @return mixed
	 */
	public function query($query, array $params = array()) {
		return $this->connection->query($query, $params);
	}
	
	
	/**
	 * Returns describe table information.
	 * @TODO
	 * @param string $table
	 */
	public function describe($table) {
		$return = array();
		$tmp = $this->get($this->strategy->describe(), array($this->getConnection()->getSettings()->getDatabase(), $table));
		if ($tmp) {
			$result = array();
			foreach ($tmp as $item) {
				$newItem = array();
				foreach ($item as $k => $v) {
					$newItem[strtolower($k)] = $v;
				}
				$item = $newItem;
				$result[$item['column_name']] = $newItem;
			}
			return $result;
		}
		throw new Exception("Describe fail. Probably table '$table' doesn't exists.");
	}
	

	/**
	 * @deprecated
	 * @param unknown_type $num
	 */
	public function qm($num) {
		return $this->getStrategy()->qm($num);
	}
	
	
	/**
	 * Returns connection object.
	 * @return Connection
	 */
	public function getConnection() {
		return $this->connection;
	}
	
	
	/**
	* Sets connection object.
	* @param Connection $connection
	*/
	public function setConnection(Connection $connection) {
	    $this->connection = $connection;
	}
	
	
	/**
	 * Returns strategy object.
	 * @return Strategy
	 */
	public function getStrategy() {
		return $this->strategy;
	}
	
	
	/**
	* Sets strategy object.
	* @param Strategy $strategy
	*/
	public function setStrategy(Strategy $strategy) {
	    $this->strategy = $strategy;
	}
	
	
}
