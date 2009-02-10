<?php
/*
 * Miniature and sort-of-similar version of the MediaWiki Database class
 */

class Database {

	/* Members */

	private $connection;

	/* Functions */

	public function __construct() {
		global $localSettings;
		$this->connection = mysql_connect(
			$localSettings['host'],
			$localSettings['username'],
			$localSettings['password']
		) or die ( "Unable to connect!" );
		mysql_select_db( $localSettings['dbname'] )
			or die ("Unable to select database!");
	}

	public function __destruct() {
		mysql_close( $this->connection );
	}

	public function select(
		$tables,
		$fields,
		$conditions = null
	) {
		$query = sprintf(
			'SELECT %s FROM %s',
			is_array( $fields ) ? implode( ',', $fields ) : $fields,
			is_array( $tables ) ? implode( ',', $tables ) : $tables
		);
		if ( $conditions != null ) {
			$conditionList = array();
			foreach ( $conditions as $key => $value ) {
				if ( is_int( $key ) ) {
					$conditionList[] = $value;
				} else {
					$conditionList[] = "{$key}=" . $this->addQuotes( $value );
				}
			}
			if ( count( $conditionList > 0 ) ) {
				$query .= ' WHERE ' . implode( ' AND ', $conditionList );
			}
		}
		$result = mysql_query( $query )
			or die ("Error in query: $query. ".mysql_error() );
		return new DatabaseResult( $result );
	}

	public function addQuotes(
		$string
	) {
		return "'" . mysql_real_escape_string( $string ) . "'";
	}
}

class DatabaseResult {

	/* Members */

	private $result;

	/* Functions */

	public function __construct(
		$result
	) {
		$this->result = $result;
	}

	public function __destruct() {
		mysql_free_result( $this->result );
	}

	public function numRows() {
		return mysql_num_rows( $this->result );
	}

	public function fetchRow() {
		return mysql_fetch_array( $this->result );
	}
}
