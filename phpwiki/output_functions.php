<?
#######################################
# OUTPUT PROCEDURES
#######################################

function getHeaderFooterParts () {
	global $title , $action , $oid , $whichOldVersion , $dosearch ;
	global $USERNAME ;

	$secureTitle = getSecureTitle ( $title ) ;
	$ret = "" ;
	
	$special = false ;
	if ( $title == "recentchanges" ) $special = true ;
	if ( $action == "revisions" or $action == "statistics" or $action == "restrictions" ) $special = true ;
	if ( $action == "prefs" or $action == "upload" or $action == "edituserrights" or $dosearch == 1 ) $special = true ;

	$ret .= "<a href=\"$PHP_SELF?title=HomePage&action=view\">Home page</a> | " ;
	$ret .= "<a href=\"$PHP_SELF?title=recentchanges&action=view\">Recent changes</a>" ;
	$ret .= " | <a href=\"$PHP_SELF?action=random\">Random page</a>" ;
	if ( !$special ) $ret .= " | <a href=\"$PHP_SELF?title=$secureTitle&action=revisions\">History</a>" ;
	if ( !$special and strstr ( $title , "/" ) ) {
		$parent = substr($title , 0 , strrpos($title,"/")) ;
		$sparent = getSecureTitle ( $parent ) ;
		$ret .= " | <a href=\"$PHP_SELF?title=$sparent&action=view\">$parent</a>" ;
		}

	if ( $action == "view" and !$special and canEdit($title) ) $ret .= " | <a href=\"$PHP_SELF?title=$secureTitle&action=edit\">Edit this page</a>" ;
	if ( $action == "view_old_article" ) $ret .= " | <a href=\"$PHP_SELF?title=$secureTitle&action=view_old_source&oid=$oid&whichOldVersion=$whichOldVersion\">View this source</a>" ;
	if ( $action == "view_old_source" ) $ret .= " | <a href=\"$PHP_SELF?title=$secureTitle&action=view_old_article&oid=$oid&whichOldVersion=$whichOldVersion\">View this article</a>" ;
	$ret .= " | <a href=\"$PHP_SELF?action=upload\">Upload files</a>" ;
	if ( $action != "statistics" ) $ret .= " | <a href=\"$PHP_SELF?action=statistics\">Statistics</a>" ;

	if ( !$special and canRestrict($title) ) $ret .= " | <a href=\"$PHP_SELF?title=$secureTitle&action=restrictions\">Change restrictions</a>" ;

	return $ret ;
	}

function getStandardHeader () {
	global $title , $action , $oid , $whichOldVersion , $dosearch , $search ;
	global $USERNAME ;
	
	$restrictions="" ;
	if ( $action == "view" ) {
		$restrictions = getTopicSetting ( $title , "cur_restrictions" ) ;
		if ( $restrictions == "NOSUCHTHING" ) $restrictions = "" ;
		if ( $restrictions != "" )
			$restrictions = "<font color=red>Restrictions on this page : ".$restrictions."</font>" ;
		}

	$special = false ;
	if ( $title == "recentchanges" ) $special = true ;
	if ( $action == "revisions" or $action == "statistics" or $action == "restrictions" ) $special = true ;
	if ( $action == "prefs" or $action == "upload" or $action == "edituserrights" or $dosearch == 1 ) $special = true ;

	$secureTitle = getSecureTitle ( $title ) ;
	$hversion = "" ;
	if ( $action == "view_old_article" or $action == "view_old_source" ) $hversion = " (Older version #$whichOldVersion)" ;

	$userName = getCurrentUserText () ;
	$hpre = "<table width=\"100%\"><tr><td valign=top><font size=\"+3\">" ;
	$hpost  = "</font></td><td align=right nowrap><font color=red>User : $userName</font><br>";
	$hpost .= "</td><td rowspan=3 width=1><a href=\"$PHP_SELF/call\"><img border=0 src=\"wiki.jpg\"></a></td></tr>";
	$hpost .= "<tr><td valign=top>".getHeaderFooterParts()."<br>$restrictions</td>" ;
	$hpost .= "<td valign=top align=right nowrap><FORM>Search: <INPUT TYPE=text NAME=search SIZE=20><INPUT TYPE=hidden NAME=dosearch VALUE=1></FORM></td>" ;
	$hpost .= "</tr><tr><td colspan=2><hr></td>" ;
	$hpost .= "</tr></table>" ;

	$ntitle = getNiceTitle ( $title ) ;
	if ( $search != "" ) $asearch = "Search results for \"$search\" :" ;
	else $asearch = "All pages index :" ;

	if ( $action == "view" or $action == "view_old_article" or $action == "view_old_source" or $special ) {
		if ( $title == "recentchanges" ) $thebody = "Recent Changes" ;
		else if ( $action == "revisions" ) $thebody = "History of $ntitle" ;
		else if ( $action == "upload" ) $thebody = "Upload page" ;
		else if ( $dosearch == 1 ) $thebody = $asearch ;
		else if ( $action == "statistics" ) $thebody = "Statistics (".date("l, F d, Y H:i:s").", PST)" ;
		else if ( $action == "edituserrights" ) $thebody = "Edit user access rights here" ;
		else if ( $action == "restrictions" ) $thebody = "Restrictions of $ntitle" ;
		else if ( $action == "prefs" ) $thebody = "Preferences for $USERNAME" ;
		else $thebody = "<a href=\"$PHP_SELF?$action=search&search=$secureTitle&dosearch=1\">$ntitle</a>$hversion" ;
		$head = $hpre.$thebody.$hpost ;
	} else if ( $action == "edit" or $action == "preview" ) {
		$head = $hpre."Editing $ntitle".$hpost ;
		}

	return $head ;
	}

function getStandardFooter () {
	global $title ;
	$ret = "<hr>" ;
	$ret .= getHeaderFooterParts () ;
	$rtitle = stripNamespace ( $title ) ;
	$namespaces = getAllNamespaces ( $rtitle ) ;
	if ( $namespaces != "" ) $ret .= "<br><font color=green>Namespaces : </font>".$namespaces ;
	$ret .= "<FORM>Search: <INPUT TYPE=text NAME=search SIZE=20><INPUT TYPE=hidden NAME=dosearch VALUE=1></FORM>" ;
	return $ret ;
	}
?>