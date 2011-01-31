<?php

namespace ml\sql;


class Strategy_MSSQL extends Strategy {
	
	
	public function __construct(Connection $connection) {
		parent::__construct($connection);
		$this->escapeIdentifierCharacter = '"';
	}
	
	
	/**
	 * @TODO
	 * !FIXIT!
	 * Test it, fix it!
	 * @param $query
	 * @param $limit
	 * @param $offset
	 */
	public function limit($query, $limit, $offset) {
		$limit = (int)$limit;
		$offset = (int)$offset;
		$end = $offset + $limit;
 
		$afterFrom = stristr($query, ' FROM ');
		$beforeFrom = substr($query, 0, stripos($query, ' FROM '));
		$afterOrder = stristr($query, 'ORDER BY ');
		if (!$afterOrder) {
			throw new Exception("MSSQL needs ORDER BY in your queries.");
		}
		$rowNumber = ", ROW_NUMBER() OVER ($afterOrder) AS RowNum "; 
		
		$cols = ' '.strstr($beforeFrom, 'SELECT').' ';
		
		$query = "$cols FROM ( ";
		$query .= $beforeFrom . $rowNumber . $afterFrom . ' ) AS MLSqlTmpTable ';
		$query .= " WHERE MLSqlTmpTable.RowNum BETWEEN $offset AND $end ";
		return $query;
	}
	
	
}

