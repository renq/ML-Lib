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
	
	
	/**
	* Sets settings object.
	* @param Settings $settigns
	*/
	public function setSettings(Settings $settings) {
	    $this->settings = $settings;
	}
	
	
}

