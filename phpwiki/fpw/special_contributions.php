<?
# This function list the contributions of a user
function contributions () {
	global $THESCRIPT , $target , $user , $protecting , $newrestrictions ;
	global $vpage , $theuser ;
	global $wikiContribTitle , $wikiContribText , $wikiContribDenied ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;
	$vpage->special ( str_replace ( "$1" , $theuser , $wikiContribTitle ) ) ;
	$vpage->makeSecureTitle () ;
	if ( $theuser == "" ) return "<nowiki><h1>$wikiContribDenied</h1></nowiki>" ;
	$theuser = str_replace ( "_" , " " , $theuser ) ;
	$ret = "<nowiki>".str_replace("$1",$theuser,$wikiContribText)."</nowiki>\n" ;

	$ac = array () ;
	$connection = getDBconnection () ;

	$question = "SELECT cur_title FROM cur WHERE cur_user_text=\"$theuser\" AND cur_minor_edit<>1" ;
	$result = mysql_query ( $question , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) array_push ( $ac , $s->cur_title ) ;
	mysql_free_result ( $result ) ;

	$question = "SELECT old_title FROM old WHERE old_user_text=\"$theuser\" AND old_minor_edit<>1" ;
	$result = mysql_query ( $question , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) )
		if ( !in_array ( $s->cur_title , $ac ) )
			array_push ( $ac , $s->cur_title ) ;
	mysql_free_result ( $result ) ;

	if ( count ( $ac ) == 0 AND $theuser == ucfirst ( $theuser ) ) { # Rerun with lowercase name
		$theuser = strtolower(substr($theuser,0,1)).substr($theuser,1) ;
		return contributions() ;
		}


	asort ( $ac ) ;
	foreach ( $ac as $x ) {
		$b = spliti ( "talk:" , $x ) ;
		if ( $x != "" and substr ( $x , 0 , 4 ) != "Log:" and count ( $b ) == 1 )
			$ret .= "* [[".$vpage->getNiceTitle($x)."]]\n" ;
		}

	return $ret ;
	}
?>
