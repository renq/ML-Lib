<?php

namespace ml\sql;


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
		if (is_array($tmp)) {
			return array_shift($tmp);
		} 
		else {
			return false;
		}
	}
	
	
	/**
	 * @deprecated
	 * @see one
	 */
	public function getOne($query, array $params = array()) {
		return $this->one($query, $params);
	}
	
	/**
	 * @deprecated
	 * @see flat
	 */
	public function getFlatList($query, $params = array()) {
		return $this->flat($query, $params);
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
		// if array is assoc
		if (is_array($params) && 0 !== count(array_diff_key($params, array_keys(array_keys($params))))) {
			if (empty($params)) {
				return false;
			}
			else {
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
					return $this->connection->lastInsertId();
				}
			}
		}
		else {
			throw new Exception('second parameter must be an associative array!');
		}
	}
	
	
	public function saveFromRequest($table, $extraParams = array(), $idColumn = 'id') {
		$params = array();
		foreach (array_intersect(array_keys($this->describe($table)), array_keys($_REQUEST)) as $key) {
			$params[$key] = $_REQUEST[$key];
		}
		
		$id = 0;
		if (isset($params[$idColumn])) {
			$id = $params[$idColumn];
		}
		$params = array_merge($params, $extraParams);
		if (!empty($params)) {
			$id = $this->save($table, $params, $id);
			return $id;
		}
		return null;
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
		return false;
	}
	
	//--- debug
	
	/**
	 * Returns debug informations.
	 * @return array
	 */
	public function getDebug() {
		return $this->connection->getDebug();
	}
	
	
	public function setDebugLevel($debug) {
		$this->connection->debug = $debug;
	}
	
	
	/**
	 * True, when debug is on.
	 * @return boolean
	 */
	public function getDebugLevel() {
		return $this->connection->debug;
	}
	
	
	public function __get($name) {
		if ($name == 'debug') {
			return $this->getDebugLevel();
		}
	}
	
	
	public function __set($name, $value) {
		if ($name == 'debug') {
			$this->setDebugLevel($value);
		}
	}
	
	/**
	 * Returns connection object.
	 * @return Connection
	 */
	public function getConnection() {
		return $this->connection;
	}
	
	/**
	 * Returns strategy object.
	 * @return Strategy
	 */
	public function getStrategy() {
		return $this->strategy;
	}
	
	
}
