<?
function getDBconnection () {
	global $wikiThisDBserver , $wikiThisDBuser, $wikiThisDBpassword ;
	$server = $wikiThisDBserver ;
	$user = $wikiThisDBuser ;
	$passwd = $wikiThisDBpassword ;
	$connection=mysql_connect ( $server , $user , $passwd ) ;
	return $connection ;
	}

function setMySQL ( $table , $var , $value , $cond ) {
	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "UPDATE $table SET $var = \"$value\" WHERE $cond" ;
	$result = mysql_query ( $sql , $connection ) ;
	mysql_close ( $connection ) ;
	}

function getMySQL ( $table , $var , $cond ) {
	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT $var FROM $table WHERE $cond" ;
	$result = mysql_query ( $sql , $connection ) ;
	if ( $result == "" ) {
		mysql_close ( $connection ) ;
		return "" ;
		}
	if ( $s = mysql_fetch_object ( $result ) ) {
		$ret = $s->$var ;
		mysql_free_result ( $result ) ;
		mysql_close ( $connection ) ;
	} else $ret = "" ;
	return $ret ;
	}
?>