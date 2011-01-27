<?php


class ML_MysqllikeSqlStrategy extends ML_SqlStrategy {
	
	
	public function __construct(ML_SqlConnection $connection) {
		parent::__construct($connection);
		$this->escapeIdentifierCharacter = '"';
	}
	
	
	public function limit($query, $limit, $offset) {
		$limit = (int)$limit;
		$offset = (int)$offset;
		return "$query LIMIT $offset, $limit";
	}
	
	
}