<?
function listUsers () {
	global $user , $vpage , $startat ;
	if ( !isset ( $startat ) ) $startat = 1 ;
	$perpage = $user->options["resultsPerPage"] ;
	if ( $perpage == 0 ) $perpage = 20 ;
	global $wikiUserlistTitle , $wikiUserlistText ;
	$vpage->special ( $wikiUserlistTitle ) ;
	$vpage->namespace = "" ;
	$ret = "$wikiUserlistText\n\n" ;
	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT * from user" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		$ret .= "#[[user:$s->user_name|$s->user_name]]" ;
		if ( in_array ( "is_sysop" , $user->rights ) ) $ret .= " ($s->user_rights)" ;
		$ret .= "\n" ;
		}

	return $ret ;
	}
?>