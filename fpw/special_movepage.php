<?
function doMove ( $ot , $nt , $rd ) {
	global $user ;
        $connection = getDBconnection () ;
	$t = new wikiPage ;
	$t->setTitle ( $ot ) ;
	$ost = $t->secureTitle ;        
	$t->setTitle ( $nt ) ;
	$nst = $t->secureTitle ;        

	$sql = "UPDATE old SET old_title=\"$nst\" WHERE old_title=\"$ost\"" ;
        $result = mysql_query ( $sql , $connection ) ;

	$sql = "UPDATE cur SET cur_title=\"$nst\" WHERE cur_title=\"$ost\"" ;
        $result = mysql_query ( $sql , $connection ) ;

	$sql = "UPDATE linked SET linked_from=\"$nst\" WHERE linked_from=\"$ost\"" ;
        $result = mysql_query ( $sql , $connection ) ;

	$sql = "UPDATE unlinked SET unlinked_from=\"$nst\" WHERE unlinked_from=\"$ost\"" ;
        $result = mysql_query ( $sql , $connection ) ;

	if ( $rd ) {
		$t->setTitle ( $ot ) ;
		$t->ensureExistence () ;
		$t->setEntry ( "#REDIRECT [[$nt]]" , "Moved to $nt" , $user->id , $user->name , 1 ) ;
		}

	$ret = "'$ot' was successfully moved to '$nt'." ;
	if ( $rd ) $ret .= " A redirect was created." ;
	return $ret ;
	}

function movepage ( ) {
	global $vpage , $target , $wikiMoveThisPage , $newname , $doredirect , $doit ;
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
		if ( $newname == "" or $npage->doesTopicExist () ) $warning = "<font color=red><b>'$newname' already exists! Please choose another name.</b></font><br><br>\n" ;
		else {
			$ret = doMove ( $nt , $newname , $doredirect ) ;
			$doshow = false ;
			}
		}

	if ( $doshow ) {
		$ret .= $warning ;
		$ret .= "<h2>You are about to move '$nt' and its history to a new title.</h2>\n" ;
		$ret .= "<FORM method=post>\n" ;
		$ret .= "New name : <INPUT type=text value='$newname' name=newname size=40 maxlength=250><br><br>\n" ;
		$ret .= "<INPUT type=checkbox$checked name=doredirect>Create a #REDIRECT from '$nt' to the new title<br><br>\n" ;
		$ret .= "<INPUT type=submit name=doit value='Move'>\n" ;
		$ret .= "</FORM>\n" ;
		}

	return $ret ;
	}
?>
