<?
########### RIGHTS MANAGEMENT

function canEditInNamespace ( $t ) {
	$t = getSecureTitle ( $t ) ;
	global $USERLOGGEDIN , $USERNAME ;
	$namespace = strtolower ( getNamespace ( $t ) ) ;
	if ( $namespace == "" or $namespace == "talk" or $namespace == "wiki" or $namespace == "user" ) return "" ;

	if ( $USERLOGGEDIN != "YES" ) return "You are not logged in! You have to be logged in to edit file. <a href=\"$PHP_SELF?action=login\">Log in</a> or return to the <a href=\"$PHP_SELF?no\">HomePage</a>" ;
	$ret = "" ;
	$rights = ",".getUserSetting ( $USERNAME , "user_rights" )."," ;

	if ( doesNamespaceExist ( $namespace ) == false ) { # Trying to create a new namespace
		if ( !strstr ( $rights , ",is_sysop," ) )
			$ret = "<h1>Access denied</h1>Namespace \"$namespace\" does not exist. Only sysops are authorized to create new namespaces." ;
		}
	else  { # Any other namespace than blank, talk, wiki or user
		if ( strstr ( $rights , ",is_editor," ) or strstr ( $rights , ",is_sysop," ) ) $ret = "" ;
		else $ret = "<h1>Access denied</h1>You are neither an editor nor a sysop, so you cannot edit articles in namespace \"$namespace\"." ;
		}

	return $ret ;
	}

function canEdit( $tt ) {
	global $USERNAME , $USERLOGGEDIN , $action ;
	$restrictions = getTopicSetting ( $tt , "cur_restrictions" ) ;
	if ( $restrictions == "" ) return true ; # No restrictions, OK to edit for everyone
	if ( $restrictions == "NOSUCHTHING" ) {
		$stt = strtolower ( $tt ) ;
		if ( $stt == "recentchanges" ) return false ;
		if ( $action == "revisions" ) return false ;
		if ( $action == "statistics" ) return false ;
		if ( $action == "restrictions" ) return false ;
		if ( $action == "prefs" ) return false ;
		return true ; # New topic
		}
	if ( $USERLOGGEDIN != "YES" ) return false ; # Restrictions, but not logged in -> No edit, bad dog!
	$resArr = explode ( "," , $restrictions ) ;
	$rights = ",".getUserSetting ( $USERNAME , "user_rights" )."," ;

	$allowed = false ;
	foreach ( $resArr as $x ) {
		$y = ",is_$x," ;
		if ( strstr ( $rights , $y ) ) $allowed = true ;
		}
	return $allowed ;
	}

function canRestrict ( $tt ) {
	global $USERNAME , $USERLOGGEDIN , $dosearch ;
	if ( $USERLOGGEDIN != "YES" ) return false ; # Not logged in
	if ( $dosearch == 1 ) return false ; # Search page
	if ( !doesTopicExist ( $tt ) ) return false ; # No such topic
	$rights = ",".getUserSetting ( $USERNAME , "user_rights" )."," ;
	$allowed = false ;
	if ( strstr ( $rights , ",is_editor," ) ) $allowed = true ;
	if ( strstr ( $rights , ",is_sysop," ) ) $allowed = true ;
	return $allowed ;
	}

######## APPLY RESTRICTIONS TO AN ARTICLE
function restrictions () {
	global $title , $therestrictions ;
	$secureTitle = getSecureTitle ( $title ) ;
	if ( !canRestrict ( $title ) ) return "You are not allowed to restrict this article. Follow <a href=\"$PHP_SELF?title=$secureTitle\">this link</a> to go back." ;

	if ( isset ( $therestrictions ) ) {
		changeTopicSetting ( $title , "cur_restrictions" , $therestrictions ) ;
		$ret="<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$PHP_SELF?title=$title&action=view\">" ;
		unset ( $therestrictions ) ;
	} else {
		$ret = "" ;
		$ret .= getStandardHeader () ;
		$r = getTopicSetting ( $title , "cur_restrictions" ) ;
		$ret .= "<FORM action=\"$PHP_SELF?title=$title&action=restrictions\" method=post>\n" ;
		$ret .= "Restrictions : <INPUT TABINDEX=1 TYPE=text NAME=therestrictions VALUE=\"$r\" SIZE=80><br>\n" ;
		$ret .= "<INPUT TYPE=SUBMIT NAME=changeprefs value=\"Save new restrictions\">\n" ;
		$ret .= "</FORM>\n" ;
		}

	return $ret ;
	}

?>