<?
# This contains special functions that are not necessare for basic view/edit purposes
# - userLogout
# - userLogin
# - editUserSettings
# - WantedPages (the most wanted)
# - shortPages (stub articles)
# - lonelyPages (orphans)
# - watchlist
# - recentChanges
# - randomPage
# - allPages
# - search
# - specialPages (the list)
# - history
# - upload
# - statistics
# - delete (a page; for sysops only!)
# - askSQL (for sysops only!)
# and many others...

function userLogout () {
	global $user , $vpage ;
	$vpage->title = "User logout" ;
	setcookie ( "WikiLoggedIn" , "" , time()-3600 ) ;
	if ( $user->options["rememberPassword"] != "on" ) setcookie ( "WikiUserPassword" , "" , time()-3600 ) ;
	$user->isLoggedIn = false ;
	global $wikiGoodbye ;
	return str_replace ( "$1" , $user->name , $wikiGoodbye ) ;
	}

function userLogin () {
	global $WikiUserPassword , $WikiLoggedIn ;
	global $loginattempt , $user , $vpage , $WikiUserID , $expiration , $wikiLogIn ;
	global $wikiYourName , $wikiYourPassword , $wikiYourPasswordAgain , $wikiNewUsersOnly , $wikiRememberMyPassword , $wikiLoginProblem , $wikiLoginPageTitle ;
	$vpage->title = $wikiLoginPageTitle ;

	if ( isset ( $loginattempt ) ) {
		unset ( $loginattempt ) ;
		global $USERNAME , $USERPASSWORD , $RETYPE , $REMEMBERPASSWORD ;

		# Language recode
		$USERNAME = wikiRecodeInput ( $USERNAME ) ;
		$USERPASSWORD = wikiRecodeInput ( $USERPASSWORD ) ;
		$RETYPE = wikiRecodeInput ( $RETYPE ) ;

		if ( $REMEMBERPASSWORD == "" ) $REMEMBERPASSWORD = "off" ;
		$nu = new WikiUser ;
		$nu->name = $USERNAME ;
		$nu->password = $USERPASSWORD ;
		$nu->options["rememberPassword"] = $REMEMBERPASSWORD ;
		$nu->retypePassword = $RETYPE ;

		$s = "<h1>".$nu->verify()."</h1>" ;
		if ( $nu->isLoggedIn ) {
			$user = new WikiUser ;
			$user = $nu ;
			setcookie ( "WikiUserID" , $user->id , $expiration ) ;
			setcookie ( "WikiLoggedIn" , "yes" , $expiration ) ;
			if ( $user->options["rememberPassword"] == "on" ) setcookie ( "WikiUserPassword" , $user->password , $expiration ) ;
			$user->options["rememberPassword"] = $REMEMBERPASSWORD ;
			$user->saveSettings() ;
		} else if ( $USERPASSWORD == $RETYPE and !($nu->doesUserExist()) ) {
			$user = new wikiUser ;
			$nu->name = ucfirst ( $nu->name ) ;
			$nu->addToDatabase () ;
			$user = $nu ;
			global $wikiWelcomeCreation ;
			$s = str_replace ( "$1" , $user->name , $wikiWelcomeCreation ) ;
			setcookie ( "WikiLoggedIn" , "yes" , $expiration ) ;
			setcookie ( "WikiUserID" , $user->id , $expiration ) ;
			if ( $user->options["rememberPassword"] == "on" ) setcookie ( "WikiUserPassword" , $user->password , $expiration ) ;
			$user->options["rememberPassword"] = $REMEMBERPASSWORD ;
			$user->saveSettings() ;

		if ( $user->options["rememberPassword"] == "on" ) $check = "checked" ;
	  	$s .= "<FORM action=\"".wikiLink("special:userLogin")."\" method=post><font face=courier>\n" ;
	  	$s .= "$wikiYourName<INPUT TABINDEX=1 TYPE=text NAME=USERNAME VALUE=\"$user->name\" SIZE=20><br>\n" ;
	  	$s .= "$wikiYourPassword<INPUT TABINDEX=2 TYPE=password NAME=USERPASSWORD VALUE=\"$user->password\" SIZE=20><br>\n" ;
  		$s .= "<INPUT TABINDEX=4 TYPE=checkbox NAME=REMEMBERPASSWORD $check>$wikiRememberMyPassword<br>\n" ;
	  	$s .= "<input TABINDEX=5 type=submit name=loginattempt value=\"$wikiLogIn\">\n" ;
  		$s .= "</font></FORM>\n" ;

		} else {
			$s .= $wikiLoginProblem ;
			}
	} else {
		global $wikiAlreadyLoggedIn , $wikiPleaseLogIn , $wikiAreYouNew ;
		$s = "" ;
		if ( $user->isLoggedIn ) $s .= str_replace ( "$" , $user->name , $wikiAlreadyLoggedIn ) ;
	  	$s .= $wikiPleaseLogIn ;
		global $WikiUserID , $WikiUserPassword , $WikiLoggedIn ;
		if ( $WikiUserID != "" ) {
			$user->name = getMySQL ( "user" , "user_name" , "user_id=$WikiUserID" ) ;
			$u = new WikiUser ;
			$u->id = $WikiUserID ;
			$u->isLoggedIn = true ;
			$u->loadSettings() ;
			$user->options["rememberPassword"] = $u->options["rememberPassword"] ;
			if ( $user->options["rememberPassword"] == "on" )
				$user->password = $WikiUserPassword ;
			}
		if ( $user->options["rememberPassword"] == "on" ) $check = "checked" ;
		$s .= $wikiAreYouNew ;
	  	$s .= "<FORM action=\"".wikiLink("special:userLogin")."\" method=post><font face=courier>\n" ;
	  	$s .= "$wikiYourName<INPUT TABINDEX=1 TYPE=text NAME=USERNAME VALUE=\"$user->name\" SIZE=20><br>\n" ;
	  	$s .= "$wikiYourPassword<INPUT TABINDEX=2 TYPE=password NAME=USERPASSWORD VALUE=\"$user->password\" SIZE=20><br>\n" ;
	  	$s .= "$wikiYourPasswordAgain<INPUT TABINDEX=2 TYPE=password NAME=RETYPE VALUE=\"\" SIZE=20>$wikiNewUsersOnly<br>\n" ;
  		$s .= "<INPUT TABINDEX=4 TYPE=checkbox NAME=REMEMBERPASSWORD $check>$wikiRememberMyPassword<br>\n" ;
	  	$s .= "<input TABINDEX=5 type=submit name=loginattempt value=\"$wikiLogIn\">\n" ;
  		$s .= "</font></FORM>\n" ;
		}

	return $s ;
	}

function editUserSettings () {
	global $ButtonSave ;
	global $vpage , $user ;
	global $wikiUserSettings , $wikiUserSettingsError , $wikiUserSettingsSaved ;
	$vpage->title = $wikiUserSettings ;
	if ( !$user->isLoggedIn ) return $wikiUserSettingsError ;
	$ret = "" ;

	if ( isset ( $ButtonSave ) ) {
		unset ( $ButtonSave ) ;
		global $QuickBar , $NewTopics , $UnderlineLinks , $ShowHover , $ROWS , $COLS , $doSkin ;
		global $OLDPASSWORD , $NEWPASSWORD , $RETYPEPASSWORD , $EMAIL , $RESULTSPERPAGE , $doJustify , $ChangesLayout ;
		global $SHOWSTRUCTURE , $HOURDIFF , $NumberHeadings , $ViewFrames , $encoding , $HideMinor ;
		if ( $RESULTSPERPAGE < 2 ) $RESULTSPERPAGE = 20 ;

		# Checkbox fixing
		if ( $ShowHover == "" ) $ShowHover = "no" ;
		if ( $UnderlineLinks == "" ) $UnderlineLinks = "no" ;
		if ( $NewTopics == "" ) $NewTopics = "normal" ;
		if ( $doJustify == "" ) $doJustify = "no" ;
		if ( $ChangesLayout == "" ) $ChangesLayout = "classic" ;
		if ( $NumberHeadings == "" ) $NumberHeadings = "no" ;

		$user->options["quickBar"] = $QuickBar ;
		$user->options["markupNewTopics"] = $NewTopics ;
		$user->options["underlineLinks"] = $UnderlineLinks ;
		$user->options["viewFrames"] = $ViewFrames ;
		$user->options["showHover"] = $ShowHover ;
		$user->options["cols"] = $COLS ;
		$user->options["rows"] = $ROWS ;
		$user->options["justify"] = $doJustify ;
		$user->options["resultsPerPage"] = $RESULTSPERPAGE ;
		$user->options["skin"] = $doSkin ;
#		$user->options["showStructure"] = $SHOWSTRUCTURE ;
		$user->options["showStructure"] = "no" ; #Subpages turned off
		$user->options["numberHeadings"] = $NumberHeadings ;
		$user->options["changesLayout"] = $ChangesLayout ;
		$user->options["hideMinor"] = $HideMinor ;
		$user->email = $EMAIL ;
		$user->options["hourDiff"] = $HOURDIFF ;
		$user->options["encoding"] = $encoding ;

		if ( $OLDPASSWORD == $user->password ) {
			global $wikiUserSettingsNewPasswordError ;
			if ( $NEWPASSWORD == $RETYPEPASSWORD ) $user->password = $NEWPASSWORD ;
			else $ret .= $wikiUserSettingsNewPasswordError ;
			}

		$user->saveSettings () ;
		$user->loadSettings () ;
		$msg = $wikiUserSettingsSaved ;
		}

	global $wikiLoggedInAs , $wikiID_Help ;
	global $wikiQuickBarSettings , $wikiSettingsNone , $wikiSettingsStandard , $wikiSettingsLeft , $wikiSettingsRight ;
	global $wikiOldPassword , $wikiNewPassword , $wikiYourPasswordAgain , $wikiSkin , $wikiStarTrek ;
	global $wikiShowHoverBox , $wikiUnderlineLinks , $wikiNewTopicsRed , $wikiJustifyParagraphs , $wikiShowRecentChangesTable ;
	global $wikiDoNumberHeadings , $wikiViewWithFrames , $wikiTurnedOn , $wikiTurnedOff ;
	global $wikiTextboxDimensions , $wikiCols , $wikiRows , $wikiYourEmail , $wikiResultsPerPage , $wikiTimeDiff , $wikiSave , $wikiReset ;
	global $wikiEncodingNames, $wikiOutputEncoding , $wikiHideMinorEdits ;

	$ret .= str_replace ( "$1" , $user->name , $wikiLoggedInAs ) ;
	$ret .= str_replace ( "$1" , $user->id , $wikiID_Help)."\n" ;
	$ret .= "<nowiki><FORM action=\"".wikiLink("special:editUserSettings")."\" method=post>" ;
	$ret .= "<table border=1 bordercolor=".$user->options["borderColor"]." cellspacing=0 cellpadding=2>" ;

	# QuickBar options
	$qb[$user->options["quickBar"]] = "checked" ;
	$ret .= "<tr><td valign=top nowrap><b>$wikiQuickBarSettings</b><br>\n" ;
	$ret .= "<input type=radio value=none ".$qb["none"]." name=QuickBar>$wikiSettingsNone ($wikiSettingsStandard)<br>\n" ;
	$ret .= "<input type=radio value=left ".$qb["left"]." name=QuickBar>$wikiSettingsLeft<br>\n" ;
	$ret .= "<input type=radio value=right ".$qb["right"]." name=QuickBar>$wikiSettingsRight\n" ;

	# Password change
	$ret .= "</td><td valign=top nowrap><b>Change password :</b><br><font face=courier>\n" ;
  	$ret .= "$wikiOldPassword<INPUT TYPE=password NAME=OLDPASSWORD VALUE=\"\" SIZE=20><br>\n" ;
  	$ret .= "$wikiNewPassword<INPUT TYPE=password NAME=NEWPASSWORD VALUE=\"\" SIZE=20><br>\n" ;
  	$ret .= "$wikiYourPasswordAgain<INPUT TYPE=password NAME=RETYPEPASSWORD VALUE=\"\" SIZE=20>\n" ;
	$ret .= "</font></td></tr>" ;

	# Skin
	$sk[$user->options["skin"]] = "checked" ;
	$ret .= "<tr><td valign=top nowrap><b>$wikiSkin</b><br>\n" ;
	$ret .= "<input type=radio value=None ".$sk["None"]." name=doSkin>$wikiSettingsNone ($wikiSettingsStandard)<br>\n" ;
	$ret .= "<input type=radio value=\"Star Trek\" ".$sk["Star Trek"]." name=doSkin>$wikiStarTrek<br>\n" ;

#----------------------------------------------
	$ret .= "<td valign=top nowrap>" ;

	# Show Hover
	$sh[$user->options["showHover"]] = "CHECKED" ;
	$ret .= "<input type=checkbox value=yes name=ShowHover ".$sh["yes"].">" ;
	$ret .= "$wikiShowHoverBox ($wikiSettingsStandard:$wikiTurnedOn)<br>\n" ;

	# Underline Links
	$ul[$user->options["underlineLinks"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=UnderlineLinks ".$ul["yes"].">" ;
	$ret .= "$wikiUnderlineLinks ($wikiSettingsStandard:$wikiTurnedOn)<br>\n" ;

	# New topics
	$nt[$user->options["markupNewTopics"]] = "checked" ;
	$ret .= "<input type=checkbox value=red name=NewTopics ".$nt["red"].">" ;
	$ret .= "$wikiNewTopicsRed ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# Justify paragraphs
	$jf[$user->options["justify"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=doJustify ".$jf["yes"].">" ;
	$ret .= "$wikiJustifyParagraphs ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# Recent changes layout
	$cl[$user->options["changesLayout"]] = "checked" ;
	$ret .= "<input type=checkbox value=table name=ChangesLayout ".$cl["table"].">" ;
	$ret .= "$wikiShowRecentChangesTable ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# Hide minor edits
	$hm[$user->options["hideMinor"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=HideMinor ".$hm["yes"].">" ;
	$ret .= "$wikiHideMinorEdits ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# Auto number headings
	$nh[$user->options["numberHeadings"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=NumberHeadings ".$nh["yes"].">" ;
	$ret .= "$wikiDoNumberHeadings ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# View frames
	$vf[$user->options["viewFrames"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=ViewFrames ".$vf["yes"].">" ;
	$ret .= "$wikiViewWithFrames ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	$ret .= "</td></tr>" ;
#----------------------------------------------

	# Cols and rows
	$ret .= "<tr><td valign=top nowrap><b>$wikiTextboxDimensions</b><br><font face=courier>\n" ;
  	$ret .= "$wikiCols<INPUT TYPE=text NAME=COLS VALUE=\"".$user->options["cols"]."\" SIZE=5><br>\n" ;
  	$ret .= "$wikiRows<INPUT TYPE=text NAME=ROWS VALUE=\"".$user->options["rows"]."\" SIZE=5></font><br><br>\n" ;
#	$ret .= "<font face=courier size=-1>Recommended sizes:<br>1280x1024 : 135x40<br>1024x 768 : <br>&nbsp;800x 600 : </font>" ;

	$ret .= "</td><td valign=top nowrap>" ;
  	$ret .= "<font face=courier>$wikiYourEmail</font><INPUT TYPE=text NAME=EMAIL VALUE=\"".$user->email."\" SIZE=25><br>\n" ;
	$n = explode ( "$1" , $wikiResultsPerPage ) ;
	$ret .= "<font face=courier>".$n[0]."</font><INPUT TYPE=text NAME=RESULTSPERPAGE VALUE=\"".$user->options["resultsPerPage"]."\" SIZE=4>".$n[1]."<br>\n" ;
	$n = explode ( "$1" , $wikiTimeDiff ) ;
  	$ret .= "<font face=courier>".$n[0]."</font><INPUT TYPE=text NAME=HOURDIFF VALUE=\"".$user->options["hourDiff"]."\" SIZE=4>".$n[1]."\n" ;

	# Encoding
	if(count($wikiEncodingNames) > 1) {
		$ret .= "<br><font face=courier>$wikiOutputEncoding</font><select name=encoding>\n";
		reset($wikiEncodingNames);
		while(list($i, $enc) = each($wikiEncodingNames))
			$ret .= "<option value=\"$i\"".(($user->options["encoding"] == $i)?" selected":"").">$enc</option>\n";
		$ret .= "</select>\n";
		}

	$ret .= "</td></tr>" ;

	$ret .= "<tr><td><center><input type=submit value=\"$wikiSave\" name=ButtonSave></center></td>" ;
	$ret .= "<td><center><input type=reset value=\"$wikiReset\" name=ButtonReset></center></td></tr>" ;

	$ret .= "</table></FORM>$msg</nowiki>" ;
	return $ret ;
	}

function refreshWantedPages () {
	global $showNumberPages , $linkedLinks , $unlinkedLinks , $vpage , $wikiWantedText , $wikiWantedLine ;
	global $wikiWantedToggleNumbers ;
	$vpage->special ( "The Most Wanted Pages" ) ;
	$vpage->namespace = "" ;
	if ( $showNumberPages == "" ) $showNumberPages = "off" ;
	if ( $showNumberPages == "off" ) $nsnp = "on" ;
	else $nsnp = "off" ;
	$allPages = array () ;
	$ret = $wikiWantedText ;
#	$ret .= "<nowiki><a href=\"".wikiLink("special:WantedPages?showNumberPages=$nsnp")."\">";
#	$ret .= str_replace("$1",$showNumberPages,$wikiWantedToggleNumbers)."</a></nowiki><br>\n" ;

	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT cur_title,cur_linked_links,cur_unlinked_links FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		$allPages[ucfirst($s->cur_title)] = -999999999999 ; # Effectively removing existing topics from list
		$fc = substr ( $s->cur_title , 0 , 1 ) ;
		if ( $showNumberPages == "on" OR $fc < "0" OR $fc > "9" ) {
			$u = explode ( "\n" , $s->cur_unlinked_links ) ;
			$v = array () ;
			foreach ( $u as $x ) {
				$w = ucfirst ( $x ) ;
				if ( $v[$w] != true ) # Count only one link per page
					{
					$allPages[$w] += 1 ;
					$v[$w] = true ;
					}
				}
			unset ( $v ) ;
			}
		}
	mysql_free_result ( $result ) ;
	#mysql_close ( $connection ) ;

	arsort ( $allPages ) ;
	$somePages = array_slice ( $allPages , 0 , 400 ) ; # Reducing needed memory
	unset ( $allPages ) ;
	$allPages = $somePages ;
	unset ( $somePages ) ;

	$ti = new wikiTitle ;
	$k = array_keys ( $allPages ) ;

	$a = 0 ;
	$o = array () ;
	while ( count ( $o ) < 50 ) {
		$x = $k[$a] ;
		$a++ ;
		$ti->setTitle ( $x ) ;
		if ( $x != "" and !$ti->doesTopicExist() ) {
			$n = str_replace ( "$1" , "[[$x|".$ti->getNiceTitle($x)."]]" , $wikiWantedLine ) ;
			$n = str_replace ( "$2" , $allPages[$x] , $n ) ;
			$n = str_replace ( "$3" , wikiLink("special:whatlinkshere&target=$x") , $n ) ;
			$n = str_replace ( "$4" , $ti->getNiceTitle($x) , $n ) ;
			array_push ( $o , "*$n\n" ) ;
			}
		}
	$ret .= implode ( "" , $o ) ;

	return $ret ;
	}

function WantedPages () {
	global $doRefresh , $wikiRefreshThisPage , $wikiResourcesWarning ;
	$pn = "Log:Most_Wanted" ;
	$ret = "<nowiki>" ;

	$ret .= "<p align=center><font size='+1'><b><a href=\"" ;
	$ret .= wikiLink ( "special:WantedPages&doRefresh=yes" ) ;
	$ret .= "\">$wikiRefreshThisPage</a></b></font><br>$wikiResourcesWarning</p></nowiki>\n" ;
	if ( $doRefresh == "yes" ) {
		$o = refreshWantedPages () ;
		$ret .= $o ;
		$p = new wikiPage ;
		$p->setTitle ( $pn ) ;
		$p->ensureExistence () ;
		$p->setEntry ( $o , "Refresh" , 0 , "System" , 1 , ",cur_timestamp=cur_timestamp" ) ; # Storing, don't show on RC
	} else {
		$ret .= getMySQL ( "cur" , "cur_text" , "cur_title=\"$pn\"" ) ;
		}
	return $ret ;
	}

function LonelyPages () {
	global $THESCRIPT ;
	global $linkedLinks , $unlinkedLinks , $vpage ;
	global $wikiLonelyPagesTitle , $wikiLonelyPagesText ;
	$vpage->special ( $wikiLonelyPagesTitle ) ;
	$vpage->namespace = "" ;
	$allPages = array () ;
	$ret = $wikiLonelyPagesText ;

	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT cur_title,cur_linked_links,cur_unlinked_links FROM cur WHERE cur_title NOT LIKE \"Talk:%\" AND cur_text NOT LIKE \"#redirect%\" AND cur_text != \"\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		$allPages[ucfirst($s->cur_title)] = $allPages[ucfirst($s->cur_title)] * 1 ;
		$u = explode ( "\n" , $s->cur_linked_links ) ; foreach ( $u as $x ) $allPages[ucfirst($x)] += 1 ;
		$u = explode ( "\n" , $s->cur_unlinked_links ) ; foreach ( $u as $x ) $allPages[ucfirst($x)] += 1 ;
		}
	if ( $result != false ) mysql_free_result ( $result ) ;
	#mysql_close ( $connection ) ;

	asort ( $allPages ) ;
#	$allPages = array_slice ( $allPages , 0 , 50 ) ;

	$orphans = array () ;
	$v = array_keys ( $allPages ) ;
	foreach ( $v as $x ) {
		if ( $allPages[$x] == 0 )
			array_push ( $orphans , $x ) ;
		}

	asort ( $orphans ) ;
	foreach ( $orphans as $x )
		$ret .= "# [[$x|".$vpage->getNiceTitle($x)."]]<br>\n" ;
	return $ret ;
	}

function AllPages () {
	global $THESCRIPT ;
	global $linkedLinks , $unlinkedLinks , $vpage ;
	global $wikiAllPagesTitle , $wikiAllPagesText ;
	$vpage->special ( $wikiAllPagesTitle ) ;
	$vpage->namespace = "" ;
	$ret = $wikiAllPagesText ;
	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT cur_title FROM cur ORDER BY cur_title" ;
	$result = mysql_query ( $sql , $connection ) ;
	$ret .= "<nowiki>" ;
	while ( $s = mysql_fetch_object ( $result ) )
		$ret .= "<a  href=\"".wikiLink("$s->cur_title")."\">".$vpage->getNiceTitle($s->cur_title)."</a><br>" ;
	$ret .= "</nowiki>" ;
	mysql_free_result ( $result ) ;
	#mysql_close ( $connection ) ;
	return $ret ;
	}

function searchLineDisplay ( $v ) {
	global $search ;
	$v = trim(str_replace("\n","",$v)) ;
	$v = str_replace ( "'''" , "" , $v ) ;
	$v = str_replace ( "''" , "" , $v ) ;
	$v = ereg_replace ( "\{\{\{.*\}\}\}" , "?" , $v ) ;
	$v = trim ( $v ) ;
	while ( substr($v,0,1) == ":" ) $v = substr($v,1) ;
	while ( substr($v,0,1) == "*" ) $v = substr($v,1) ;
	while ( substr($v,0,1) == "#" ) $v = substr($v,1) ;
	$v = eregi_replace ( $search , "'''".$search."'''" , $v ) ;
	$v = "<font size=-1>$v</font>" ;
	return $v ;
	}

function doSearch () {
	global $THESCRIPT ;
	global $vpage , $search , $startat , $user ;
	global $wikiSearchTitle , $wikiSearchedVoid , $wikiNoSearchResult ;
	$vpage = new WikiPage ;
	$vpage->special ( $wikiSearchTitle ) ;
	$r = array () ;
	$s = "" ;

	if ( $search == "" ) $s = $wikiSearchedVoid ;
	else {
		$search = wikiRecodeInput ( $search ) ;
		if ( !isset ( $startat ) ) $startat = 1 ;
		$perpage = $user->options["resultsPerPage"] ;
		global $wikiSQLServer ;
		$connection = getDBconnection () ;
		mysql_select_db ( $wikiSQLServer , $connection ) ;

/*
		# Old search algorithm
		$sql = "SELECT * FROM cur WHERE cur_title LIKE \"%$search%\" OR cur_text LIKE \"%$search%\" ORDER BY cur_title" ;
*/

		# New search algorithm
		$totalcnt = 0 ;
		$s2 = str_replace ( "_" , " " , $search ) ;
		$s2 = ereg_replace ( "[^A-Za-z0-9 ]" , "" , $s2 ) ;
		$s2 = str_replace ( "  " , " " , $s2 ) ;
		$s2 = explode ( " " , $s2 ) ;

		$exclude = "cur_title NOT LIKE \"%alk:%\"" ;
		if ( $exclude != "" ) $exclude = "($exclude) AND " ;

		# Phase 1
		$s3 = array () ;
		foreach ( $s2 as $x ) {
			$s4 = "(cur_title LIKE \"%".strtolower(substr($x,0,1)).substr($x,1)."%\" OR cur_title LIKE \"%".ucfirst($x)."%\")" ;
			array_push ( $s3 , $s4 ) ;
			}
		$s3 = implode ( " AND " , $s3 ) ;
		$sql = "SELECT * FROM cur WHERE $exclude( $s3 ) ORDER BY cur_title" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result != "" ) {
			while ( $s = mysql_fetch_object ( $result ) ) {
				if ( $totalcnt+1 >= $startat and count ( $r ) < $perpage )
					array_push ( $r , $s ) ;
					$totalcnt++ ;
				}
			mysql_free_result ( $result ) ;
			}

		# Phase 2
		$s3 = implode ( "%\" AND cur_text LIKE \"%" , $s2 ) ;
		$sql = "SELECT * FROM cur WHERE $exclude(cur_text LIKE \"%$s3%\" ) ORDER BY cur_title" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result != "" ) {
			while ( $s = mysql_fetch_object ( $result ) ) {
				if ( $totalcnt+1 >= $startat and count ( $r ) < $perpage )
					array_push ( $r , $s ) ;
					$totalcnt++ ;
				}
			mysql_free_result ( $result ) ;
			}


		#mysql_close ( $connection ) ;
		}

	if ( $s == "" and count ( $r ) == 0 ) {
		global $wikiUnsuccessfulSearch , $wikiUnsuccessfulSearches ;
		$s = "<h2>".str_replace("$1",$search,$wikiNoSearchResult)."</h2>" ;
		# Appending log page "wikpedia:Unsuccessful searches"
		$now = date ( "Y-m" , time() ) ;
		$logText = "*[[$search]]\n" ;
		makeLog ( str_replace ( "$1" , $now , $wikiUnsuccessfulSearches ) , $logText , str_replace ( "$1" , $search , $wikiUnsuccessfulSearch ) ) ;

	} else if ( $s == "" ) {
		global $wikiFoundHeading , $wikiFoundText ;
		$n = count ( $r ) ;
		$s .= "<table width=\"100%\" bgcolor=\"#FFFFCC\"><tr><td><font size=\"+1\"><b>$wikiFoundHeading</b></font><br>\n" ;
		$n = str_replace ( "$1" , $totalcnt , $wikiFoundText ) ;
		$n = str_replace ( "$2" , $search , $n ) ;
		$s .= "$n</td></tr></table>\n" ;
		$s .= "<table>" ;
		$realcnt = $startat ;
		$minlen = strlen ( $realcnt + count ( $r ) ) ;
		foreach ( $r as $x ) {
			$u = spliti ( "\n" , $x->cur_text ) ;
			$u = spliti ( "--" , $u[0] ) ;
			$y = searchLineDisplay ( array_shift ( $u ) ) ;
			foreach ( $u as $v ) {
				if ( stristr($v,$search) != false ) {
					$y .= "...<br>...".searchLineDisplay($v) ;
					break ;
					}
				}

			for ( $z = $realcnt ; strlen ( $z ) < $minlen ; $z = "0$z" ) ;
			$ct = $vpage->getNiceTitle ( $x->cur_title ) ;
			$s .= "<tr><td valign=top width=20 align=right><b>$z</b></td><td><font face=\"Helvetica,Arial\">'''[[$ct]]'''</font><br>" ;
			$s .= $y ;
			$s .= "</td></tr>" ;
			$realcnt++ ;
			}
		$s .= "</table>" ;
		if ( $totalcnt > $perpage ) {
			$s .= "<nowiki>" ;
			$last = $startat-$perpage ;
			$next = $startat+$perpage ;
			if ( $startat != 1 ) $s .= "<a href=\"".wikiLink("&search=$search&startat=$last")."\">&lt;&lt;</a> | ";
			for ( $a = 1 ; $a <= $totalcnt ; $a += $perpage ) {
				if ( $a != 1 ) $s .= " | " ;
				if ( $a != $startat ) $s .= "<a href=\"".wikiLink("&search=$search&startat=$a")."\">";
				$s .= "$a-" ;
				$s .= $a+$perpage-1 ;
				if ( $a != $startat ) $s .= "</a>" ;
				}
			if ( $startat != $a-$perpage ) $s .= " | <a href=\"".wikiLink("&search=$search&startat=".$next)."\">&gt;&gt;</a>";
			$s .= "</nowiki>" ;
			}
		}

	$vpage->contents = $s ;
	return $vpage->renderPage () ;
	}

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

function randompage () {
	global $THESCRIPT , $headerScript , $vpage ;
	global $wikiSQLServer ;
	$connection=getDBconnection() ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT COUNT(*) AS number FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	mt_srand((double)microtime()*1000000);
	$randval = mt_rand(0,$s->number-1);
	mysql_free_result ( $result ) ;

	$sql = "SELECT cur_title FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $randval >= 0 ) {
		$s = mysql_fetch_object ( $result ) ;
		$randval-- ;
		}
	$thelink = $s->cur_title ;
	$nt = $vpage->getNiceTitle($thelink) ;
	if ( count ( explode ( ":" , $thelink ) ) == 1 ) $thelink = ":".$thelink ;
	$ret = "<h2>--> [[$thelink|".$nt."]]...</h2>" ;
	$headerScript .= "<nowiki><META HTTP-EQUIV=Refresh CONTENT=\"0; URL=".wikiLink($thelink)."\"></nowiki>" ;
	mysql_free_result ( $result ) ;
	#mysql_close ( $connection ) ;

	return $ret ;
	}


function recentchanges () {
	global $THESCRIPT , $user ;
	global $vpage , $maxcnt , $daysAgo , $from , $wikiRecentChangesText , $wikiRecentChangesTitle ;
	global $wikiRecentChangesLastDays , $wikiRecentChangesSince , $wikiViewLastDays , $wikiViewMaxNum , $wikiListOnlyNewChanges ;
	$vpage->special ( $wikiRecentChangesTitle ) ;
	$vpage->makeSecureTitle() ;
	if ( !isset ( $maxcnt ) ) $maxcnt = 250 ;
	if ( !isset ( $daysAgo ) ) $daysAgo = 3 ;

	$from2 = substr ( $from , 0 , 4 ) . "-" . substr ( $from , 4 , 2 ) . "-" . substr ( $from , 6 , 2 ) ;
	$from2 .= " " . substr ( $from , 8 , 2 ) . ":" . substr ( $from , 10 , 2 ) . ":" . substr ( $from , 12 , 2 ) ;

	$ret = "" ;
	if ( $wikiRecentChangesText != "" ) $ret .= "$wikiRecentChangesText<br><br>" ;

	$ret .= "<nowiki>" ;
	if ( !isset($from) ) $ret .= str_replace ( "$1" , $maxcnt , str_replace ( "$2" , $daysAgo , $wikiRecentChangesLastDays ) ) ;
	else $ret .= str_replace ( "$1" , $maxcnt , str_replace ( "$2" , $from2 , $wikiRecentChangesSince ) ) ;

	$ret .= "<br>\n" ;
	$n = explode ( "$1" , $wikiViewMaxNum ) ;
	$ret .= $n[0] ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=50")."\">50</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=100")."\">100</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=250")."\">250</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=500")."\">500</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=1000")."\">1000</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=2500")."\">2500</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&daysAgo=$daysAgo&maxcnt=5000")."\">5000</a> " ;
	$ret .= $n[1]."; \n" ;
	$n = explode ( "$1" , $wikiViewLastDays ) ;
	$ret .= $n[0] ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=1")."\">1 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=2")."\">2 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=3")."\">3 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=5")."\">5 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=7")."\">7 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:RecentChanges&maxcnt=$maxcnt&daysAgo=14")."\">14 </a> ".$n[1]."; \n" ;

	$mindate = date ( "Ymd000000" , time () - $daysAgo*24*60*60 ) ;
	$mindate = timestampAddHour ( $mindate , $user->options["hourDiff"] ) ;

	$now = date ( "YmdHis" , time() ) ;
	$now = timestampAddHour ( $now , $user->options["hourDiff"] ) ;

	$ret .= "<a href=\"".wikiLink("special:RecentChanges&from=$now")."\">$wikiListOnlyNewChanges</a>" ;
	$ret .= "</nowiki>" ;
	$ret .= "\n----\n" ;
	$arr = array () ;

	if ( $from != "" ) $mindate = $from ;

	global $wikiSQLServer ;
	$connection=getDBconnection() ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT cur_timestamp,cur_title,cur_comment,cur_user,cur_user_text,cur_minor_edit FROM cur WHERE cur_timestamp>$mindate ORDER BY cur_timestamp DESC LIMIT $maxcnt" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) array_push ( $arr , $s ) ;
	mysql_free_result ( $result ) ;

	$minoredits = ( $user->options["hideMinor"] == "yes" ) ? "AND old_minor_edit<>1" : "" ;
	$d = array () ;
	foreach ( $arr as $s ) {
		$addoriginal = 1 ;
		if ( $minoredits != "" and $s->cur_minor_edit == 1 ) $addoriginal = 0 ;
		$i = 0 ;
		$j = tsc ( $s->cur_timestamp ) ;
		$ja = date ( "Ymd000000" , $j ) ;
		$jb = date ( "Ymd000000" , $j + 24*60*60 ) ;
		$sql = "SELECT count(old_id) AS cnt FROM old WHERE old_title=\"".$s->cur_title."\" AND old_timestamp>=$ja AND old_timestamp<=$jb $minoredits" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result != "" ) {
			$t = mysql_fetch_object ( $result ) ;
			if ( $t != "" ) $i = $t->cnt + $addoriginal ;
			mysql_free_result ( $result ) ;
			}
		if ( $i < 2 ) $i = "" ;
		$s->changes = $i ;
		if ( $s->cur_minor_edit != 1 OR $i > 1 OR $minoredits == "" ) {
			if ( $minoredits != "" ) $s->cur_minor_edit = 0 ;
			array_push ( $d , $s ) ;
			}
		}
	$arr = $d ;
	$d = array () ;

	#mysql_close ( $connection ) ;
	$ret .= recentChangesLayout($arr) ;
	return $ret ;
	}

function newPages_timeSort ( $a , $b ) { # This belongs to newpages alone!
	$a = $a->cur_timestamp ;
	$b = $b->cur_timestamp ;
	if ($a == $b) return 0;
	return ($a < $b) ? -1 : 1;
	}

function newpages () {
	global $THESCRIPT , $user ;
	global $vpage , $maxcnt , $daysAgo , $wikiNewPagesTitle , $wikiNewPagesText ;
	global $wikiRecentChangesLastDays , $wikiRecentChangesSince , $wikiViewLastDays , $wikiViewMaxNum , $wikiListOnlyNewChanges ;
	$vpage->special ( $wikiNewPagesTitle ) ;
	$vpage->makeSecureTitle() ;
	if ( !isset ( $maxcnt ) ) $maxcnt = 100 ;
	if ( !isset ( $daysAgo ) ) $daysAgo = 3 ;
	$names = array () ;

	$ret = "<nowiki>" ;
	$ret .= str_replace ( "$1" , $maxcnt , str_replace ( "$2" , $daysAgo , $wikiNewPagesText ) )."<br>\n" ;
	$n = explode ( "$1" , $wikiViewMaxNum ) ;
	$ret .= $n[0] ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&daysAgo=$daysAgo&maxcnt=50")."\">50</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&daysAgo=$daysAgo&maxcnt=100")."\">100</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&daysAgo=$daysAgo&maxcnt=250")."\">250</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&daysAgo=$daysAgo&maxcnt=500")."\">500</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&daysAgo=$daysAgo&maxcnt=1000")."\">1000</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&daysAgo=$daysAgo&maxcnt=2500")."\">2500</a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&daysAgo=$daysAgo&maxcnt=5000")."\">5000</a> " ;
	$ret .= $n[1]."; \n" ; 
	$n = explode ( "$1" , $wikiViewLastDays ) ;
	$ret .= $n[0] ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&maxcnt=$maxcnt&daysAgo=1")."\">1 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&maxcnt=$maxcnt&daysAgo=2")."\">2 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&maxcnt=$maxcnt&daysAgo=3")."\">3 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&maxcnt=$maxcnt&daysAgo=5")."\">5 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&maxcnt=$maxcnt&daysAgo=7")."\">7 </a> | " ;
	$ret .= "<a href=\"".wikiLink("special:NewPages&maxcnt=$maxcnt&daysAgo=14")."\">14 </a> ".$n[1]."<br>\n" ;
	$ret .= "</nowiki>" ;
	$arr = array () ;

	$mindate = date ( "Ymd000000" , time () - $daysAgo*24*60*60 ) ;
	$mindate = timestampAddHour ( $mindate , $user->options["hourDiff"] ) ;
	global $wikiSQLServer ;
	$connection=getDBconnection() ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;

	# Looking at the "cur" table
	$sql = "SELECT cur_title FROM cur WHERE cur_minor_edit=2 AND cur_timestamp>$mindate ORDER BY cur_timestamp DESC LIMIT $maxcnt" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) $names[$s->cur_title] = 1 ;
	mysql_free_result ( $result ) ;

	# Looking at the "old" table
	$arrB = array () ;
	$sql = "SELECT old_title FROM old WHERE old_minor_edit=2 AND old_timestamp>$mindate ORDER BY old_timestamp DESC LIMIT $maxcnt" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) $names[$s->old_title] = 1 ;
	mysql_free_result ( $result ) ;

	# Merging things
	$k = array_keys ( $names ) ;
	$arr = array () ;
	foreach ( $k as $x ) {
		$sql = "SELECT cur_timestamp,cur_title,cur_comment,cur_user,cur_user_text,cur_minor_edit FROM cur WHERE cur_title=\"$x\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $s = mysql_fetch_object ( $result ) ) {
			array_push ( $arr , $s ) ;
			mysql_free_result ( $result ) ;
			}
		}
	#mysql_close ( $connection ) ;
	
	uasort ( $arr , "newPages_timeSort" ) ;
	while ( count ( $arr ) > $maxcnt ) array_pop ( $arr ) ;

	$ret .= recentChangesLayout($arr) ;
	return $ret ;
	}

# Changes the date in recentChangesLayout() according to user setting; can be used similar elsewhere
function timestampAddHour ( $x , $d ) {
	$x = tsc ( $x ) ;
	$x += $d * 3600 ;
	$x = date ( "YmdHis" , $x ) ;
	return $x ;
	}

function recentChangesLayout ( &$arr ) {
	if ( count ( $arr ) == 0 ) return "" ;
	global $THESCRIPT , $user , $wikiDiff ;
	$lastDay = "" ;
	$color1 = $user->options["tabLine1"] ;
	$color2 = $user->options["tabLine2"] ;

	# Correcting time difference
	$arr2 = array () ;
	foreach ( $arr as $y ) {
		$y->cur_timestamp = timestampAddHour ( $y->cur_timestamp , $user->options["hourDiff"] ) ;
		array_push ( $arr2 , $y ) ;
		}
	$arr = $arr2 ;
	$arr2 = array () ;

	global $wikiRCLegend ;

	$xyz = new WikiTitle ;
	$editTypes = array ( "0"=>"" , "1"=>"<font color=cyan>M</font>" , "2"=>"<font color=green>N</font>" ) ;
	$ret = str_replace ( "$1" , $editTypes["1"] , str_replace ( "$2" , $editTypes["2"] ,  $wikiRCLegend ) ) ;
	if ( $user->options["changesLayout"] == "table" ) $ret .= "<table width=\"100%\" border=0 cellpadding=2 cellspacing=0>\n" ;
	else $ret .= "<ul>\n" ; 
	$dummy = wikiLink("x=y") ;
	foreach ( $arr as $s ) {
		$nt = $xyz->getNiceTitle ( $s->cur_title ) ;
		$day = date ( "l, F d, Y" , tsc ( $s->cur_timestamp ) ) ;
		$time = date ( "H:i" , tsc ( $s->cur_timestamp ) ) ;
		if ( $day != $lastDay ) {
			$lastDay = $day ;
			$tday = wikiGetDate ( tsc ( $s->cur_timestamp ) ) ;
			if ( $user->options["changesLayout"] == "table" ) $ret.="<tr><td width=\"100%\" colspan=7".$user->options["tabLine0"]."><b>$tday</b></td></tr>";
			else $ret .= "</ul><b>$tday</b><ul>\n" ;
			$color = $color1 ;
			}
		$u = $s->cur_user_text ;
		if ( $s->cur_user != 0 ) {
			$xyz->title = $u ;
			$xyz->makeSecureTitle () ;
			$u = "<a href=\"".wikiLink("user:$xyz->secureTitle")."\">$u</a>" ;
			}
#		else $u = "<font color=red>$u</font>" ; # IPs in red, deactivated
		$comment = trim($s->cur_comment) ;
		if ( $comment == "*" ) $comment = "" ;
		$o_comment = $comment ;
		if ( $s->cur_minor_edit == 1 ) $comment = "<font size=-1><i>$comment</i></font>" ;
		$minor = $editTypes[$s->cur_minor_edit] ;

		if ( $user->options["changesLayout"] == "table" ) $t = "<tr><td$color valign=top width=0%>" ;
		else $t = "<li>" ;

		if ( $s->version == "current" ) $t .= "<a href=\"".wikiLink("$s->cur_title&diff=yes")."\">$wikiDiff</a>&nbsp;" ;
		else if ( $s->version != "" ) $t .= "<a href=\"".wikiLink("$s->cur_title&oldID=$s->old_id&version=$s->version&diff=yes")."\">$wikiDiff</a>&nbsp;";
		else $t .= "<a href=\"".wikiLink("$s->cur_title&diff=yes")."\">$wikiDiff</a>" ;

		if ( $user->options["changesLayout"] == "table" ) $t .= "</td><td$color valign=top>" ;
		else $t .= " " ;

		if ( $s->version == "current" ) $t .= "<a href=\"".wikiLink("$s->cur_title")."\">$nt</a></td>" ;
		else if ( $s->version != "" ) $t .= "<a href=\"".wikiLink("$s->cur_title&oldID=$s->old_id&version=$s->version")."\">$nt ($s->version)</a></td>" ;
		else $t .= "<a href=\"".wikiLink("$s->cur_title")."\">$nt</a>" ;

		if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top width=0% nowrap>$time</td>" ;
		else $t = str_replace ( "</td>" , "; " , $t ) . " $time" ;

		$noc = $s->changes ;
		if ( $noc > 1 ) $noc = "$noc <a href=\"".wikiLink("$s->cur_title&action=history")."\">changes</a>" ;
		if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top width=0% nowrap>$noc</td>" ;
		else { 
			if ( $noc != "" ) $t .= " ($noc)" ;
			$t .= " . . . " ;
			}


		if ( $s->version != "" ) {
			$v = new wikiTitle ;
			$v->title = $s->cur_user_text ;
			$v->makeSecureTitle () ;
			if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top nowrap>" ;
			if ( $s->cur_user == 0 ) $t .= "$s->cur_user_text</td>" ;
			else $t .= "<a href=\"".wikiLink("user:$v->secureTitle")."\">$s->cur_user_text</a></td>" ;
			if ( $user->options["changesLayout"] == "table" ) $t .= "</td>" ;
			else $t .= "; " ;
			}
		else {
			if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top nowrap>$u</td>" ;
			else $t .= $u ;
			}
		if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top>$minor</td>" ;
		else $t .= " $minor" ;
		if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color >$comment</td>" ;
		else if ( $o_comment != "" ) $t .= " <b>[$comment]</b>" ;
		if ( $user->options["changesLayout"] == "table" ) $t .= "</tr>\n" ;
		else $t .= "</li>\n" ;
		$ret .= $t ;
		if ( $color == $color1 ) $color = $color2 ;
		else $color = $color1 ;
		}
	if ( $user->options["changesLayout"] == "table" ) $ret .= "</table>" ;
	else {
		$ret = "$ret</ul>\n" ;
		$ret = str_replace ( "</td>" , "" , $ret ) ;
		}
	return "<nowiki>$ret</nowiki>" ;
	}

function modifyArray ( $a , $sep , $rem , $add = "" ) {
	$b = explode ( $sep , $a ) ;
	$c = array () ;
	foreach ( $b as $x ) {
		if ( $x != "" and $x != $rem )
			array_push ( $c , $x ) ;
		}
	if ( $add != "" ) array_push ( $c , $add ) ;
	return implode ( $sep , $c ) ;
	}

function watch ( $t , $m ) {
	global $THESCRIPT , $user , $wikiUserSettingsError , $wikiWatchYes , $wikiWatchNo ;
	if ( !$user->isLoggedIn ) return $wikiUserSettingsError ;

	# Modifying user_watch
	$separator = "\n" ;
	$a = getMySQL ( "user" , "user_watch" , "user_id=$user->id" ) ;
	if ( $m == "yes" ) $a = modifyArray ( $a , $separator , $t , $t ) ;
	else $a = modifyArray ( $a , $separator , $t ) ;
	setMySQL ( "user" , "user_watch" , $a , "user_id=$user->id" ) ;

	if ( $m == "yes" ) $ret = str_replace ( "$1" , $t , $wikiWatchYes ) ;
	else str_replace ( "$1" , $t , $wikiWatchNo ) ;
	$ret .= "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL='".wikiLink(nurlencode($t))."'\">" ;
	return $ret ;
	}

function WatchList () {
	global $THESCRIPT ;
	global $vpage , $user , $wikiWatchlistTitle , $wikiWatchlistText ;
	$vpage->special ( $wikiWatchlistTitle ) ;
	$ret = $wikiWatchlistText ;
	$a = getMySQL ( "user" , "user_watch" , "user_id=$user->id" ) ;
	$separator = "\n" ;
	$b = explode ( $separator , $a ) ;
	$vpage->namespace = "" ;

	$n = array () ;
	foreach ( $b as $x )
		$n[$x] = getMySQL ( "cur" , "cur_timestamp" , "cur_title=\"$x\"" ) ;
	arsort ( $n ) ;
	$k = array_keys ( $n ) ;

	global $wikiSQLServer ;
	$connection=getDBconnection() ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$arr = array () ;
	$any = false ;
	foreach ( $k as $x ) {
		if ( $x != "" ) {
			$sql = "SELECT * FROM cur WHERE cur_title=\"$x\"" ;
			$result = mysql_query ( $sql , $connection ) ;
			$s = mysql_fetch_object ( $result ) ;
			array_push ( $arr , $s ) ;
			mysql_free_result ( $result ) ;
			$any = true ;
			}
		}
	#mysql_close ( $connection ) ;
	if ( $any ) $ret .= recentChangesLayout ( $arr ) ;

	return $ret ;
	}

function statistics () {
	global $THESCRIPT , $wikiSQLServer , $wikiStatisticsTitle , $wikiStatTotalPages ;
	global $wikiStatTalkPages , $wikiStatCommaPages , $wikiStatWikipediaNoTalk , $wikiStatSubNoTalk , $wikiStatNoTalk , $wikiStatArticles , $wikiStatJunk , $wikiStatOld , $wikiStatUsers , $wikiStatSysops ;
	$connection=getDBconnection() ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$ret = "" ;
	$ret .= "<h2>$wikiStatisticsTitle</h2><ul>" ;

	$nf1 = "<font color=red><b>" ;
	$nf2 = "</b></font>" ;

	# TOTAL	
	$sql = "SELECT COUNT(*) AS number FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$totalPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$totalPages$nf2" , $wikiStatTotalPages )."</li>" ;
	mysql_free_result ( $result ) ;

	# /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title LIKE \"%/Talk\" OR cur_title LIKE \"Talk:%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$talkPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$talkPages$nf2" , $wikiStatTalkPages )."</li>" ;
	mysql_free_result ( $result ) ;

	# , NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title NOT LIKE \"talk:%\" AND cur_text LIKE \"%,%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$commaPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$commaPages$nf2" , $wikiStatCommaPages )."</li>" ;
	mysql_free_result ( $result ) ;

	# WIKIPEDIA NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title NOT LIKE \"talk:%\" AND cur_title LIKE \"%ikipedia%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$wikiPages = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$wikiPages$nf2" , $wikiStatWikipediaNoTalk )."</li>" ;
	mysql_free_result ( $result ) ;

	# WIKIPEDIA NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title LIKE \"%/%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$subPages = $s->number - $talkPages;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$subPages$nf2" , $wikiStatSubNoTalk )."</li>" ;
	mysql_free_result ( $result ) ;

	# RESULT
	$x = $commaPages - $wikiPages ; # Comma (no /Talk) - wiki pages = articles, including subpages
	$ret .= "<li>".str_replace ( "$1" , "$nf1$x$nf2" , $wikiStatNoTalk )."</li>" ;
	$y = $x - $subPages ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$y$nf2" , $wikiStatArticles )."</li>" ;
	$z = $totalPages - $talkPages - $commaPages ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$z$nf2" , $wikiStatJunk )."</li>" ;

	# OLD PAGES
	$sql = "SELECT COUNT(*) as number FROM old" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$p = round ( $oldPages / $totalPages , 2 ) ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$oldPages$nf2" , str_replace ( "$2" , $p , $wikiStatOld ) )."</li>" ;
	mysql_free_result ( $result ) ;


	$ret .= "</ul><hr>" ;
	$ret .= "<h2>User statistics</h2><ul>" ;
	
	# USERS
	$sql = "SELECT COUNT(*) as number FROM user" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$numUser = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$numUser$nf2" , $wikiStatUsers )."</li>" ;
	mysql_free_result ( $result ) ;
	
	# EDITORS AND SYSOPS
	$sql = "SELECT COUNT(*) as number FROM user WHERE user_rights LIKE \"%is_editor%\" OR user_rights LIKE \"%is_sysop%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$numEditors = $s->number ;
	$ret .= "<li>".str_replace ( "$1" , "$nf1$numEditors$nf2" , $wikiStatSysops )."</li>" ;
	mysql_free_result ( $result ) ;

	#mysql_close ( $connection ) ;
	$ret .= "</ul>" ;
	return $ret ;
	}

function upload () {
	global $THESCRIPT ;
	global $removeFile , $xtitle , $removeFile , $Upload , $Upload_name , $no_copyright ;
	global $user , $vpage , $wikiUploadTitle , $wikiUploadText , $wikiUploadDenied ;
	global $wikiUploadDeleted , $wikiUploadDelMsg1 , $wikiUploadDelMsg2 ;
	global $wikiUploadAffirm , $wikiUploadFull ;
	global $wikiUploadSuccess , $wikiUploadSuccess1 , $wikiUploadSuccess2 ;
	global $wikiUploadAffirmText , $wikiUploadButton ;
	$vpage->special ( $wikiUploadTitle ) ;
	$isSysop = in_array ( "is_sysop" , $user->rights ) ;
	$xtitle = $wikiUploadPage ;
	$ret = "<nowiki>" ;

	$message = "" ;

	if (isset($removeFile)) {
		if ( !$isSysop ) return $wikiUploadDenied ;
		if (is_file("./upload/$removeFile") ) unlink ("./upload/$removeFile");
		$message = str_replace ( "$1" , $removeFile , $wikiUploadDeleted ) ;

		# Appending log page "log:Uploads"
		$now = date ( "Y-m-d H:i:s" , time () ) ;
		$logText = str_replace ( "$1" , $user->name , str_replace ( "$2" , $removeFile , $wikiUploadDelMsg1 ) ) ;
		makeLog ( "log:Uploads" , $logText , str_replace ( "$1" , $removeFile , $wikiUploadDelMsg2 ) ) ;

		unset ( $removeFile ) ;
	} else if (isset($Upload_name) or isset($Upload)) {
		if ( $no_copyright != "AFFIRMED" ) return $wikiUploadAffirm ;
#		$Upload_name = ereg_replace(" ", "_", $Upload_name);
		$abc = split("\.", $Upload_name);

		$num = exec ("df");
		$readata = substr($num,(strpos($num, "%")-2),2);

		if ($readata > 96) {
			$ret .= "<body bgcolor=white>\n";
			$ret .= "<br><b>$wikiUploadFull</b>\n";
			return $ret ;
			}

		copy ( $Upload , "./upload/$Upload_name" ) ;
		chmod ( "./upload/$Upload_name" , 0777 ) ;
		$message = str_replace ( "$1" , htmlspecialchars ( $Upload_name ) , $wikiUploadSuccess ) ;

		# Appending log page "log:Uploads"
		global $REMODE_ADDR ;
		$now = date ( "Y-m-d H:i:s" , time () ) ;
		$userText = "[[user:$user->name|$user->name]]" ;
		if ( $user->name == "" ) $userText = $REMODE_ADDR ;
		$logText = str_replace ( "$1" , $now , str_replace ( "$2" , $userText , str_replace ( "$3" , htmlspecialchars ( $Upload_name ) , $wikiUploadSuccess1 ) ) ) ;
		makeLog ( "log:Uploads" , $logText , str_replace ( "$1" , $Upload_name , $wikiUploadSuccess2 ) ) ;

		unset ( $Upload_name ) ;
	}

	if ( $message != "" ) $ret .= "<font color=red>$message</font><br>\n" ;
	$ret .= $wikiUploadText ;
	$ret .= " <form enctype=\"multipart/form-data\" action=\"".wikiLink("special:upload")."\" method=post>\n";
	$ret .= " <input type=hidden name=max value=20096>\n";
	$ret .= " <input name=Upload type=\"file\"><br>\n";
	$ret .= " <input type=hidden name=update value=1>\n";
	$ret .= " <input type=hidden name=step value=$step>\n";
	$ret .= "<INPUT TYPE=checkbox NAME=\"no_copyright\" VALUE=\"AFFIRMED\">$wikiUploadAffirmText<br>\n" ;
	$ret .= " <input type=submit name=Upload value=$wikiUploadButton>\n";
	$ret .= "</form>\n";

	global $wikiUploadPrev , $wikiUploadSize , $wikiFileRemoval , $wikiUploadRemove, $THESCRIPT ;

	if (is_dir("upload")) {
		$mydir = dir("upload");
			while ($entry = $mydir->read()) {
			if ($entry != "." and $entry != "..")
				$file = "yes";
			}
		$mydir->close();
		$uploaddir = ereg_replace("[A-Za-z0-9_.]+$", "upload", $THESCRIPT);

		if ($file == "yes") {
			$ret .= "<h2>$wikiUploadPrev</h2>";
			$mydir = opendir("upload");
			$i = 0;
			$ret .= "<table border=1 width=\"100%\">\n";
			$ret .= "<tr><th>File</th><th>$wikiUploadSize</th>";
			if ( $isSysop )
				$ret .= "<th>$wikiFileRemoval</th>";
			$ret .= "</tr>\n" ;
			while ($entry = readdir($mydir)) {
				if ($entry != '.' && $entry != '..') {
					$ret .= "<tr><td align=center>" ;
					$ret .= "<a href=\"$uploaddir/".rawurlencode($entry)."\">".htmlspecialchars($entry)."</a></td>";
					$ret .= "<td align=center>".filesize("upload/$entry")." bytes</td>";
					if ( $isSysop )  {
						$ret .= "<td align=center><a href=\"".wikiLink("special:upload&removeFile=".urlencode($entry))."\">" ;
						$ret .= str_replace ( "$1" , $entry , $wikiUploadRemove ) ;
						$ret .= "</a></td>" ;
						}
					$ret .= "</tr>" ;
					$i++;
				}
			}
		$ret .= "</table>\n";
		closedir($mydir);
		}
	}
	$ret .= "</nowiki>" ;
	return $ret ;
	}

function doHistory ( $title ) {
	global $THESCRIPT , $vpage , $wikiSQLServer , $wikiHistoryTitle , $wikiCurrentVersion , $wikiHistoryHeader ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;
	$vpage->special ( str_replace ( "$1" , $title , $wikiHistoryTitle ) ) ;
	$vpage->makeSecureTitle () ;

	$a = array () ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT * FROM cur WHERE cur_title=\"$ti\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	array_push ( $a , $s ) ;
	mysql_free_result ( $result ) ;
	$o = $s->cur_old_version ;
	while ( $o != 0 ) {
		$sql = "SELECT * FROM old WHERE old_id=$o" ;
		$result = mysql_query ( $sql , $connection ) ;
		$s = mysql_fetch_object ( $result ) ;
#		print "<font color=red>$s->old_timestamp:</font> ".$s->old_text."<br>" ;
		$s->cur_timestamp = $s->old_timestamp ;
		$s->cur_title = $s->old_title ;
		$s->cur_user = $s->old_user ;
		$s->cur_comment = $s->old_comment ;
		$s->cur_user_text = $s->old_user_text ;
		$s->cur_minor_edit = $s->old_minor_edit ;
		array_push ( $a , $s ) ;
		$o = $s->old_old_version ;
		mysql_free_result ( $result ) ;
		}
	#mysql_close ( $connection ) ;

	$i = count ( $a ) ;
	$k = array_keys ( $a ) ;
	foreach ( $k as $x ) {
		if ( $i != count ( $a ) ) $a[$x]->version = $i ;
		else $a[$x]->version = $wikiCurrentVersion ;
		$i-- ;
		}

	$t = recentChangesLayout ( $a ) ;
	$t = "<b>".str_replace("$1",$title,$wikiHistoryHeader)."</b>".$t ;

	$ret = $vpage->getHeader() ;
	$ret .= $vpage->getMiddle($t) ;
	$ret .= $vpage->getFooter() ;
	return $ret ;
	}

function special_pages () {
	global $THESCRIPT , $vpage , $user , $wikiSpecialTitle , $wikiSpecialText ;
	$vpage->special ( $wikiSpecialTitle ) ;
	$ret = $wikiSpecialText ;
	$ret .= $vpage->getQuickBar () ;
	$ret = "<nowiki>$ret</nowiki>" ;
	return $ret ;
	}

function pagesThatLinkHere ( $t , $connection ) {
	$a = array () ;
	$sql = "SELECT cur_title,cur_linked_links FROM cur WHERE cur_linked_links LIKE \"%$t%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		$b = explode ( "\n" , $s->cur_linked_links ) ;
		if ( in_array ( $t , $b ) )
			array_push ( $a , $s ) ;
		}
	mysql_free_result ( $result ) ;
	return $a ;
	}

function ShortPages () {
	global $THESCRIPT , $wikiSQLServer , $user , $vpage , $startat , $wikiStubTitle , $wikiStubText , $showLinksThere , $wikiStubShowLinks ;
	if ( !isset ( $startat ) ) $startat = 1 ;
	$perpage = $user->options["resultsPerPage"] ;
	if ( $perpage == 0 ) $perpage = 20 ;
	$vpage->special ( $wikiStubTitle ) ;
	$vpage->namespace = "" ;
	if ( $showLinksThere == "" ) $showLinksThere = 0 ;
	if ( $showLinksThere == 1 ) $sLT2 = 0 ;
	else $sLT2 = 1 ;
	$ret = $wikiStubText ;
	$ret .= str_replace ( "$1" , $sLT2 , $wikiStubShowLinks ) ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT COUNT(*) AS number FROM cur WHERE cur_title NOT LIKE \"%:%\" AND cur_text NOT LIKE \"#redirect%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$total = $s->number ;
	$sql = "SELECT cur_title,LENGTH(cur_text) AS len FROM cur WHERE cur_title NOT LIKE \"%:%\" AND cur_text NOT LIKE \"#redirect%\" ORDER BY LENGTH(cur_text),cur_title" ;
	$result = mysql_query ( $sql , $connection ) ;
	$cnt = 1 ;
	$color1 = $user->options["tabLine1"] ;
	$color2 = $user->options["tabLine2"] ;
	$color = $color1 ;
	$ret .= "<table width=\"100%\">\n" ;
	$ar = array () ;
	while ( $s = mysql_fetch_object ( $result ) and $cnt < $startat+$perpage ) {
		if ( $cnt >= $startat ) {
			$s->cnt = $cnt ;
			array_push ( $ar , $s ) ;
			}
		$cnt++ ;
		}
	mysql_free_result ( $result ) ;

	global $wikiStubChars , $wikiStubDelete , $wikiStubLinkHere ;

	foreach ( $ar as $s ) {
		$k = new wikiTitle ;
		$k->setTitle ( $s->cur_title ) ;
		$ret .= "<tr><td$color align=right valign=top nowrap>$s->cnt</td>" ;
		$ret .= "<td$color align=right valign=top nowrap>(".str_replace("$1",$s->len,$wikiStubChars).")</td>\n" ;
		$ret .= "<td$color nowrap valign=top>[[$s->cur_title|".$k->getNiceTitle()."]]</td>\n";
		if ( in_array ( "is_sysop" , $user->rights ) )
			$ret .= "<td$color valign=top nowrap><nowiki><a href=\"".wikiLink("special:deletepage&target=$s->cur_title")."\">$wikiStubDelete</a></nowiki></td>" ;
		else $ret .= "<td$color width=\"100%\" nowrap>&nbsp;</td>" ;

		if ( $showLinksThere == 1 ) {
			$lf = "" ;
			$lh = pagesThatLinkHere($s->cur_title,$connection);
			if ( count ( $lh ) <= 5 and count ( $lh ) > 0 ) {
				foreach ( $lh as $ll ) {
					if ( $lf == "" ) $lf = " <font size=-1>(" ;
					else $lf .= " - " ;
					$lf .= "[[$ll->cur_title]]" ;
					}
				$lf .= ")</font>" ;
				}
			$ret .= "<td$color width=\"100%\" valign=top>".str_replace("$1",count($lh),$wikiStubLinkHere)."$lf</td>\n";
		} else $ret .= "<td$color valign=top><nowiki><a href=\"".wikiLink("special:whatlinkshere&target=$s->cur_title")."\">Show pages that link to \"".$k->getNiceTitle()."\"</a></nowiki></td>\n" ;

		$ret .= "</tr>" ;
		if ( $color == $color1 ) $color = $color2 ;
		else $color = $color1 ;
		}
	$ret .= "</table>\n" ;

	$ret .= "<nowiki>" ;
	$before = $startat - $perpage ; $fin = $before + $perpage - 1 ;
	if ( $startat > 1 ) $ret .= "<a href=\"".wikiLink("special:ShortPages&startat=$before&showLinksThere=$showLinksThere")."\">$before-$fin&lt;&lt;</a> &nbsp;";
	$after = $startat + $perpage ; $fin = $after+$perpage - 1 ; if ( $fin > $total ) $fin = $total ;
	if ( $after-1 < $total ) $ret .= "<a href=\"".wikiLink("special:ShortPages&startat=$after&showLinksThere=$showLinksThere")."\">&gt;&gt;$after-$fin</a>" ;
	$ret .= "</nowiki>" ;
	#mysql_close ( $connection ) ;
	return $ret ;
	}

function removeFromLinkList ( $item , $link ) {
	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT cur_id FROM cur WHERE $item LIKE \"%$link%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$ids = array () ;
	while ( $s = mysql_fetch_object ( $result ) ) array_push ( $ids , $s->cur_id ) ;
	mysql_free_result ( $result ) ;

	foreach ( $ids as $x ) {
		$sql = "SELECT cur_timestamp,$item FROM cur WHERE cur_id=$x" ;
		$result = mysql_query ( $sql , $connection ) ;
		$s = mysql_fetch_object ( $result )  ;
		mysql_free_result ( $result ) ;
		$y = explode ( "\n" , $s->$item ) ;
		$z = array () ;
		foreach ( $y as $u ) {
			if ( $u != $link )
				array_push ( $z , $u ) ;
			}
		$y = implode ( "\n" , $z ) ;
		$sql = "UPDATE cur SET cur_timestamp=\"$s->cur_timestamp\",$item=\"$y\" WHERE cur_id=$x" ;
		$result = mysql_query ( $sql , $connection ) ;
		}

	#mysql_close ( $connection ) ;
	}

function makeLog ( $logPage , $logText , $logMessage , $doAppend = true ) {
	global $user ;
	$np = new wikiPage ;
	$np->setTitle ( $logPage ) ;
	$np->ensureExistence () ;
	$log = getMySQL ( "cur" , "cur_text" , "cur_title=\"".$np->secureTitle."\"" ) ;
	if ( $doAppend ) {
		$log = $logText.$log ;
	} else { # Not implemented
		}
	$np->setEntry ( $log , $logMessage , $user->id , $user->name , 1 ) ;
	}

function deletepage () {
	global $THESCRIPT , $target , $user , $iamsure ;
	global $vpage , $wikiSQLServer ;
	$target = str_replace ( "\\\\" , "\\" , $target ) ;
	$target = str_replace ( "\\\\" , "\\" , $target ) ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;

	global $wikiDeleteTitle , $wikiDeleteDenied , $wikiDeleteSuccess , $wikiDeleteMsg1 , $wikiDeleteMsg2 , $wikiDeleteAsk ;
	$vpage->special ( str_replace ( "$1" , $target , $wikiDeleteTitle ) ) ;
	$vpage->makeSecureTitle () ;
	if ( !in_array ( "is_sysop" , $user->rights ) ) return $wikiDeleteDenied ;
	if ( $iamsure == "yes" ) {
		$ret = "<h2>".str_replace("$1",$target,$wikiDeleteSuccess)."</h2>" ;
		$connection = getDBconnection () ;
		mysql_select_db ( $wikiSQLServer , $connection ) ;
		$sql = "DELETE FROM cur WHERE cur_title=\"$target\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		#mysql_close ( $connection ) ;

		# Appending log page "log:Page Deletions"
		$now = date ( "Y-m-d H:i:s" , time () ) ;
		$logTarget = $vpage->getNiceTitle ( $target ) ;
		$logText = str_replace("$1",$now,str_replace("$2",$user->name,str_replace("$3",$logTarget,$wikiDeleteMsg1))) ;
		makeLog ( "log:Page Deletions" , $logText , str_replace("$1",$logTarget,$wikiDeleteMsg2)) ;

		removeFromLinkList ( "cur_linked_links" , $target ) ;
		removeFromLinkList ( "cur_unlinked_links" , $target ) ;
	} else {
		$ret = "<font size=\"+2\">".str_replace("$1",$target,$wikiDeleteAsk)."</font>" ;
		}
	return "<nowiki>$ret</nowiki>" ;
	}

function protectpage () {
	global $THESCRIPT , $target , $user , $protecting , $newrestrictions , $vpage ;
	global $wikiProtectTitle , $wikiProtectDenied , $wikiProtectNow , $wikiProtectText , $wikiProtectCurrent ;
	$target = str_replace ( "\\\\" , "\\" , $target ) ;
	$target = str_replace ( "\\\\" , "\\" , $target ) ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;
	$vpage->special ( str_replace ( "$1" , $target , $wikiProtectTitle ) ) ;
	$vpage->makeSecureTitle () ;
	if ( !in_array ( "is_sysop" , $user->rights ) ) return $wikiProtectDenied ;
	if ( $protecting == "yes" ) {
		$r = explode ( "," , $newrestrictions ) ;
		$nr = array () ;
		foreach ( $r as $x )
			if ( strtolower ( substr ( $x , 0 , 3 ) ) == "is_" )
				array_push ( $nr , strtolower ( $x ) ) ;
		$nr = implode ( "," , $nr ) ;
		$t = getMySQL ( "cur" , "cur_timestamp" , "cur_title=\"$target\"" ) ;
		setMySQL ( "cur" , "cur_restrictions" , $nr , "cur_title=\"$target\"" ) ;
		$ret = "<font size=\"+2\">".str_replace("$1",$target,str_replace("$2",$nr,$wikiProtectNow))."</font>" ;
		setMySQL ( "cur" , "cur_timestamp" , $t , "cur_title=\"$target\"" ) ;
	} else {
		$p = getMySQL ( "cur" , "cur_restrictions" , "cur_title=\"$target\"" ) ;

		$ret = str_replace("$1",$target,$wikiProtectText) ;
		$ret .= "<br><br><FORM action=\"".wikiLink("special:protectpage&target=$target&protecting=yes")."\" method=post>$wikiProtectCurrent\n" ;
		$ret .= "<INPUT TABINDEX=1 TYPE=text NAME=newrestrictions VALUE=\"$p\" SIZE=30>\n" ;
		$ret .= "<INPUT TABINDEX=2 TYPE=submit NAME=save VALUE=\"Save\">" ;
		$ret .= "</FORM>\n" ;
		}
	return "<nowiki>$ret</nowiki>" ;
	}

# This function list the contributions of a user
function contributions () {
	global $THESCRIPT , $target , $user , $protecting , $newrestrictions ;
	global $vpage , $theuser , $wikiSQLServer ;
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
	mysql_select_db ( $wikiSQLServer , $connection ) ;

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
	#mysql_close ( $connection ) ;

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

function whatLinksHere () {
	global $THESCRIPT , $target , $user , $protecting , $newrestrictions ;
	global $vpage , $target , $wikiLinkhereTitle ;
	global $wikiLinkhereBacklink , $wikiLinkhereNoBacklink , $wikiBacklinkNolink , $wikiBacklinkFollowing ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;
	$niceTarget = $vpage->getNiceTitle ( $target ) ;
	$vpage->special ( str_replace ( "$1" , $niceTarget , $wikiLinkhereTitle ) ) ;
	$vpage->makeSecureTitle () ;

	global $wikiSQLServer ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;

	# The question is kinda long, but I don't want to sort everything out manually, so...
	$question = "SELECT cur_title FROM cur WHERE" ;
	$question .= " cur_linked_links LIKE \"$target\" OR " ;
	$question .= " cur_linked_links LIKE \"$target\n%\" OR" ;
	$question .= " cur_linked_links LIKE \"%\n$target\n%\" OR" ;
	$question .= " cur_linked_links LIKE \"%\n$target\" OR " ;
	$question .= " cur_unlinked_links LIKE \"$target\" OR " ;
	$question .= " cur_unlinked_links LIKE \"$target\n%\" OR" ;
	$question .= " cur_unlinked_links LIKE \"%\n$target\n%\" OR" ;
	$question .= " cur_unlinked_links LIKE \"%\n$target\"" ;

	$result = mysql_query ( $question , $connection ) ;
	$p = array () ;
	if ( $result != "" ) {
		while ( $s = mysql_fetch_object ( $result ) ) array_push ( $p , $s->cur_title ) ;
		mysql_free_result ( $result ) ;
		}

	$question = "SELECT cur_linked_links,cur_unlinked_links FROM cur WHERE cur_title=\"$target\"" ;
	$result = mysql_query ( $question , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	mysql_free_result ( $result ) ;
	#mysql_close ( $connection ) ;

	$out = explode ( "\n" , $s->cur_linked_links."\n".$s->cur_unlinked_links ) ;
	$dlb = array () ;
	$dnlb = array () ;

	foreach ( $p as $x ) {
		$y = $vpage->getNiceTitle ( $x ) ;
		if ( in_array ( $x , $out ) ) array_push ( $dlb , $y ) ;
		else array_push ( $dnlb , $y ) ;
		}

	asort ( $dlb ) ;
	$dlb = implode ( "]]\n*[[" , $dlb ) ;
	if ( $dlb != "" ) $dlb = "<h3>".str_replace("$1",$niceTarget,$wikiLinkhereBacklink)."</h3>\n*[[$dlb]]\n" ;

	asort ( $dnlb ) ;
	$dnlb = implode ( "]]\n*[[" , $dnlb ) ;
	if ( $dnlb != "" ) $dnlb = "<h3>".str_replace("$1",$niceTarget,$wikiLinkhereNoBacklink)."</h3>\n*[[$dnlb]]\n" ;

	$ret = $dnlb.$dlb ;
	if ( $ret == "" ) $ret = "<h1>".str_replace("$1",$niceTarget,$wikiBacklinkNolink)."</h1>" ;
	else $ret = "<h1>".str_replace("$1",$niceTarget,$wikiBacklinkFollowing)."</h1>\n$ret" ;

	return $ret ;
	}

function popularpages () {
	global $wikiSQLServer , $vpage ;
	$a = array () ;
	$connection = getDBconnection () ;
	mysql_select_db ( $wikiSQLServer , $connection ) ;
	$sql = "SELECT cur_title,cur_counter FROM cur GROUP BY cur_title ORDER BY cur_counter DESC LIMIT 100" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) )
		array_push ( $a , $s ) ;
	if ( $result != false ) mysql_free_result ( $result ) ;
	#mysql_close ( $connection ) ;

	$ret = "" ;
	$ret .= "<table>\n" ;
	foreach ( $a as $x ) {
		$ret .= "<tr>\n" ;
		$ret .= "<td align=right nowrap>".number_format($x->cur_counter,0)."</td>\n" ;
		$ret .= "<td>[[".$vpage->getNiceTitle($x->cur_title)."]]</td>\n" ;
		$ret .= "</tr>\n" ;
		}
	$ret .= "</table>" ;
	return $ret ;
	}

# A little hack for direct MySQL access; for sysops only!
function askSQL () {
	global $THESCRIPT ;
	global $Save , $question ;
	$ret = "" ;
	if ( isset ( $Save ) ) {
		$ret .= "$question<br>" ;
		unset ( $Save ) ;
		global $wikiSQLServer ;
		$connection = getDBconnection () ;
		mysql_select_db ( $wikiSQLServer , $connection ) ;
		$question = str_replace ( "\\\"" , "\"" , $question ) ;
		$result = mysql_query ( $question , $connection ) ;
		$n = mysql_num_fields ( $result ) ;
		$k = array () ;
		for ( $x = 0 ; $x < $n ; $x++ ) array_push ( $k , mysql_field_name ( $result , $x ) ) ;
		$a = array () ;
		while ( $s = mysql_fetch_object ( $result ) ) {
			array_push ( $a , $s ) ;
			}
		mysql_free_result ( $result ) ;
		#mysql_close ( $connection ) ;

		$ret .= "<table width=\"100%\" border=1 bordercolor=black cellspacing=0 cellpadding=2><tr>" ;
		foreach ( $k as $x ) $ret .= "<th>$x</th>" ;
		$ret .= "</tr><tr>" ;
		foreach ( $a as $y ) {
			foreach ( $k as $x ) $ret .= "<td>".$y->$x."</td>" ;
			$ret .= "</tr><tr>" ;
			}
		$ret .= "</tr></table>" ;
		}
	$form = "" ;
	$form .= "<FORM method=POST>" ;
	$form .= "<input type=text value=\"$question\" name=question size=150> \n" ;
	$form .= "<input type=submit value=Ask name=Save> \n" ;
	$form .= "</FORM>" ;
	return $form.$ret ;
	}
?>
