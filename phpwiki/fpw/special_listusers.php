<?
function listUsers () {
	global $user , $vpage , $startat , $wikiUser , $wikiEditUserRights ;
	if ( !isset ( $startat ) ) $startat = 1 ;
	$perpage = $user->options["resultsPerPage"] ;
	if ( $perpage == 0 ) $perpage = 20 ;
	global $wikiUserlistTitle , $wikiUserlistText ;
	$vpage->special ( $wikiUserlistTitle ) ;
	$vpage->namespace = "" ;
	$ret = "$wikiUserlistText\n\n" ;
	$connection = getDBconnection () ;
	$sql = "SELECT * from user" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		$ret .= "#[[$wikiUser:$s->user_name|$s->user_name]]" ;
		if ( in_array ( "is_sysop" , $user->rights ) ) {
			$ret .= "; ($s->user_rights); " ;
			$ret .= "<a href=\"".wikiLink("special:edituser&theuser=$s->user_id&todo=editrights")."\">$wikiEditUserRights</a> " ;
			}
		$ret .= "\n" ;
		}

	return $ret ;
	}
?>
