<?
function getDBconnection () {
	global $wikiThisDBserver , $wikiThisDBuser, $wikiThisDBpassword ;
	$server = $wikiThisDBserver ;
	$user = $wikiThisDBuser ;
	$passwd = $wikiThisDBpassword ;
	# Using persistent connections, so that one apache process
	# can reuse one database connection over and over.
	# There is no need for mysql_close.
	$connection=mysql_pconnect ( $server , $user , $passwd ) ;
	return $connection ;
	}

function setMySQL ( $table , $var , $value , $cond ) {
	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "UPDATE $table SET $var = \"$value\" WHERE $cond" ;
	$result = mysql_query ( $sql , $connection ) ;
	}

function getMySQL ( $table , $var , $cond ) {
	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
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
