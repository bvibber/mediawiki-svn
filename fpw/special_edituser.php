<?
function editUser () {
	global $user , $vpage , $theuser , $todo ;
	global $wikiPermissionDenied , $wikiEditUserTitle , $allowedRoles , $wikiChangeRights , $wikiCurrentUserRights ;
	$vpage->special ( $wikiEditUserTitle ) ;
	if ( !in_array ( "is_sysop" , $user->rights ) )
		return $wikiPermissionDenied ;

	$ret = "" ;
	$connection = getDBconnection () ;
	$sql = "SELECT * from user WHERE user_id=\"$theuser\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$u = mysql_fetch_object ( $result ) ;
	mysql_free_result ( $result ) ;

	if ( $todo == "editrights" ) {
		global $b1 ;
		$k = array_keys ( $allowedRoles ) ;
		if ( isset ( $b1 ) ) { # Button was pressed
			$v1 = array () ;
			foreach ( $k as $x ) {
				$c = "cb$x" ;
				global $$c ;
				$d = $$c ;
				if ( $$c == "yes" ) array_push ( $v1 , $x ) ;
				}
			$v1 = implode ( "," , $v1 ) ;
			$sql = "UPDATE user SET user_rights=\"$v1\" WHERE user_id=\"$theuser\"" ;
			$result = mysql_query ( $sql , $connection ) ;
			$u->user_rights = $v1 ;
#			$ret .= "<font color=red>The change has been saved!</font><br>\n" ;
			}
		$ret .= "<FORM method=post>\n" ;
		$ret .= str_replace ( "$1" , $u->user_name , $wikiCurrentUserRights ) ;
		$r = explode ( "," , strtolower ( $u->user_rights ) ) ;

		foreach ( $k as $x ) {
			if ( in_array ( $x , $r ) ) $c = " checked" ;
			else $c = "" ;
			$ret .= "<INPUT TYPE=checkbox value=yes name='cb$x'$c>".$allowedRoles[$x]."<br>\n" ;
			}

		$ret .= "<INPUT TYPE=submit name=b1 value=\"$wikiChangeRights\">" ;
		$ret .= "</FORM>\n" ;
		}

	return $ret ;
	}
?>
