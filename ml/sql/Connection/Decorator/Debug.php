<?php

namespace ml\sql;


class Connection_Decorator_Debug extends Connection_Decorator {
	
	
	private $decorated;
	private $queries = array();

	
	public function __construct(Connection $connection) {
		parent::__construct($connection);
		$this->decorated = $connection;
	}
	
		
	public function query($query, array $params = array()) {
		$this->queries[] = $this->buildQuery($query, $params);		
		return $this->decorated->query($query, $params);
	}
	
	
	public function getDebug() {
		return $this->queries;
	}
	

	private function buildQuery($query, $params) {
		$count = 0;
		$query = str_replace(array('%', '?'), array('%%', '%s'), $query, $count);
		foreach ($params as $param) {
			$param = $this->escape($param);
		}
		return vsprintf($query, $params);	
	}
	
	
}
