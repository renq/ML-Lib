<?php

namespace ML\SQL;


/**
 * Base Strategy class.
 * @author Michał Lipek (michal@lipek.net)
 * @version 2.0 2010-11-01
 *
 */
abstract class Strategy {
	
	/**
	 * @var Connection
	 */
	protected $connection;
	
	protected $escapeIdentifierCharacter = '"';
	
	
	/**
	 * Constructor.
	 * @param Connection $connection
	 */
	public function __construct(Connection $connection) {
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
		$table = $this->escapeIdentifier($table);
		$idColumn = $this->escapeIdentifier($idColumn);
		return "SELECT * FROM $table WHERE $idColumn = ? ";
	}
	
	
	/**
	 * Retuns an insert query.
	 * @param string $table
	 * @param array $params
	 * @return string
	 */
	public function insert($table, array $params) {
		$table = $this->escapeIdentifier($table);
		$keys = array_keys($params);
		foreach ($keys as &$key) {
			$key = $this->escapeIdentifier($key);
		}
		$columns = implode(', ', $keys);
		$qm = $this->qm(count($params));
		return "INSERT INTO $table ($columns) VALUES ($qm) ";
	}
	
	
	/**
	 * Returns an update query.
	 * @param string $table
	 * @param array $params
	 * @param string $idColumn
	 * @return string
	 */
	public function update($table, array $params, $idColumn) {
		$table = $this->escapeIdentifier($table);
		$idColumn = $this->escapeIdentifier($idColumn);
		
		$setParts = array();
		foreach ($params as $k => $v) {
			$setParts[] = $this->escapeIdentifier($k) . ' = ?';
		}
		$set = implode(', ', $setParts);
		return "UPDATE $table SET $set WHERE $idColumn = ? ";
	}
	
	
	/**
	 * Zwraca zapytanie Delete
	 * @param string $table
	 * @param string $idColumn
	 * @return string
	 */
	public function delete($table, $idColumn) {
		$table = $this->escapeIdentifier($table);
		$idColumn = $this->escapeIdentifier($idColumn);
		
		return "DELETE FROM $table WHERE $idColumn = ? ";
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
	
	
	/**
	 * 
	 * Escape an identifier, i.e. table or column name.
	 * @param string $name
	 * @return string escaped identifier
	 */
	public function escapeIdentifier($name) {
		$e = $this->getEscapeIdentifierCharacter();
		$parts = \explode('.', $name);
		foreach ($parts as &$part) {
			$part = "{$e}$part{$e}";
		}
		return implode('.', $parts);
	}



}
