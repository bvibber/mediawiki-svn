<?
function getDBconnection () {
	$server="127.0.0.1" ;
	$user="manske" ;
	$passwd="KMnO4" ;
	$connection=mysql_connect ( $server , $user , $passwd ) ;
	return $connection ;
	}

function setMySQL ( $table , $var , $value , $cond ) {
	$connection = getDBconnection () ;
	mysql_select_db ( "wikipedia" , $connection ) ;
	$sql = "UPDATE $table SET $var = \"$value\" WHERE $cond" ;
	$result = mysql_query ( $sql , $connection ) ;
	mysql_close ( $connection ) ;
	}

function getMySQL ( $table , $var , $cond ) {
	$connection = getDBconnection () ;
	mysql_select_db ( "wikipedia" , $connection ) ;
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