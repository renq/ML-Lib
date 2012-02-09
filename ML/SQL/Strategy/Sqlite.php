<?php

namespace ML\SQL;


class Strategy_Sqlite extends Strategy {
	
	
	public function __construct(Connection $connection) {
		parent::__construct($connection);
		$this->escapeIdentifierCharacter = '"';
	}
	
	
	public function limit($query, $limit, $offset) {
		$limit = (int)$limit;
		$offset = (int)$offset;
		return "$query LIMIT $offset, $limit";
	}
	
	
}