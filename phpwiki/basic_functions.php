<?
############################################
# BASIC FUNCTIONS
############################################

function view () {
	global $title , $action ;
	$content = acquireTopic ( $title ) ;
	if ( strtolower ( substr ( $content , 0 , 9 ) ) == "#redirect" ) {
		$pieces1=spliti("\[\[",$content,2);
		$pieces2=spliti("\]\]",$pieces1[1],2);
		$thelink=getSecureTitle($pieces2[0]) ;
		$otitle = $title ;
		$title = $thelink ;
		$content="<font color=green>[Redirected from <b>$otitle</b>]</font><hr>".acquireTopic ( $title ) ;
		}
	$content = parseContent ( $content ) ;
	$secureTitle = getSecureTitle ( $title ) ;
	$head = getStandardHeader () ;
	$content = $head.$content."\n" ;
	$content .= getStandardFooter () ;
	return $content ;
	}

function view_old_article ( $mode="parsed" ) {
	global $title , $action , $oid ;
	if ( $oid == "" ) return "NO OID GIVEN" ;

	$content = acquireOldTopic ( $title , $oid ) ;
	if ( $mode == "parsed" )
		$content = parseContent ( $content ) ;
	else if ( $mode == "source" )
		$content = "<textarea name=newtext rows=20 cols=65 STYLE=\"width:100%\" wrap=virtual>$content</textarea>" ;

	$secureTitle = getSecureTitle ( $title ) ;
	$head = getStandardHeader () ;

	$content = $head.$content ;
	$content .= getStandardFooter () ;

	return $content ;
	}

function edit () {
	global $title , $action ;
	global $newtext , $comment , $recent_edit ;

	if ( ! $comment ) $comment = "*" ;
	if ( $recent_edit ) $recent_edit = "on" ;
	else $recent_edit = "off" ;

	$realTitle=$title ;
	$secureTitle = getSecureTitle ( $title ) ;

	# Checking clearance
	if ( !canEdit($title) ) return "You are not allowed to edit this article. Follow <a href=\"$PHP_SELF?title=$secureTitle\">this link</a> to go back." ;

	$cein = canEditInNamespace ( $title ) ;
	if ( $cein != "" ) return $cein ;

	if ( $newtext ) {
		$content = $newtext ;
		$content = str_replace ( "\\\"" , "\"" , $content ) ;
		$content = str_replace ( "\\'" , "'" , $content ) ;
		}
	else $content = acquireTopic ( $title ) ;

	$content = str_replace ( "\r" , "" , $content ) ;

	$source = $content ;

	$head = getStandardHeader () ;
	$head .= "<form action=\"$PHP_SELF?title=$title&action=edited\" method=post>\n";

	if ( $content == "" ) $content = "Describe the new page here.\n" ;

	$content =  "<textarea name=newtext rows=20 cols=65 STYLE=\"width:100%\" wrap=virtual>$content</textarea><br>\n" ;

	$content .= "Summary:<INPUT TYPE=text NAME=comment VALUE=\"$comment\" SIZE=60 MAXLENGTH=200><br>\n" ;
	$content .= "<INPUT TYPE=checkbox NAME=\"minor_edit\" VALUE=\"on\">This change is a minor edit.<br>\n" ;
	$content .= "<input type=submit name=save value=\"Save changes\">\n" ;
	$content .= "<input type=submit name=preview value=\"Preview changes\">\n" ;

	$content .= "</form>\n" ;

	if ( $action=="preview" ) {
		$source = parseContent ( $source ) ; 
		$content .= "<hr>\n" ;
		$content .= "<font size=\"+3\">PREVIEW</font><br><br>\n" ;
		$content .= $source ;
		$content .= "\n<hr><b>Remember, this is just a preview!</b>\n" ;
		}

	unset ( $recent_edit ) ;
	unset ( $comment ) ;
	unset ( $newtext ) ;

	$content = $head.$content ;
	return $content ;
	}

function edited () {
	global $action , $preview , $title , $save ;
	global $newtext , $comment , $minor_edit ;

	$cein = canEditInNamespace ( $title ) ;
	if ( $cein != "" ) return $cein ;

	$secureTitle = getSecureTitle ( $title ) ;
	# Checking clearance
	if ( !canEdit($title) ) return "You are not allowed to edit this article. Follow <a href=\"$PHP_SELF?title=$secureTitle\">this link</a> to go back." ;

	if ( doesTopicExist ( $title ) ) {
		# Backup old version
		backupTopic ( $title ) ;
	} else {
		# New topic
		addPlainTopic ( $title ) ;
		}
	
	saveTopic ( $newtext , $comment , $minor_edit ) ;

	unset ( $preview ) ;
	unset ( $newtext ) ;
	unset ( $save ) ;

	$action="view" ;
	$ret="<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$PHP_SELF?title=$title&action=view\">" ;
	return $ret ;
	}

function MySQLtimestamp ( $edit_time ) {
	if ( $edit_time == "" ) return "&lt;unknown&gt;" ;
	$qh = substr ( $edit_time ,  8 , 2 ) ;
	$qm = substr ( $edit_time , 10 , 2 ) ;
	$qs = substr ( $edit_time , 12 , 2 ) ;
	$qo = substr ( $edit_time ,  4 , 2 ) ;
	$qd = substr ( $edit_time ,  6 , 2 ) ;
	$qy = substr ( $edit_time ,  0 , 4 ) ;
	$edit_time = date ( "H:i:s" , mktime ( $qh , $qm , $qs , $qo , $qd , $qy ) ) ;
	return $edit_time ;
	}

function MySQLdatestamp ( $edit_date ) {
	if ( $edit_date == "" ) return "&lt;unknown&gt;" ;
	$qh = substr ( $edit_date ,  8 , 2 ) ;
	$qm = substr ( $edit_date , 10 , 2 ) ;
	$qs = substr ( $edit_date , 12 , 2 ) ;
	$qo = substr ( $edit_date ,  4 , 2 ) ;
	$qd = substr ( $edit_date ,  6 , 2 ) ;
	$qy = substr ( $edit_date ,  0 , 4 ) ;
	$edit_date = date ( "F d, Y" , mktime ( $qh , $qm , $qs , $qo , $qd , $qy ) ) ;
	return $edit_date ;
	}

function currentMySQLtime () {
	return date ( "YmdHis" ) ;
	}

function login () {
	global $USERNAME , $USERPASSWORD , $USERLOGGEDIN , $USER_REMEMBER_PASSWORD ;
	$ret = "<font size=\"+3\">Log in</font><hr>\n" ;
	if ( $USERLOGGEDIN == "YES" ) $ret .= "$USERNAME, you are already logged in!<br>\n" ;
	$ret .= "<FORM action=\"$PHP_SELF?action=loginattempt\" method=post><font face=courier>\n" ;
	$ret .= "Your current user name : <INPUT TABINDEX=1 TYPE=text NAME=user_name VALUE=\"$USERNAME\" SIZE=20><br>\n" ;

	$pwd = $USERPASSWORD ;
	if ( !doesUserExist($USERNAME) ) $pwd = "" ;

	$ret .= "Your current password&nbsp; : <INPUT TABINDEX=2 TYPE=password NAME=user_password VALUE=\"$pwd\" SIZE=20><br>\n" ;
	$ret .= "<INPUT TABINDEX=3 TYPE=checkbox NAME=USER_REMEMBER_PASSWORD value=$USER_REMEMBER_PASSWORD>Remember my password (as a cookie).<br>\n" ;
	$ret .= "<input TABINDEX=4 type=submit name=dologin value=\"Log in\">\n" ;
	$ret .= "</font></FORM>\n" ;
	$ret .= "<hr>Return to the <a href=\"$PHP_SELF?no\">Main Page</a> without logging in" ;

	return $ret ;
	}

function loginattempt () {
	global $user_name , $user_password , $user_remember_password , $newuser ;
	global $USERNAME , $USERPASSWORD , $USERLOGGEDIN , $USER_REMEMBER_PASSWORD ;
	
	if ( $newuser == "YES" and !doesUserExist ( $user_name ) ) {
		addNewUser ( $user_name , $user_password , "" ) ;
		$ret .= "Congratulations, $user_name! You were added to the user list.<br>\n" ;
		$ret .= "Check your preferences <a href=\"$PHP_SELF?action=prefs\">here</a>!<br>\n" ;
		$ret .= "Or go directly to the <a href=\"$PHP_SELF?no\">Main Page</a>.\n" ;
	} else if ( checkUserPassword ( $user_name , $user_password ) ) { # Correct log-in
		setcookie ( "USERNAME" , $user_name ) ;
		setcookie ( "USER_REMEMBER_PASSWORD" , $USER_REMEMBER_PASSWORD ) ;
		if ( $user_remember_password == "on" ) setcookie ( "USERPASSWORD" , $user_password ) ;
		else setcookie ( "USERPASSWORD" , "" ) ;
		setcookie ( "USERLOGGEDIN" , "YES" ) ;
		$USERNAME = $user_name ;
		$ret .= "$USERNAME, you have been successfully logged in!<br>\n" ;
		$ret .= "<hr>Return to the <a href=\"$PHP_SELF?no\">Main Page</a>" ;
	} else { #Wrong log-in
		$ret .= "Sorry, your login was incorrect. You can :<br>\n" ;
		$ret .= "- <a href=\"$PHP_SELF?action=login\">Try again</a>.<br>\n" ;
		$ret .= "- Go to the <a href=\"$PHP_SELF?no\">Main Page</a> without logging in.<br>\n" ;
		if ( !doesUserExist ( $user_name ) ) {
			$ret .= "- Create a new user \"$user_name\", with the password \"$user_password\"." ;
			$ret .= "<FORM action=\"$PHP_SELF?action=loginattempt\" method=post>\n" ;
			$ret .= "<input type=submit name=createnewuser value=\"Create user $user_name\">\n" ;
			$ret .= "<INPUT TYPE=HIDDEN NAME=user_name VALUE=\"$user_name\">\n" ;
			$ret .= "<INPUT TYPE=HIDDEN NAME=user_password VALUE=\"$user_password\">\n" ;
			$ret .= "<INPUT TYPE=HIDDEN NAME=newuser VALUE=\"YES\">\n" ;
			$ret .= "</FORM>\n" ;
			}
		}
	
	unset ( $newuser ) ;
	return $ret ;
	}

function logout () {
	global $USERNAME , $USERPASSWORD , $USERLOGGEDIN , $USERID ;
	setcookie ( "USERLOGGEDIN" , "NO" ) ;
	$ret = "<font size=\"+3\">Goodbye, $USERNAME!</font><br>\n" ;
	$ret .= "Return to the <a href=\"$PHP_SELF?no\">Main Page</a>" ;
	return $ret ;
	}
?>