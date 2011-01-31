<?php

namespace ml\sql;


class Strategy_Sqlite extends Strategy_MySQLLike {
	
	
	public function limit($query, $limit, $offset) {
		$limit = (int)$limit;
		$offset = (int)$offset;
		return "$query LIMIT $offset, $limit";
	}
	
	
}