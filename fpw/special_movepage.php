<?
function doMove ( $ot , $nt , $rd ) {
	global $user ;
        $connection = getDBconnection () ;
	$t = new wikiPage ;
	$t->setTitle ( $ot ) ;
	$ost = $t->secureTitle ;        
	$t->setTitle ( $nt ) ;
	$nst = $t->secureTitle ;        

	# Updating old table
	$sql = "UPDATE old SET old_title=\"$nst\" WHERE old_title=\"$ost\"" ;
        $result = mysql_query ( $sql , $connection ) ;

	# Updating new entry
	$sql = "UPDATE cur SET cur_title=\"$nst\" WHERE cur_title=\"$ost\"" ;
        $result = mysql_query ( $sql , $connection ) ;

	# Updating links with the old page as source
	$sql = "UPDATE linked SET linked_from=\"$nst\" WHERE linked_from=\"$ost\"" ;
        $result = mysql_query ( $sql , $connection ) ;
	$sql = "UPDATE unlinked SET unlinked_from=\"$nst\" WHERE unlinked_from=\"$ost\"" ;
        $result = mysql_query ( $sql , $connection ) ;

	# Updating links to the new REDIRECT
	# INCOMPLETE, thus deactivated
/*
	$sql = "UPDATE linked SET linked_to=\"$nst\" WHERE linked_to=\"$ost\"" ;
        $result = mysql_query ( $sql , $connection ) ;
	# Now the article bodies will have to be canged...
*/

	if ( $rd ) {
		global $wikiMoveRedirectMessage ;
		$t->setTitle ( $ot ) ;
		$t->ensureExistence () ;
		$t->setEntry ( "#REDIRECT [[$nt]]" , str_replace ( "$1" , "$nt" , $wikiMoveRedirectMessage ) , $user->id , $user->name , 1 ) ;
		}

	global $wikiMoveMoved , $wikiMoveRedirected ;
	$ret = str_replace ( "$1" , $ot , str_replace ( "$2" , $nt , $wikiMoveMoved ) ) ;
	if ( $rd ) $ret .= $wikiMoveRedirected ;
	return $ret ;
	}

function movepage ( ) {
	global $vpage , $target , $wikiMoveThisPage , $newname , $doredirect , $doit , $wikiMoveWarning , $wikiMoveForm ;
	$vpage->setTitle ( $wikiMoveThisPage ) ;
	$ret = "" ;
	$doshow = true ;

	$nt = $vpage->getNiceTitle ( urldecode ( $target ) ) ;
	if ( $newname == "" ) $newname = $nt ;
	$checked = " checked" ;
	if ( $doredirect == "" and $doit != "" ) $checked = "" ;

	if ( $doit != "" ) {
		$npage = new wikiPage ;
		$npage->setTitle ( $newname ) ;
		if ( $newname == "" or $npage->doesTopicExist () ) $warning = str_replace ( "$1" , $newname , $wikiMoveWarning ) ;
		else {
			$ret = doMove ( $nt , $newname , $doredirect ) ;
			$doshow = false ;
			}
		}

	if ( $doshow ) {
		$ret .= $warning ;
		$ret .= str_replace ( "$3", $checked, str_replace("$2" , $newname , str_replace ( "$1" , $nt , $wikiMoveForm ) ) );
		}

	return $ret ;
	}
?>
