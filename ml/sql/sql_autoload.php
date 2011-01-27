<?php 


/**
 * Autoload for ML_Sql library 
 * @author Michał Lipek (michal@lipek.net)
 *
 */
class ML_SqlAutoload {
	
	
	public static function load($class) {
		if (strpos($class, 'ML_') !== false) {
			$filename = dirname(__FILE__).'/'.substr(strtolower(preg_replace('/([A-Z])/', '_\1', $class)), 6) . '.php';
			if (strpos($class, 'Strategy')) {
				$filename = dirname(__FILE__).'/strategy/'.substr(strtolower(preg_replace('/([A-Z])/', '_\1', $class)), 6) . '.php';
			}
			elseif (strpos($class, 'Connection')) {
				$filename = dirname(__FILE__).'/connection/'.substr(strtolower(preg_replace('/([A-Z])/', '_\1', $class)), 6) . '.php';
			}
			if ($filename && file_exists($filename)) {
				include_once($filename);
			}
		}
		return false;
	}
	
	
}