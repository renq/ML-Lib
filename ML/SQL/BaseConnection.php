<?php

namespace ML\SQL;


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
	 * Constructor
	 * @param Settings $settings
	 */
	public function __construct(Settings $settings) {
		$this->settings = $settings;
	}
	
	
	/**
	 * Returns settings object.
	 * @return Settings
	 */
	public function getSettings() {
		return $this->settings;
	}
	
	
	protected function parseQueryArrays($query, array $params) {
	    $newQuery = '';
	    $newParams = array();
	    $split = explode('?', $query);
	    foreach ($params as $v) {
	        $newQuery .= array_shift($split);
	        if (is_array($v)) {
	            $newQuery .= '(' . implode(', ', array_fill(0, count($v), '?')) . ')';
	            $newParams = array_merge($newParams, $v);
	        }
	        else {
	            $newQuery .= '?';
	            $newParams[] = $v;
	        }
	    }
	    $newQuery .= array_shift($split);
	    return array($newQuery, $newParams);
	}
	
	
}

