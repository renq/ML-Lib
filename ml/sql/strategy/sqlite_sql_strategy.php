<?php


class ML_SqliteSqlStrategy extends ML_MysqllikeSqlStrategy {
	
	
	public function limit($query, $limit, $offset) {
		$limit = (int)$limit;
		$offset = (int)$offset;
		return "$query LIMIT $offset, $limit";
	}
	
	
}