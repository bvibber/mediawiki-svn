<?
############################################
# HIGHER BRAIN FUNCTIONS
############################################

function showRecentChanges () {
	global $title , $countDays ;
	$s=getSecureTitle($s);
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT * FROM cur ORDER BY cur_timestamp DESC LIMIT 1000" ;
	$result = mysql_query ( $sql , $connection ) ;

	$output .= getStandardHeader () ;
	$last_date = "" ;
	if ( $countDays != "" ) $count_days = $countDays ;
	else $count_days = 2 ;

	if ( $count_days < 1 ) $count_days = 2 ;

	$output .= "<h2>Updates in the last $count_days day" ;
	if ( $count_days != 1 ) $output .= "s" ;
	$output .= " :</h2>\n" ;
	$output .= "<a href=\"$PHP_SELF?title=recentchanges&action=view&countDays=1\">1 day</a>" ;
	$output .= " | <a href=\"$PHP_SELF?title=recentchanges&action=view&countDays=3\">3 days</a>" ;
	$output .= " | <a href=\"$PHP_SELF?title=recentchanges&action=view&countDays=30\">30 days</a>" ;
	$output .= " | <a href=\"$PHP_SELF?title=recentchanges&action=view&countDays=90\">90 days</a><br>\n" ;
	
	$s = currentMySQLtime () ;
	$s = MySQLdatestamp ( $s ) . ", " . MySQLtimestamp ( $s ) ;
	$output .= "Now is $s" ;

	while ( $s = mysql_fetch_object ( $result ) ) {
		$secureTitle=getSecureTitle($s->cur_title);
		$edit_time = MySQLtimestamp ( $s->cur_timestamp ) ;
		$edit_date = MySQLdatestamp ( $s->cur_timestamp ) ;
		$comment=$s->cur_comment ;
		if ( $comment == "*" ) $comment = "" ;
		if ( $s->cur_minor_edit == 1 and $comment != "" ) $comment = "<i>edit</i> ".$comment ;
		$cuser=$s->cur_user_text ;
		if ( $cuser == "" ) $cuser = "&lt;unknown&gt;" ;
		else if ( doesUserExist ( $cuser ) ) $cuser = "<a href=\"$PHP_SELF?title=user:$cuser&action=view\">$cuser</a>" ;

		$toutput = "" ;
		if ( $last_date != $edit_date ) {
			$count_days-- ;
			$toutput .= "</ul><h3>$edit_date</h3>\n<ul>\n" ;
			$last_date = $edit_date ;
			}
		
		$toutput .= "<li><a href=\"$PHP_SELF?title=$secureTitle&action=view\">$s->cur_title</a>" ;
		$toutput .= " $edit_time" ;
		$toutput .= " (<a href=\"$PHP_SELF?title=$secureTitle&action=revisions\">history</a>)" ;
		if ( $comment != "" ) $toutput .= " <strong>$comment</strong>" ;
		$toutput .= "...... $cuser" ;
		$toutput .= "</li>\n" ;

		if ( $count_days >= 0 ) $output .= $toutput ;
		}
	$output .= "</ul>" ;
	$output .= getStandardFooter () ;
	mysql_free_result ( $result ) ;
#	mysql_close ( $connection ) ;
	return $output ;
	}

function revisions () {
	global $title ;
	if ( !doesTopicExist ( $title ) ) return "There is no topic $title." ;
	
	$ret .= getStandardHeader () ;

	$secureTitle=getSecureTitle($title);
#	$s=strtolower($secureTitle);
	$stitle=$secureTitle ;
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "select * from cur where cur_title=\"$stitle\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$id = $s->cur_id ;
	$next = $s->cur_old_version ;
	$comment = $s->cur_comment ;
	$user_text = $s->cur_user_text ;
	$edit_time = $s->cur_timestamp ;
	$member_id = $s->cur_user ;
	$release = "current" ;

	$sql = "select * from old where old_title=\"$stitle\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$revs = 0 ;
	while ( $s = mysql_fetch_object ( $result ) ) $revs++ ;

	$ret .= "<table width=\"100%\" border=1>\n" ;
	$ret .= "<tr><th nowrap width=60><center><b>Version</b></center></th>";
	$ret .= "<th nowrap width=10><b>Source</b></th>" ;
	$ret .= "<th nowrap width=10><b>User</b></th>" ;
	$ret .= "<th nowrap width=10><b>Time</b></th>" ;
	$ret .= "<th nowrap width=\"100%\"><b>Comment</b></th>" ;
	$ret .= "</tr>\n" ;
	do {
		$oid = $next ;
		if ( $release == "current" ) $oid = $release ;

		$edit_time = MySQLtimestamp ( $edit_time ) ;

		if ( $user_text == "" ) $user_text = "&lt;unknown&gt;" ;
		if ( $member_id != "" ) $user_text = "<a href=\"$PHP_SELF?action=view&title=user:$user_text\">$user_text</a>" ;

		if ( $release == "current" ) $releaseText = $release ;
		else $releaseText = $revs + 1 - $release ;

		$ret .= "<tr>" ;
		$ret .= "<td align=center nowrap><a href=\"$PHP_SELF?title=$title&action=view_old_article&oid=$oid&whichOldVersion=$releaseText\">$releaseText</a></td>";
		$ret .= "<td align=center nowrap><a href=\"$PHP_SELF?title=$title&action=view_old_source&oid=$oid&whichOldVersion=$releaseText\">edit</a></td>";
		$ret .= "<td nowrap>$user_text</td>" ;
		$ret .= "<td nowrap>$edit_time</td>" ;
		$ret .= "<td>$comment</td>" ;
		$ret .= "</tr>\n" ;

		if ( $release != "current" ) $next = $s->old_old_version ;
		if ( $release == "current" ) $release = 0 ;
		$release = $release + 1 ;
		if ( $next != 0 ) {
			mysql_free_result ( $result ) ;
			$sql = "select * from old where old_id=$next" ;
			$result = mysql_query ( $sql , $connection ) ;
			$s = mysql_fetch_object ( $result ) ;
			$comment = $s->old_comment ;
			$member_id = $s->old_user ;
			$user_text = $s->old_user_text ;
			$edit_time = $s->old_timestamp ;
			}
		} while ( $next != 0 ) ;
	$ret .= "</table>\n" ;
	$ret .= getStandardFooter () ;

#	mysql_close ( $connection ) ;
	return $ret ;
	}

function doSearch () {
	global $search ;

	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT * FROM cur WHERE cur_text LIKE \"%$search%\" OR cur_title LIKE \"%$search%\" ORDER BY cur_title" ;
	$result = mysql_query ( $sql , $connection ) ;

	$noresult = true ;
	$output = "<table border=1>\n" ;
	$output .= "<tr><th nowrap>Topic</th><th nowrap>Last edit</th><th>Last comment</th></tr>";
	while ( $s = mysql_fetch_object ( $result ) ) {
		$noresult = false ;
		$secureTitle=getSecureTitle($s->cur_title);
		$edit_time = MySQLtimestamp ( $s->cur_timestamp ) ;
		$comment=$s->cur_comment ;
		if ( $comment == "*" ) $comment = "" ;
		if ( $s->cur_minor_edit == 1 ) $comment = "<i>edit</i> ".$comment ;
		$cuser=$s->cur_user_text ;
		if ( $cuser == "" ) $cuser = "&lt;unknown&gt;" ;
		else $cuser = "<a href=\"$PHP_SELF?title=$cuser&action=view\">$cuser</a>" ;

		$output .= "<tr>" ;
		$output .= "<td nowrap><a href=\"$PHP_SELF?title=$secureTitle&action=view\"><b>$s->cur_title</b></a>";
		$output .= " (<a href=\"$PHP_SELF?title=$secureTitle&action=revisions\">History</a>)</td>" ;
		$output .= "<td nowrap><font color=green>$edit_time</font> by $cuser</td>" ;
		$output .= "<td>$comment</td>" ;
		$output .= "</tr>\n" ;
	}
	$output .= "</table>\n" ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;

	if ( $noresult ) {
		$output = "<h2>Sorry, there are no matches for \"$search\" in our database.</h2>" ;
		$output .= "You could write about [[$search]] yourself!" ;
		$output = parseContent ( $output ) ;
		}

	$output = getStandardHeader().$output.getStandardFooter () ;
	return $output ;
	}

function prefs () {
	global $changeprefs , $u_email , $u_password ;
	global $USERNAME , $USERPASSWORD , $USERLOGGEDIN , $USERID ;
	if ( $USERLOGGEDIN != "YES" ) return "You are not logged in. <a href=\"$PHP_SELF?action=login\">Log in</a> or return to the <a href=\"$PHP_SELF?no\">Main Page</a>" ;
	$ret = getStandardHeader () ;

	if ( $changeprefs ) { # Save new settings
		changeUserSetting ( $USERNAME , "user_email" , $u_email ) ;
		changeUserSetting ( $USERNAME , "user_password" , $u_password ) ;
		$ret .= "Settings are changed.<br>\n" ;
		}

	$uemail = getUserSetting ( $USERNAME , "user_email" ) ;
	$ur = getUserSetting ( $USERNAME , "user_rights" ) ;
	$ret .= "<font face=courier>\n" ;
	$ret .= "<FORM action=\"$PHP_SELF?action=prefs\" method=post>\n" ;
	$ret .= "<p>Your user ID&nbsp; : $USERID</p>\n" ;
	$ret .= "<p>Your rights&nbsp;&nbsp; : $ur</p>\n" ;
	$ret .= "<p>Your email&nbsp;&nbsp;&nbsp; : <INPUT TABINDEX=1 TYPE=text NAME=u_email VALUE=\"$uemail\" SIZE=20></p>\n" ;
	$ret .= "<p>Your password : <INPUT TABINDEX=1 TYPE=text NAME=u_password VALUE=\"$USERPASSWORD\" SIZE=20></p>\n" ;
	$ret .= "<INPUT TYPE=SUBMIT NAME=changeprefs value=\"Save settings\">\n" ;
	$ret .= "</FORM>\n" ;
	$ret .= "</font>\n" ;

	$rights = ",".getUserSetting ( $USERNAME , "user_rights" )."," ;
	if ( strstr ( $rights , ",is_editor" ) or strstr ( $rights , ",is_sysop" ) ) {
		$ret .= "<hr><font color=red>You are allowed to <a href=\"$PHP_SELF?action=editUserRights\">edit user rights</a>!</font>" ;
		}

	$ret .= getStandardFooter () ;

	return $ret ;
	}

######## EDIT USER RIGHTS
function editUserRights () {
	global $title , $editusername , $newuserrights , $USERLOGGEDIN , $USERNAME ;
	$secureTitle = getSecureTitle ( $title ) ;
	if ( !$USERLOGGEDIN ) return "You are not logged in. <a href=\"$PHP_SELF?action=login\">Log in</a> or return to the <a href=\"$PHP_SELF?no\">Main Page</a>" ;

	# AUTHENTIFICATION
	$rights = ",".getUserSetting ( $USERNAME , "user_rights" )."," ;
	if ( strstr ( $rights , ",is_editor," ) or strstr ( $rights , ",is_sysop" ) ) $isEditor = true ;
	else $isEditor = false ;
	if ( strstr ( $rights , ",is_sysop," ) or strstr ( $rights , ",is_sysop" ) ) $isSysop = true ;
	else $isSysop = false ;
	if ( !$isSysop and !$isEditor ) return "You are neither an editor nor a sysop. Return to the <a href=\"$PHP_SELF?no\">Main Page</a>" ;

	$ret = "" ;
	if ( isset ( $editusername ) ) {
		if ( isset ( $newuserrights ) ) {
			changeUserSetting ( $editusername , "user_rights" , $newuserrights ) ;
			$ret="<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$PHP_SELF?action=prefs\">" ;
		} else {
		$ret .= getStandardHeader () ;
		$ret .= "<font size=\"+2\">Editing rights of user $editusername</font><br>" ;
		$r = getUserSetting ( $editusername , "user_rights" ) ;
		$ret .= "<FORM action=\"$PHP_SELF?action=editUserRights&editusername=$editusername\" method=post>\n" ;
		$ret .= "User rights : <INPUT TABINDEX=1 TYPE=text NAME=newuserrights VALUE=\"$r\" SIZE=80><br>\n" ;
		$ret .= "<INPUT TYPE=SUBMIT NAME=changeprefs value=\"Save new user rights\">\n" ;
		$ret .= "</FORM>\n" ;
			}
		unset ( $editusername ) ;
		unset ( $newuserrights ) ;
	} else {
		$ret .= getStandardHeader () ;
		$connection=getDBconnection() ;
		mysql_select_db ( "nikipedia" , $connection ) ;
		$sql = "SELECT * FROM user" ;
		if ( !$isSysop ) $sql .= " WHERE user_rights NOT LIKE \"is_sysop\"" ;
		$sql .= " ORDER BY user_name" ;
		$result = mysql_query ( $sql , $connection ) ;
		while ( $s = mysql_fetch_object ( $result ) ) {
			$t = $s->user_name ;
			$t = "<a href=\"$PHP_SELF?action=editUserRights&editusername=$t\">$t</a>" ;
			$ret .= "Edit the rights of $t ($s->user_rights)<br>\n" ;
			}
		mysql_free_result ( $result ) ;
		mysql_close ( $connection ) ;
		$ret .= getStandardFooter () ;
		}

	return $ret ;
	}


function statistics () {
	$ret = getStandardHeader () ;
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$ret = getStandardHeader() ;
	$ret .= "<h2>Article statistics</h2><ul>" ;

	$nf1 = "<font color=red><b>" ;
	$nf2 = "</b></font>" ;

	# TOTAL	
	$sql = "SELECT COUNT(*) AS number FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$totalPages = $s->number ;
	$ret .= "<li>There are $nf1$totalPages$nf2 pages in the database</li>" ;
	mysql_free_result ( $result ) ;

	# /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title LIKE \"%/Talk\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$talkPages = $s->number ;
	$ret .= "<li>There are $nf1$talkPages$nf2 <b>/Talk</b> pages</li>" ;
	mysql_free_result ( $result ) ;

	# , NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_text LIKE \"%,%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$commaPages = $s->number ;
	$ret .= "<li>There are $nf1$commaPages$nf2 with a comma that are <i>not</i> <b>/Talk</b> pages</li>" ;
	mysql_free_result ( $result ) ;

	# WIKIPEDIA NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title LIKE \"%ikipedia%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$wikiPages = $s->number ;
	$ret .= "<li>There are $nf1$wikiPages$nf2 that have \"ikipedia\" in the title and are <i>not</i> <b>/Talk</b> pages</li>" ;
	mysql_free_result ( $result ) ;

	# WIKIPEDIA NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title LIKE \"%/%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$subPages = $s->number - $talkPages;
	$ret .= "<li>There are $nf1$subPages$nf2 subpages that are <i>not</i> <b>/Talk</b> pages</li>" ;
	mysql_free_result ( $result ) ;

	# RESULT
	$x = $commaPages - $wikiPages ; # Comma (no /Talk) - wiki pages = articles, including subpages
	$ret .= "<li>That means there are about $nf1$x$nf2 articles, including subpages (except <b>/Talk</b>).</li>" ;
	$y = $x - $subPages ;
	$ret .= "<li>Or, there are about $nf1$y$nf2 articles, not counting any subpages!</li>" ;
	$z = $totalPages - $talkPages - $commaPages ;
	$ret .= "<li>Finally, there are about $nf1$z$nf2 junk pages :-(</li>" ;

	# OLD PAGES
	$sql = "SELECT COUNT(*) as number FROM old" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$oldPages = $s->number - $talkPages;
	$p = round ( $oldPages / $totalPages , 2 ) ;
	$ret .= "<li>And, there are $nf1$oldPages$nf2 old page versions in the database, giving an average of $p old pages on every active page.</li>" ;
	mysql_free_result ( $result ) ;


	$ret .= "</ul><hr>" ;
	$ret .= "<h2>User statistics</h2><ul>" ;
	
	# USERS
	$sql = "SELECT COUNT(*) as number FROM user" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$numUser = $s->number ;
	$ret .= "<li>There are currently $nf1$numUser$nf2 users signed up.</li>" ;
	mysql_free_result ( $result ) ;
	
	# EDITORS AND SYSOPS
	$sql = "SELECT COUNT(*) as number FROM user WHERE user_rights LIKE \"%is_editor%\" OR user_rights LIKE \"%is_sysop%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$numEditors = $s->number ;
	$ret .= "<li>$nf1$numEditors$nf2 of them have sysop status.</li>" ;
	mysql_free_result ( $result ) ;

	mysql_close ( $connection ) ;
	$ret .= "</ul>" ;
	$ret .= getStandardFooter () ;
	return $ret ;
	}

# RANDOM PAGE
function randomPage () {
	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT COUNT(*) AS number FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	mt_srand((double)microtime()*1000000);
	$randval = mt_rand(0,$s->number-1);
	mysql_free_result ( $result ) ;

	$sql = "SELECT * FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $randval > 0 ) {
		$s = mysql_fetch_object ( $result ) ;
		$randval-- ;
		}
	$thelink = $s->cur_title ;
	$ret = "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$PHP_SELF?title=$thelink&action=view\">" ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;

	return $ret ;
	}

function pageIndex () {
	global $search , $action , $dosearch ;
	$action = "search" ;
	$dosearch = 1 ;
	$search = "" ;
	return doSearch () ;
	}

function listLinks ( $s ) {
	global $title , $allTopics , $allTopicsKeys ;
	$ns = getNamespace ( $title ) ;
	$rn = stripNamespace ( $title ) ;
	$rna = explode ( "/" , $rn."/" ) ;
	$rn = $rna[0] ;
	if ( $ns != "" ) $ns .= ":" ;
	$tag1 = "[[" ;
	$tag2 = "]]" ;
	$e1 = explode ( $tag1 , $s ) ;
	foreach ( $e1 as $x ) {
		$e2 = explode ( $tag2 , $x , 2 ) ;
		if ( count ( $e2 ) == 2 ) {
			$y = $e2[0] ;
			$ya = explode ( "|" , $y."|" ) ;
			$y = $ya[0] ;
			if ( substr($y,0,1) == "/" ) $y = $rn.$y ;
			if ( getnamespace ( $y ) == "" ) $y = $ns.$y ;
			$y = getSecureTitle ( $y ) ;
#			if ( !in_array ( $y , $allTopicsKeys ) ) {
#				$allTopics[$y] = 0 ;
#				array_push ( $allTopicsKeys , $y ) ;
#				}
			$allTopics[$y]++ ;
			}
		}
	}

function demanded_topics () {
	global $title , $xtitle ;
	global $allLinkedTopics , $allUnlinkedTopics , $allTopics, $allTopicsKeys  ;
	$title = "" ;
	$xtitle = "Demanded topics" ;
	$ret = getStandardHeader () ;
	$allUnlinkedTopics = array () ;
	$allLinkedTopics = array () ;
	$allTopics = array () ;
	$allTopicsKeys = array () ;


	$connection=getDBconnection() ;
	mysql_select_db ( "nikipedia" , $connection ) ;
	$sql = "SELECT cur_title FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) )
		array_push ( $allTopics , $s->cur_title ) ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;

	foreach ( $allTopics as $x ) {
		$title=$x ;
		listLinks ( acquireTopic ( $title ) ) ;
		}

	foreach ( $allTopics as $y ) {
		$x = key($allTopics) ;
		if ( !doesTopicExist($x) )
			$allUnlinkedTopics[$x]  = $y ;
		next($allTopics);
		}

	arsort ( $allUnlinkedTopics ) ;
	foreach ( $allUnlinkedTopics as $y ) {
		$x = key($allUnlinkedTopics) ;
		$x = ucfirst ( getNiceTitle ( $x ) ) ;
		if ( $x != "" ) $ret .= "$x:$y<br>\n" ;
		next($allUnlinkedTopics);
		}

	$title = "" ;
	return $ret.getStandardFooter () ;
	}

function special_pages () {
	global $xtitle , $title ;
	$title = "" ;
	$xtitle = "Special pages" ;
	$ret = getStandardHeader () ;
	$ret .= "This is a list of wikipedia pages with special functions.\n<ul>\n" ;
	$ret .= "<li><a href=\"$PHPSELF?action=statistics\">Up-to-the-minute statistics</a></li>\n" ;
	$ret .= "<li><a href=\"$PHPSELF?action=view&title=Random_Page\">A random page</a></li>\n" ;
	$ret .= "<li><a href=\"$PHPSELF?action=view&title=Page_Index\">The index of all pages</a></li>\n" ;
	$ret .= "<li><a href=\"$PHPSELF?action=upload\">Upload files</a></li>\n" ;
	$ret .= "<li><a href=\"$PHPSELF?action=view&title=recentchanges\">Recent changes</a></li>\n" ;
	$ret .= "<li><a href=\"$PHPSELF?action=demanded_topics\">Demanded topics</a></li>\n" ;
#	$ret .= "<li><a href=\"$PHPSELF?action=\"></a></li>\n" ;
	return $ret."</ul>".getStandardFooter();
	}
?>