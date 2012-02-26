<?php

namespace ML\SQL;


/**
 * Iinterface defines methods for connection classes.  
 * @author Michał Lipek (michal@lipek.net)
 * @version 2.0 2010-01-08
 */
interface Connection {
	
	
	/**
	 * Connect to database.
	 * @return mixed
	 */
	public function connect();
	
	/**
	 * Disconnect from database.
	 */
	public function disconnect();
	
	/**
	 * Fetch row from query result.
	 * @param mixed $queryResult
	 * @return array
	 */
	public function fetch($queryResult);
	
	/**
	 * Begin transaction. Not works on every database.
	 */
	public function beginTransaction();
	
	/**
	 * Commit transaction.
	 */
	public function commit();
	
	/**
	 * Rollback transaction.
	 */
	public function rollback();
	
	/**
	 * Returns last inset id.
	 * @param string $table Table name
	 * @param string $table identify column name
	 * @return int
	 */
	public function lastInsertId($table = '', $idColumn = '');
	
	/**
	 * Returns databse connection.
	 * @return mixed
	 */
	public function getHandle();
	
	
	/**
	 * 
	 * Sets database handle. 
	 * @param resource $handle
	 */
	public function setHandle($handle);
	
	/**
	 * Execute query.
	 * @param string $query
	 * @param array $params
	 * @return mixed
	 */
	public function query($query, array $params = array());
	
	/**
	 * Escape variable.
	 * @param mixed
	 */
	public function escape($value);
	
	/**
	 * Returns rows affected for last SQL operation.
	 * @return int
	 */
	public function getAffectedRows();
	
	/**
	 * Returns settings object.
	 * @return Settings
	 */
	public function getSettings();
	
	/**
	* Sets settings object
	* @param Settings $settings 
	*/
	public function setSettings(Settings $settings);
	
	
}

