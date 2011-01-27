<?php


/**
 * Base Strategy class.
 * @author Michał Lipek (michal@lipek.net)
 * @version 2.0 2010-11-01
 *
 */
abstract class ML_SqlStrategy {
	
	/**
	 * @var ML_SqlConnection
	 */
	protected $connection;
	
	protected $escapeIdentifierCharacter = '"';
	
	
	/**
	 * Constructor.
	 * @param ML_SqlConnection $connection
	 */
	public function __construct(ML_SqlConnection $connection) {
		$this->connection = $connection;
	}
	
	/**
	 * Adds limit part to the query.
	 * @param string $query
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	abstract public function limit($query, $limit, $offset);
	
	
	/**
	 * Gets one (first) row from query result.
	 * @param $query
	 * @return string
	 */
	public function one($query) {
		return $query;
	}
	
	
	/**
	 * Gets row by id.
	 * @param string $table
	 * @param string $idColumn
	 * @return string
	 */
	public function byId($table, $idColumn) {
		$e = $this->escapeIdentifierCharacter;
		return "SELECT * FROM {$e}$table{$e} WHERE {$e}$idColumn{$e} = ? ";
	}
	
	
	/**
	 * Retuns an insert query.
	 * @param string $table
	 * @param array $params
	 * @return string
	 */
	public function insert($table, array $params) {
		$e = $this->escapeIdentifierCharacter;
		$keys = array_keys($params);
		foreach ($keys as &$key) {
			$key = "{$e}$key{$e}";
		}
		$columns = implode(', ', $keys);
		$qm = $this->qm(count($params));
		return "INSERT INTO {$e}$table{$e} ($columns) VALUES ($qm) ";
	}
	
	
	/**
	 * Returns an update query.
	 * @param string $table
	 * @param array $params
	 * @param string $idColumn
	 * @return string
	 */
	public function update($table, array $params, $idColumn) {
		$e = $this->escapeIdentifierCharacter;
		$setParts = array();
		foreach ($params as $k => $v) {
			$setParts[] = "{$e}$k{$e} = ?";
		}
		$set = implode(', ', $setParts);
		return "UPDATE {$e}$table{$e} SET $set WHERE {$e}$idColumn{$e} = ? ";
	}
	
	
	/**
	 * Zwraca zapytanie Delete
	 * @param string $table
	 * @param string $idColumn
	 * @return string
	 */
	public function delete($table, $idColumn) {
		$e = $this->escapeIdentifierCharacter;
		return "DELETE FROM {$e}$table{$e} WHERE {$e}$idColumn{$e} = ? ";
	}
	
	
	/**
	 * Retuns $num question marks. Usefull for .. IN (?, ?, ..., ?).
	 * @param unknown_type $num
	 * @return unknown_type
	 */
	public function qm($num) {
		return implode(', ', array_fill(0, $num, '?'));
	}
	
	
	/**
	 * Returns describe query.
	 */
	public function describe() {
		return "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = ? AND table_name = ?";
	}
	
	
	/**
	 * Returns escape identifier character.
	 * @return string
	 */
	public function getEscapeIdentifierCharacter() {
		return $this->escapeIdentifierCharacter;
	}

	
	/**
	 * Set escape identifier character.
	 * @param string $e
	 */
	public function setEscapeIdentifierCharacter($e) {
		$this->escapeIdentifierCharacter = $e;
	}



}
