<?
function getDBconnection () {
        # Returns a connection to the MySQL server, and selects
        # database $wikiSQLServer. This connection does not have to be
        # closed.
	global $wikiThisDBserver , $wikiThisDBuser, $wikiThisDBpassword , 
               $wikiSQLServer , $wikiDBconnection ;
	if (!$wikiDBconnection) {
	   # Using persistent connections, so that one apache process
	   # can reuse a database connection over and over.
	   $wikiDBconnection=mysql_pconnect ( $wikiThisDBserver , $wikiThisDBuser , $wikiThisDBpassword ) 
		or die("Could not connect to MySQL server: $wikiThisDBserver");
	   mysql_select_db ($wikiSQLServer , $wikiDBconnection)
	        or die("Could not select database: $wikiSQLServer");
	   }
	return $wikiDBconnection ;
	}

function setMySQL ( $table , $var , $value , $cond ) {
	$connection = getDBconnection () ;
	$sql = "UPDATE $table SET $var = \"$value\" WHERE $cond" ;
	$result = mysql_query ( $sql , $connection ) ;
	}


function getMySQL ( $table , $var , $cond ) {
	$connection = getDBconnection () ;
	$sql = "SELECT $var FROM $table WHERE $cond" ;
	$result = mysql_query ( $sql , $connection ) ;
	if ( $result == "" ) {
		return "" ;
		}
	if ( $s = mysql_fetch_object ( $result ) ) {
		$ret = $s->$var ;
		mysql_free_result ( $result ) ;
	} else $ret = "" ;
	return $ret ;
	}

?>
