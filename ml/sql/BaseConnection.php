<?php

namespace ml\sql;


/**
 * SqlConnection is an abstract class corresponds to database connection.
 * Subclasses to this class implements connection to specific database, ie. MySQL.  
 * @author Michał Lipek (michal@lipek.net)
 * @version 2.0 2010-01-08
 */
abstract class BaseConnection implements Connection {
	
	
	/*
	 * DSN
	 * @var string
	 */
	//protected $dsn;
	
	/**
	 * Settings object generated from DSN string.
	 * @var Settings
	 */
	protected $settings;
	
	/**
	 * Debug mode. If true, all queries are stored.
	 * @var unknown_type
	 */
	public $debug = false;
	
	/**
	 * Keep all runned queries. Works only if debug mode is on.
	 * @var array
	 */
	private $debugData = array();
	
	
	/**
	 * Constructor
	 * @param Settings $settings
	 */
	public function __construct(Settings $settings) {
		$this->settings = $settings;
	}
	
	
	/**
	 * Execute query.
	 * @param string $query
	 * @param array $params
	 * @return mixed
	 */
	public function query($query, array $params = array()) {
		if ($this->debug) {
			$this->addToDebug($query, $params);
		}
	}
	
	
	/**
	 * Add query to query debug list. 
	 * @param string $query
	 * @param array $params
	 */
	protected function addToDebug($query, array $params = array()) {
		$this->debugData[] = $this->buildSql($query, $params);
	}
	
	/**
	 * Builds query. This function shouldn'd be use in production code.
	 * Debug queries are created with this function.
	 * @param string $query
	 * @param arry $params
	 * @return string
	 */
	public function buildSql($query, $params) {
		$count = 0;
		$query = str_replace(array('%', '?'), array('%%', '%s'), $query, $count);
		
		foreach ($params as &$param) {
			$param = $this->escape($param);
		}
		return vsprintf($query, $params);	
	}
	
	
	/**
	 * Returns executed query. Works only if debug is on.
	 * @return array
	 */
	public function getDebug() {
		return $this->debugData;
	}
	
	
	/**
	 * Returns settings object.
	 * @return Settings
	 */
	public function getSettings() {
		return $this->settings;
	}
	
	
}

