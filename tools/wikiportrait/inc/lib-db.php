<?php
Class db {
	private $db = false;
	public  $last_query = '';
	public  $num_rows;
	public  $last_insert_id;
	public  $last_error;
	private $stripslashes = false;

	function db( $host, $user, $pass, $database ) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->database = $database;
		$this->debug = 1;
		$this->connect();
	}

	function select_db( $database = false ) {
		// only change if a new database is given
		if ( $database ) $this->database = $database;
		@mysql_select_db( $this->database, $this->db ) or $this->bail( "The database " . $this->database . " could not be found. " );
	}

	function connect() {
		@$this->db = mysql_connect( $this->host, $this->user, $this->pass ) or $this->bail( "Could not connect to database!" );
		$this->select_db();
		return $db;
	}

	function query( $sql ) {
		$this->last_query = $sql;
		@$result = mysql_query( $sql );
		if ( $result ) {
			// TODO
			// This is a little risky, because the insert_id() function is
			// run every time a query is done, while it should only happen with INSERT
			// queries
			$this->last_insert_id = @mysql_insert_id( $this->db );
			return $result;
		}
		else {
			$this->last_error = @mysql_error();
			$this->bail();
		}
	}

	function last_query() {
		return $this->last_query;
	}

	function where( $where ) {
		// User can either pass the $where clausule as an array
		// like array("id" => 5, "text" => "foo"); or as a digit
		// in the last case, we assume its an id
		if ( is_array( $where ) ) {
			$sql = " WHERE ";
			$i = 0;
			foreach ( $where as $key => $value ) {
				$sql .= "`" . $this->escape( $key ) . "` = '" . $this->escape( $value ) . "'";
				if ( $i < count( $where ) - 1 ) {
					$sql .= " AND ";
				}
				$i++;
			}
			$sql .= ";";
		}
		else if ( $where != false ) {
			$sql =  "WHERE `id`='" . $this->escape( $where ) . "' LIMIT 1;";
		}
		return $sql;
	}

	function select( $table, $where = false ) {
		$sql = "SELECT * FROM `" . $this->escape( $table ) . "`";

		// Add WHERE clause if needed
		if ( $where ) {
			$sql .= $this->where( $where );
		}

		$result = $this->query( $sql );
		return $this->convert_to_array( $result );
	}

	function num_rows() {
		// returns number of returned rows from the last query
		return $this->num_rows;
	}

	function delete( $table, $where ) {
		$sql  = "DELETE FROM `$table`";
		$sql .= $this->where( $where );
		return $this->query( $sql, false );
	}

	function insert( $table, $array ) {
		// Loop through the $values array to add keys and values
		$i = 0;
		$max = count( $array ) - 1;
		foreach ( $array as $key => $value ) {
			$insert .= "`" . $this->escape( $key ) . "`";
			if ( $i < $max ) {
				$insert .= ",";
			}
			$values .= "'" . $this->escape( $value ) . "'";
			if ( $i < $max ) {
				$values .= ",";
			}
			$i++;
		}

		// Finally make the sql statement and query it
		$sql = "INSERT INTO `$table` ($insert) VALUES ($values);";
		return $this->query( $sql, false );
	}

	function convert_to_array( $mysql_result ) {
		if ( !$mysql_result ) return false;

		// If this result only has one row, simply return that (default setting)
		if ( mysql_num_rows( $mysql_result ) == 1 ) {
			return mysql_fetch_array( $mysql_result, MYSQL_ASSOC );
		}
		else {
			$array = array();
			while ( $row = mysql_fetch_array( $mysql_result, MYSQL_ASSOC ) ) {
				// Stupid PHP with its slashes
				if ( ( $this->stripslashes ) && get_magic_quotes_gpc() ) {
					$row = stripslashes_deep( $row );
				}
				$array[] = $row;
			}
			return $array;
		}
	}

	function escape( $value ) {
		// Strips slashes and escapes possibile scary characters
		if ( get_magic_quotes_gpc() ) {
			$value = stripslashes( $value );
		}
		return mysql_real_escape_string( $value, $this->db );
	}

	function bail( $msg ) {
		// die with a message
		if ( $this->debug > 0 ) $msg .= "<br />Mysql_error: " . $this->last_error . "<br />Last query:" . $this->last_query;
		die( $msg );
	}
} // Class db
