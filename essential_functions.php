<?
function getDBconnection () {
	$server="127.0.0.1" ;
	$user="manske" ;
	$passwd="test" ;
	$connection=mysql_connect ( $server , $user , $passwd ) ;
	return $connection ;
	}
?>