<?php

namespace ml\sql;


class Strategy_Mysql extends Strategy_MySQLLike {
	
	
	public function __construct(Connection $connection) {
		parent::__construct($connection);
		$this->escapeIdentifierCharacter = '`';
	}
	
	
	public function limit($query, $limit, $offset) {
		$limit = (int)$limit;
		$offset = (int)$offset;
		return "$query LIMIT $offset, $limit";
	}
	
	
}