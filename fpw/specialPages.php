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

function userLogout () {
	global $user , $vpage ;
	$vpage->title = "User logout" ;
	setcookie ( "WikiLoggedIn" , "" , time()-3600 ) ;
	if ( $user->options["rememberPassword"] != "on" ) setcookie ( "WikiUserPassword" , "" , time()-3600 ) ;
	$user->isLoggedIn = false ;
	return "<h1>Goodbye, $user->name!</h1>" ;
	}

function userLogin () {
	global $THESCRIPT ;
	global $WikiUserPassword , $WikiLoggedIn ;
	global $loginattempt , $user , $vpage , $WikiUserID , $expiration ;
	$vpage->title = "User login" ;

	if ( isset ( $loginattempt ) ) {
		unset ( $loginattempt ) ;
		global $USERNAME , $USERPASSWORD , $RETYPE , $REMEMBERPASSWORD ;
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
			$nu->addToDatabase () ;
			$user = $nu ;
			$s = "<h1>Welcome, $user->name!</h1><font color=red>Don't forget to personalize your wikipedia preferences!</font>" ;
			$s .= "<br>Your account has been created. Please press \"Log in\" once more to log in!" ;
			setcookie ( "WikiLoggedIn" , "yes" , $expiration ) ;
			setcookie ( "WikiUserID" , $user->id , $expiration ) ;
			if ( $user->options["rememberPassword"] == "on" ) setcookie ( "WikiUserPassword" , $user->password , $expiration ) ;
			$user->options["rememberPassword"] = $REMEMBERPASSWORD ;
			$user->saveSettings() ;

		if ( $user->options["rememberPassword"] == "on" ) $check = "checked" ;
	  	$s .= "<FORM action=\"$THESCRIPT?title=special:userLogin\" method=post><font face=courier>\n" ;
	  	$s .= "Your user name&nbsp; : <INPUT TABINDEX=1 TYPE=text NAME=USERNAME VALUE=\"$user->name\" SIZE=20><br>\n" ;
	  	$s .= "Your password&nbsp;&nbsp; : <INPUT TABINDEX=2 TYPE=password NAME=USERPASSWORD VALUE=\"$user->password\" SIZE=20><br>\n" ;
  		$s .= "<INPUT TABINDEX=4 TYPE=checkbox NAME=REMEMBERPASSWORD $check>Remember my password (as a cookie).<br>\n" ;
	  	$s .= "<input TABINDEX=5 type=submit name=loginattempt value=\"Log in\">\n" ;
  		$s .= "</font></FORM>\n" ;

		} else {
			$s .= "<h2>Problem with login</h2>" ;
			$s .= "Try again!" ;
			}
	} else {
		$s = "" ;
		if ( $user->isLoggedIn ) $s .= "<font color=red><b>User $user->name, you are already logged in!</b></font><br>\n" ;
	  	$s .= "<h1>Please log in:</h1>\n" ;
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
		$s .= "If you are new to wikipedia and want to get a user account, enter a user name, type and re-type a password.<br>\n" ;
	  	$s .= "<FORM action=\"$THESCRIPT?title=special:userLogin\" method=post><font face=courier>\n" ;
	  	$s .= "Your user name&nbsp; : <INPUT TABINDEX=1 TYPE=text NAME=USERNAME VALUE=\"$user->name\" SIZE=20><br>\n" ;
	  	$s .= "Your password&nbsp;&nbsp; : <INPUT TABINDEX=2 TYPE=password NAME=USERPASSWORD VALUE=\"$user->password\" SIZE=20><br>\n" ;
	  	$s .= "Retype password : <INPUT TABINDEX=2 TYPE=password NAME=RETYPE VALUE=\"\" SIZE=20> (new users only)<br>\n" ;
  		$s .= "<INPUT TABINDEX=4 TYPE=checkbox NAME=REMEMBERPASSWORD $check>Remember my password (as a cookie).<br>\n" ;
	  	$s .= "<input TABINDEX=5 type=submit name=loginattempt value=\"Log in\">\n" ;
  		$s .= "</font></FORM>\n" ;
		}

	return $s ;
	}

function editUserSettings () {
	global $THESCRIPT ;
	global $ButtonSave ;
	global $vpage , $user ;
	$vpage->title = "User Settings" ;
	if ( !$user->isLoggedIn ) return "You are not logged in! [[special:userLogin|Log in]] or go to the [[:Main Page|Main Page]]" ;
	$ret = "" ;

	if ( isset ( $ButtonSave ) ) {
		unset ( $ButtonSave ) ;
		global $QuickBar , $NewTopics , $UnderlineLinks , $AutoTalk , $ShowHover , $ROWS , $COLS , $doSkin ;
		global $OLDPASSWORD , $NEWPASSWORD , $RETYPEPASSWORD , $EMAIL , $RESULTSPERPAGE , $doJustify , $ChangesLayout ;
		global $SHOWSTRUCTURE , $HOURDIFF , $NumberHeadings ;
		if ( $RESULTSPERPAGE < 2 ) $RESULTSPERPAGE = 20 ;
		$user->options["quickBar"] = $QuickBar ;
		$user->options["markupNewTopics"] = $NewTopics ;
		$user->options["underlineLinks"] = $UnderlineLinks ;
		$user->options["autoTalk"] = $AutoTalk ;
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
		$user->email = $EMAIL ;
		$user->options["hourDiff"] = $HOURDIFF ;

		if ( $OLDPASSWORD == $user->password ) {
			if ( $NEWPASSWORD == $RETYPEPASSWORD ) $user->password = $NEWPASSWORD ;
			else $ret .= "<h1><font color=red>The new passwords didn't match. PASSWORD UNCHANGED!</font></h1>\n" ;
			}

		$user->saveSettings () ;
		$user->loadSettings () ;
		$msg = "<font color=red size=+1>Your settings have been saved!</font>" ;
		}

	$ret .= "<b>You are logged in as [[user:$user->name|$user->name]]. ";
	$ret .= "Your internal ID is $user->id.</b><br>\n";
	$ret .= "You can get help [[wikipedia:Help/User preferences|here]].\n" ;
	$ret .= "<nowiki><FORM action=\"$THESCRIPT?title=special:editUserSettings\" method=post>" ;
	$ret .= "<table border=1 bordercolor=".$user->options["borderColor"]." cellspacing=0 cellpadding=2>" ;

	# QuickBar options
	$qb[$user->options["quickBar"]] = "checked" ;
	$ret .= "<tr><td valign=top nowrap><b>QuickBar Settings :</b><br>\n" ;
	$ret .= "<input type=radio value=none ".$qb["none"]." name=QuickBar>None (Standard)<br>\n" ;
	$ret .= "<input type=radio value=left ".$qb["left"]." name=QuickBar>Left<br>\n" ;
	$ret .= "<input type=radio value=right ".$qb["right"]." name=QuickBar>Right\n" ;

	# Password change
	$ret .= "</td><td valign=top nowrap><b>Change password :</b><br><font face=courier>\n" ;
  	$ret .= "Old password&nbsp; &nbsp; : <INPUT TYPE=password NAME=OLDPASSWORD VALUE=\"\" SIZE=20><br>\n" ;
  	$ret .= "New password&nbsp; &nbsp; : <INPUT TYPE=password NAME=NEWPASSWORD VALUE=\"\" SIZE=20><br>\n" ;
  	$ret .= "Retype password : <INPUT TYPE=password NAME=RETYPEPASSWORD VALUE=\"\" SIZE=20>\n" ;
	$ret .= "</font></td></tr>" ;

	# Underline Links options
	$ul[$user->options["underlineLinks"]] = "checked" ;
	$ret .= "<tr><td valign=top nowrap><b>Underline Links :</b><br>\n" ;
	$ret .= "<input type=radio value=yes ".$ul["yes"]." name=UnderlineLinks>Yes (Standard)<br>\n" ;
	$ret .= "<input type=radio value=no ".$ul["no"]." name=UnderlineLinks>No<br>\n" ;

	# New Topics options
	$nt[$user->options["markupNewTopics"]] = "checked" ;
	$ret .= "</td><td valign=top nowrap><b>New Topics :</b><br>\n" ;
	$ret .= "<input type=radio value=normal ".$nt["normal"]." name=NewTopics>Normal (Standard)<br>\n" ;
	$ret .= "<input type=radio value=red ".$nt["red"]." name=NewTopics>Red<br>\n" ;
	$ret .= "</td></tr>" ;

	# Automatic Talk page
	$at[$user->options["autoTalk"]] = "checked" ;
	$ret .= "<tr><td valign=top nowrap><b>Automatic /Talk page :</b><br>\n" ;
	$ret .= "<input type=radio value=yes ".$at["yes"]." name=AutoTalk>Yes<br>\n" ;
	$ret .= "<input type=radio value=no ".$at["no"]." name=AutoTalk>No (Standard)<br>\n" ;

	# Show Hover
	$sh[$user->options["showHover"]] = "checked" ;
	$ret .= "</td><td valign=top nowrap><b>Show hoverbox over wiki links :</b><br>\n" ;
	$ret .= "<input type=radio value=yes ".$sh["yes"]." name=ShowHover>Yes (Standard)<br>\n" ;
	$ret .= "<input type=radio value=no ".$sh["no"]." name=ShowHover>No<br>\n" ;
	$ret .= "</td></tr>" ;

	# Cols and rows
	$ret .= "<tr><td valign=top rowspan=3 nowrap><b>Textbox dimensions :</b><br><font face=courier>\n" ;
  	$ret .= "Cols : <INPUT TYPE=text NAME=COLS VALUE=\"".$user->options["cols"]."\" SIZE=5><br>\n" ;
  	$ret .= "Rows : <INPUT TYPE=text NAME=ROWS VALUE=\"".$user->options["rows"]."\" SIZE=5></font><br><br>\n" ;
	$ret .= "<font face=courier size=-1>Recommended sizes:<br>";
	$ret .= "1280x1024 : 135x40<br>\n" ;
	$ret .= "1024x 768 : <br>\n" ;
	$ret .= "&nbsp;800x 600 : </font>\n" ;

	$ret .= "</td><td valign=top nowrap><b>Your email address :</b><br>" ;
  	$ret .= "<font face=courier>Email : <INPUT TYPE=text NAME=EMAIL VALUE=\"".$user->email."\" SIZE=35></font>\n" ;

	$ret .= "</td></tr><tr><td valign=top nowrap>" ;
	$ret .= "<b>Search Settings :</b><br>" ;
  	$ret .= "<font face=courier>Results per page : ";
	$ret .= "<INPUT TYPE=text NAME=RESULTSPERPAGE VALUE=\"".$user->options["resultsPerPage"]."\" SIZE=18></font>\n" ;

	$jf[$user->options["justify"]] = "checked" ;
	$ret .= "</td></tr><tr><td><b>Justify paragraphs :</b><br>" ;
	$ret .= "<input type=radio value=yes ".$jf["yes"]." name=doJustify>Yes<br>\n" ;
	$ret .= "<input type=radio value=no ".$jf["no"]." name=doJustify>No (Standard)\n" ;
	$ret .= "</td></tr>" ;

	# Skin
	$sk[$user->options["skin"]] = "checked" ;
	$ret .= "<tr><td valign=top nowrap><b>Skin :</b><br>\n" ;
	$ret .= "<input type=radio value=None ".$sk["None"]." name=doSkin>None (Standard)<br>\n" ;
	$ret .= "<input type=radio value=\"Star Trek\" ".$sk["Star Trek"]." name=doSkin>Star Trek<br>\n" ;

	# Changes layout
	$cl[$user->options["changesLayout"]] = "checked" ;
	$ret .= "</td><td valign=top nowrap><b>New Topics :</b><br>\n" ;
	$ret .= "<input type=radio value=classic ".$cl["classic"]." name=ChangesLayout>Classic (Standard)<br>\n" ;
	$ret .= "<input type=radio value=table ".$cl["table"]." name=ChangesLayout>As a table<br>\n" ;
	$ret .= "</td></tr>" ;

	# hourDiff
	$ret .= "<tr><td><b>Time difference :</b><br>\n" ;
  	$ret .= "<font face=courier><INPUT TYPE=text NAME=HOURDIFF VALUE=\"".$user->options["hourDiff"]."\" SIZE=3> hours</font>\n" ;


	# Show parent page and subpage structure in QuickBar    TURNED OFF
#	$shs[$user->options["showStructure"]] = "checked" ;
#	$ret .= "<tr><td valign=top nowrap><b>Show subpage structure :</b><br>\n" ;
#	$ret .= "<input type=radio value=yes ".$shs["yes"]." name=SHOWSTRUCTURE>Yes<br>\n" ;
#	$ret .= "<input type=radio value=no ".$shs["no"]." name=SHOWSTRUCTURE>No (Standard)<br>\n" ;

	# UNUSED
	$nh[$user->options["numberHeadings"]] = "checked" ;
	$ret .= "</td><td valign=top nowrap><b>Auto-number headings :</b><br>\n" ;
	$ret .= "<input type=radio value=yes ".$nh["yes"]." name=NumberHeadings>Yes (Standard)<br>\n" ;
	$ret .= "<input type=radio value=no ".$nh["no"]." name=NumberHeadings>No<br>\n" ;
	$ret .= "</td></tr>" ;

	$ret .= "<tr><td><center><input type=submit value=Save name=ButtonSave></center></td>" ;
	$ret .= "<td><center><input type=reset value=Reset name=ButtonReset></center></td></tr>" ;

	$ret .= "</table></FORM>$msg</nowiki>" ;
	return $ret ;
	}

function WantedPages () {
	global $THESCRIPT ;
	global $linkedLinks , $unlinkedLinks , $vpage ;
	$vpage->special ( "The Most Wanted Pages" ) ;
	$vpage->namespace = "" ;
	$allPages = array () ;
	$ret = "'''These articles don't exist, but other articles link to them!''' (the top 50)<br>\n" ;

	$connection = getDBconnection () ;
	mysql_select_db ( "wikipedia" , $connection ) ;
	$sql = "SELECT cur_title,cur_linked_links,cur_unlinked_links FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		$allPages[ucfirst($s->cur_title)] = -999999999999 ; # Effectively removing existing topics from list
		$u = explode ( "\n" , $s->cur_unlinked_links ) ; foreach ( $u as $x ) $allPages[ucfirst($x)] += 1 ;
		}
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;


	arsort ( $allPages ) ;
	$allPages = array_slice ( $allPages , 0 , 200 ) ; # Reducing needed memory
	$ti = new wikiTitle ;
	$k = array_keys ( $allPages ) ;

	$a = 0 ;
	$o = array () ;
	while ( count ( $o ) < 50 ) {
		$x = $k[$a] ;
		$a++ ;
		$ti->setTitle ( $x ) ;
		if ( $x != "" and !$ti->doesTopicExist() )
			array_push ( $o , "<li><a href=\"$THESCRIPT?action=edit&title=$x\">".$ti->getNiceTitle($x)."</a> is wanted by ".$allPages[$x]." articles.</li>\n" ) ;
		}
	$ret .= "<nowiki><ol>".implode ( "" , $o )."</ol></nowiki>" ;
	return $ret ;
	}

function LonelyPages () {
	global $THESCRIPT ;
	global $linkedLinks , $unlinkedLinks , $vpage ;
	$vpage->special ( "The Orphans" ) ;
	$vpage->namespace = "" ;
	$allPages = array () ;
	$ret = "'''These articles exist, but no articles link to them!''' (the first 50)\n\n" ;

	$connection = getDBconnection () ;
	mysql_select_db ( "wikipedia" , $connection ) ;
	$sql = "SELECT cur_title,cur_linked_links,cur_unlinked_links FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) {
		$allPages[ucfirst($s->cur_title)] = $allPages[ucfirst($s->cur_title)] * 1 ;
		$u = explode ( "\n" , $s->cur_linked_links ) ; foreach ( $u as $x ) $allPages[ucfirst($x)] += 1 ;
		$u = explode ( "\n" , $s->cur_unlinked_links ) ; foreach ( $u as $x ) $allPages[ucfirst($x)] += 1 ;
		}
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;

	asort ( $allPages ) ;
	$allPages = array_slice ( $allPages , 0 , 50 ) ;

	$orphans = array () ;
	$v = array_keys ( $allPages ) ;
	foreach ( $v as $x ) {
		if ( $allPages[$x] == 0 )
			array_push ( $orphans , $x ) ;
		}

	asort ( $orphans ) ;
	foreach ( $orphans as $x )
		$ret .= "[[$x|".$vpage->getNiceTitle($x)."]]<br>\n" ;
	return $ret ;
	}

function AllPages () {
	global $THESCRIPT ;
	global $linkedLinks , $unlinkedLinks , $vpage ;
	$vpage->special ( "All Pages Index" ) ;
	$vpage->namespace = "" ;
	$ret = "'''These are all the articles in the database!'''\n\n" ;
	$connection = getDBconnection () ;
	mysql_select_db ( "wikipedia" , $connection ) ;
	$sql = "SELECT cur_title FROM cur ORDER BY cur_title" ;
	$result = mysql_query ( $sql , $connection ) ;
	$ret .= "<nowiki>" ;
	while ( $s = mysql_fetch_object ( $result ) )
		$ret .= "<a  href=\"$THESCRIPT?title=$s->cur_title\">".$vpage->getNiceTitle($s->cur_title)."</a><br>" ;
	$ret .= "</nowiki>" ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
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
	$vpage = new WikiPage ;
	$vpage->special ( "Search" ) ;
	$r = array () ;
	$s = "" ;

	if ( $search == "" ) $s = "<h2>As you were searching for the void, you just found it.</h2>" ;
	else {
		if ( !isset ( $startat ) ) $startat = 1 ;
		$perpage = $user->options["resultsPerPage"] ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "SELECT * FROM cur WHERE cur_title LIKE \"%$search%\" OR cur_text LIKE \"%$search%\" ORDER BY cur_title" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result != "" ) {
			$totalcnt = 0 ;
			while ( $s = mysql_fetch_object ( $result ) ) {
				if ( $totalcnt+1 >= $startat and count ( $r ) < $perpage )
					array_push ( $r , $s ) ;
					$totalcnt++ ;
				}
			mysql_free_result ( $result ) ;
			}
		mysql_close ( $connection ) ;
		}

	if ( $s == "" and count ( $r ) == 0 ) {
		$s = "<h2>Sorry, we were unable to find an article containing \"$search\" in any article title or body.</h2>" ;
	} else if ( $s == "" ) {
		$n = count ( $r ) ;
		if ( $n > 1 ) { $z1 = "s" ; $z2 = "were" ; }
		else $z2 = "was" ;
		$s .= "<table width=100% bgcolor=#FFFFCC><tr><td><font size=+1><b>Wikipedia Articles</b></font><br>\n" ;
		$s .= "$totalcnt occurence$z1 of ''$search'' $z2 found. " ;
		$s .= "For each article, you can see its first paragraph and the first paragraph that contains ''$search''.</td></tr></table>\n" ;
		$s .= "<table>" ;
		$realcnt = $startat ;
		$minlen = strlen ( $realcnt + count ( $r ) ) ;
		foreach ( $r as $x ) {
			$u = spliti ( "\n" , $x->cur_text ) ;
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
			if ( $startat != 1 ) $s .= "<a href=\"$THESCRIPT?search=$search&startat=".$last."\">&lt;&lt;</a> | ";
			for ( $a = 1 ; $a <= $totalcnt ; $a += $perpage ) {
				if ( $a != 1 ) $s .= " | " ;
				if ( $a != $startat ) $s .= "<a href=\"$THESCRIPT?search=$search&startat=$a\">";
				$s .= "$a-" ;
				$s .= $a+$perpage-1 ;
				if ( $a != $startat ) $s .= "</a>" ;
				}
			if ( $startat != $a-$perpage ) $s .= " | <a href=\"$THESCRIPT?search=$search&startat=".$next."\">&gt;&gt;</a>";
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

	$vpage->special ( "User List" ) ;
	$vpage->namespace = "" ;
	$ret = "'''These are all wikipedia users (that have an account)!'''\n\n" ;
	$connection = getDBconnection () ;
	mysql_select_db ( "wikipedia" , $connection ) ;
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
	$connection=getDBconnection() ;
	mysql_select_db ( "wikipedia" , $connection ) ;
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
	$ret = "<h2>Loading random page [[$thelink|".$nt."]]...</h2>" ;
	$headerScript .= "<nowiki><META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$THESCRIPT?title=$thelink\"></nowiki>" ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;

	return $ret ;
	}


function recentchanges () {
	global $THESCRIPT , $user ;
	global $vpage , $maxcnt , $daysAgo ;
	$vpage->special ( "Recent Changes" ) ;
	$vpage->makeSecureTitle() ;
	if ( !isset ( $maxcnt ) ) $maxcnt = 100 ;
	if ( !isset ( $daysAgo ) ) $daysAgo = 3 ;

	$ret = "<nowiki>" ;
	$ret .= "These are the last <b>$maxcnt</b> of the changes made on Wikipedia in the last <b>$daysAgo</b> days.<br>\n" ;
	$ret .= "View the last " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&daysAgo=$daysAgo&maxcnt=50\">50</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&daysAgo=$daysAgo&maxcnt=100\">100</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&daysAgo=$daysAgo&maxcnt=250\">250</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&daysAgo=$daysAgo&maxcnt=500\">500</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&daysAgo=$daysAgo&maxcnt=1000\">1000</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&daysAgo=$daysAgo&maxcnt=2500\">2500</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&daysAgo=$daysAgo&maxcnt=5000\">5000</a> " ;
	$ret .= "changes.<br>\n" ; 
	$ret .= "View the last " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&maxcnt=$maxcnt&daysAgo=1\">1 day</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&maxcnt=$maxcnt&daysAgo=2\">2 days</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&maxcnt=$maxcnt&daysAgo=3\">3 days</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&maxcnt=$maxcnt&daysAgo=5\">5 days</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&maxcnt=$maxcnt&daysAgo=7\">7 days</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:RecentChanges&maxcnt=$maxcnt&daysAgo=14\">14 days</a><br>\n" ;
	$ret .= "</nowiki>" ;
	$arr = array () ;

	$mindate = date ( "Ymd000000" , time () - $daysAgo*24*60*60 ) ;
	$mindate = timestampAddHour ( $mindate , $user->options["hourDiff"] ) ;
	$connection=getDBconnection() ;
	mysql_select_db ( "wikipedia" , $connection ) ;
	$sql = "SELECT cur_timestamp,cur_title,cur_comment,cur_user,cur_user_text,cur_minor_edit FROM cur WHERE cur_timestamp>$mindate ORDER BY cur_timestamp DESC LIMIT $maxcnt" ;
	$result = mysql_query ( $sql , $connection ) ;
	while ( $s = mysql_fetch_object ( $result ) ) array_push ( $arr , $s ) ;
	mysql_free_result ( $result ) ;

	$d = array () ;
	foreach ( $arr as $s ) {
		$i = 0 ;
		$j = tsc ( $s->cur_timestamp ) ;
		$ja = date ( "Ymd000000" , $j ) ;
		$jb = date ( "Ymd000000" , $j + 24*60*60 ) ;
		$sql = "SELECT count(old_id) AS cnt FROM old WHERE old_title=\"".$s->cur_title."\" AND old_timestamp>=$ja AND old_timestamp<=$jb" ;
		$result = mysql_query ( $sql , $connection ) ;
		if ( $result != "" ) {
			$t = mysql_fetch_object ( $result ) ;
			if ( $t != "" ) $i = $t->cnt + 1 ;
			mysql_free_result ( $result ) ;
			}
		if ( $i < 2 ) $i = "" ;
		$s->changes = $i ;
		array_push ( $d , $s ) ;
		}
	$arr = $d ;
	$d = array () ;

	mysql_close ( $connection ) ;
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
	global $vpage , $maxcnt , $daysAgo ;
	$vpage->special ( "New pages" ) ;
	$vpage->makeSecureTitle() ;
	if ( !isset ( $maxcnt ) ) $maxcnt = 100 ;
	if ( !isset ( $daysAgo ) ) $daysAgo = 3 ;
	$names = array () ;

	$ret = "<nowiki>" ;
	$ret .= "These are the last <b>$maxcnt</b> new pages on Wikipedia in the last <b>$daysAgo</b> days.<br>\n" ;
	$ret .= "View the last " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&daysAgo=$daysAgo&maxcnt=50\">50</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&daysAgo=$daysAgo&maxcnt=100\">100</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&daysAgo=$daysAgo&maxcnt=250\">250</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&daysAgo=$daysAgo&maxcnt=500\">500</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&daysAgo=$daysAgo&maxcnt=1000\">1000</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&daysAgo=$daysAgo&maxcnt=2500\">2500</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&daysAgo=$daysAgo&maxcnt=5000\">5000</a> " ;
	$ret .= "new pages.<br>\n" ; 
	$ret .= "View the last " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&maxcnt=$maxcnt&daysAgo=1\">1 day</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&maxcnt=$maxcnt&daysAgo=2\">2 days</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&maxcnt=$maxcnt&daysAgo=3\">3 days</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&maxcnt=$maxcnt&daysAgo=5\">5 days</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&maxcnt=$maxcnt&daysAgo=7\">7 days</a> | " ;
	$ret .= "<a href=\"$THESCRIPT?title=special:NewPages&maxcnt=$maxcnt&daysAgo=14\">14 days</a><br>\n" ;
	$ret .= "</nowiki>" ;
	$arr = array () ;

	$mindate = date ( "Ymd000000" , time () - $daysAgo*24*60*60 ) ;
	$mindate = timestampAddHour ( $mindate , $user->options["hourDiff"] ) ;
	$connection=getDBconnection() ;
	mysql_select_db ( "wikipedia" , $connection ) ;

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
	mysql_close ( $connection ) ;
	
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
	global $THESCRIPT ;
	global $user ;
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

	$xyz = new WikiTitle ;
	$editTypes = array ( "0"=>"" , "1"=>"<font color=cyan>M</font>" , "2"=>"<font color=green>N</font>" ) ;
	$ret = " <b>Legend :</b> ".$editTypes["1"]."=Minor edit ; ".$editTypes["2"]."=New article." ;
	if ( $user->options["changesLayout"] == "table" ) $ret .= "<table width=100% border=0 cellpadding=2 cellspacing=0>\n" ;
	else $ret .= "<ul>\n" ; 
	$dummy = "$THESCRIPT?x=y" ;
	foreach ( $arr as $s ) {
		$nt = $xyz->getNiceTitle ( $s->cur_title ) ;
		$day = date ( "l, F d, Y" , tsc ( $s->cur_timestamp ) ) ;
		$time = date ( "H:i" , tsc ( $s->cur_timestamp ) ) ;
		if ( $day != $lastDay ) {
			$lastDay = $day ;
			if ( $user->options["changesLayout"] == "table" ) $ret.="<tr><td width=100% colspan=7".$user->options["tabLine0"]."><b>$day</b></td></tr>";
			else $ret .= "</ul><b>$day</b><ul>\n" ;
			$color = $color1 ;
			}
		$u = $s->cur_user_text ;
		if ( $s->cur_user != 0 ) {
			$xyz->title = $u ;
			$xyz->makeSecureTitle () ;
			$u = "<a href=\"$THESCRIPT?title=user:$xyz->secureTitle\">$u</a>" ;
			}
		$comment = trim($s->cur_comment) ;
		if ( $comment == "*" ) $comment = "" ;
		$o_comment = $comment ;
		if ( $s->cur_minor_edit == 1 ) $comment = "<font size=-1><i>$comment</i></font>" ;
		$minor = $editTypes[$s->cur_minor_edit] ;

		if ( $user->options["changesLayout"] == "table" ) $t = "<tr><td$color valign=top width=0%>" ;
		else $t = "<li>" ;

		if ( $s->version == "current" ) $t .= "<a href=\"$THESCRIPT?title=$s->cur_title&diff=yes\">(diff)</a>&nbsp;" ;
		else if ( $s->version != "" ) $t .= "<a href=\"$THESCRIPT?title=$s->cur_title&oldID=$s->old_id&version=$s->version&diff=yes\">(diff)</a>&nbsp;" ;
		else $t .= "<a href=\"$THESCRIPT?title=$s->cur_title&diff=yes\">(diff)</a>" ;

		if ( $user->options["changesLayout"] == "table" ) $t .= "</td><td$color valign=top>" ;
		else $t .= " " ;

		if ( $s->version == "current" ) $t .= "<a href=\"$THESCRIPT?title=$s->cur_title\">$nt</a></td>" ;
		else if ( $s->version != "" ) $t .= "<a href=\"$THESCRIPT?title=$s->cur_title&oldID=$s->old_id&version=$s->version\">$nt ($s->version)</a></td>" ;
		else $t .= "<a href=\"$THESCRIPT?title=$s->cur_title\">$nt</a>" ;

		if ( $user->options["changesLayout"] == "table" ) $t .= "<td$color valign=top width=0% nowrap>$time</td>" ;
		else $t = str_replace ( "</td>" , "; " , $t ) . " $time" ;

		$noc = $s->changes ;
		if ( $noc > 1 ) $noc = "$noc <a href=\"$THESCRIPT?action=history&title=$s->cur_title\">changes</a>" ;
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
			else $t .= "<a href=\"$THESCRIPT?title=user:$v->secureTitle\">$s->cur_user_text</a></td>" ;
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
	global $THESCRIPT ;
	global $user ;
	if ( !$user->isLoggedIn ) return "NOT LOGGED IN!" ;

	# Modifying user_watch
	$separator = "\n" ;
	$a = getMySQL ( "user" , "user_watch" , "user_id=$user->id" ) ;
	if ( $m == "yes" ) $a = modifyArray ( $a , $separator , $t , $t ) ;
	else $a = modifyArray ( $a , $separator , $t ) ;
	setMySQL ( "user" , "user_watch" , $a , "user_id=$user->id" ) ;

	$ret = "Watching $t ($m)" ;
	$ret .= "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL='$THESCRIPT?title=".urlencode($t)."'\">" ;
	return $ret ;
	}

function WatchList () {
	global $THESCRIPT ;
	global $vpage , $user ;
	$vpage->special ( "My watchlist" ) ;
	$ret = "'''Currently, you are watching the following articles :''' " ;
	$a = getMySQL ( "user" , "user_watch" , "user_id=$user->id" ) ;
	$separator = "\n" ;
	$b = explode ( $separator , $a ) ;
	$vpage->namespace = "" ;

	$n = array () ;
	foreach ( $b as $x )
		$n[$x] = getMySQL ( "cur" , "cur_timestamp" , "cur_title=\"$x\"" ) ;
	arsort ( $n ) ;
	$k = array_keys ( $n ) ;

	$connection=getDBconnection() ;
	mysql_select_db ( "wikipedia" , $connection ) ;
	$arr = array () ;
	foreach ( $k as $x ) {
		$sql = "SELECT * FROM cur WHERE cur_title=\"$x\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		$s = mysql_fetch_object ( $result ) ;
		array_push ( $arr , $s ) ;
		mysql_free_result ( $result ) ;
		}
	mysql_close ( $connection ) ;
	$ret .= recentChangesLayout ( $arr ) ;	

	return $ret ;
	}

function statistics () {
	global $THESCRIPT ;
	$connection=getDBconnection() ;
	mysql_select_db ( "wikipedia" , $connection ) ;
	$ret = "" ;
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
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title LIKE \"%/Talk\" OR cur_title LIKE \"Talk:%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$talkPages = $s->number ;
	$ret .= "<li>There are $nf1$talkPages$nf2 <b>Talk</b> pages</li>" ;
	mysql_free_result ( $result ) ;

	# , NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title NOT LIKE \"talk:%\" AND cur_text LIKE \"%,%\"" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$commaPages = $s->number ;
	$ret .= "<li>There are $nf1$commaPages$nf2 with a comma that are <i>not</i> <b>/Talk</b> pages</li>" ;
	mysql_free_result ( $result ) ;

	# WIKIPEDIA NOT /TALK
	$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title NOT LIKE \"talk:%\" AND cur_title LIKE \"%ikipedia%\"" ;
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
#	$oldPages = $s->number - $talkPages;
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
	$ret .= "<li>There are currently $nf1$numUser$nf2 [[special:ListUsers|users]] signed up.</li>" ;
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
	return $ret ;
	}

function upload () {
	global $THESCRIPT ;
	global $removeFile , $xtitle , $removeFile , $Upload , $Upload_name , $no_copyright ;
	global $user , $vpage ;
	$vpage->special ( "Upload Page" ) ;
	$isSysop = in_array ( "is_sysop" , $user->rights ) ;
	$xtitle = "File upload page";
	$ret = "<nowiki>" ;

	$message = "" ;

	if (isset($removeFile)) {
		if ( !$isSysop ) return "You are neither an editor nor a sysop. Return to the <a href=\"$THESCRIPT?action=upload\">Upload page</a>" ;
		if (is_file("upload/$removeFile") ) unlink ("./upload/$removeFile");
		$message = "File <b>$removeFile</b> deleted!" ;

		# Appending log page "log:Uploads"
		$now = date ( "Y-m-d H:i:s" , time () ) ;
		$logText = "*On $now, [[user:$user->name|$user->name]] deleted file '''$removeFile'''\n" ;
		makeLog ( "log:Uploads" , $logText , "Deletion of file $removeFile" ) ;

		unset ( $removeFile ) ;
	} else if (isset($Upload_name) or isset($Upload)) {
		if ( $no_copyright != "AFFIRMED" ) return "<nowiki>You need to affirm that the file is not violating copygights. Return to the <a href=\"$THESCRIPT?title=special:upload\">Upload page</a></nowiki>" ;
		$Upload_name = ereg_replace(" ", "_", $Upload_name);
		$abc = split("\.", $Upload_name);

		$num = exec ("df");
		$readata = substr($num,(strpos($num, "%")-2),2);

		if ($readata > 96) {
			$ret .= "<body bgcolor=white>\n";
			$ret .= "<br><b>Sorry, we are almost out of disk space. We can't let you upload any files right now.</b>\n";
			return $ret ;
			}

		copy ( $Upload , "./upload/$Upload_name" ) ;
		system ("chmod 777 ./upload/$Upload_name");
		$message = "File <b>$Upload_name</b> was successfully uploaded!" ;

		# Appending log page "log:Uploads"
		global $REMODE_ADDR ;
		$now = date ( "Y-m-d H:i:s" , time () ) ;
		$userText = "[[user:$user->name|$user->name]]" ;
		if ( $user->name == "" ) $userText = $REMODE_ADDR ;
		$logText = "*On $now, $userText uploaded file '''$Upload_name'''\n" ;
		makeLog ( "log:Uploads" , $logText , "Upload of file $UploadName" ) ;

		unset ( $Upload_name ) ;
	}

	if ( $message != "" ) $ret .= "<font color=red>$message</font><br>\n" ;

	$ret .= "<h2>Instructions:</h2><ul>\n";
	$ret .= "<li><strong>Use this form to upload various files</strong></li>\n";
	$ret .= "<li>To replace a previously-uploaded file (e.g., a\n";
	$ret .= "new version of the article), simply re-upload the\n";
	$ret .= "same file. But first look below and make sure you\n";
	$ret .= "haven't changed the name.</li>\n";
	$ret .= "<li><strong>Here's how to upload your file. </strong>Click\n";
	$ret .= "&quot;Browse...&quot; to your find the file you\n";
	$ret .= "want to upload on your hard drive. This will open\n";
	$ret .= "a &quot;Choose file&quot; dialogue window.</li>\n";
	$ret .= "<li>When you've found the file, click &quot;Open.&quot;\n";
	$ret .= "This will select the file and close the &quot;Choose\n";
	$ret .= "file&quot; dialogue window.</li>\n";
	$ret .= "<li>Don't forget to check the copyright statement!</li>\n";
	$ret .= "<li>Then click &quot;Upload.&quot; The file will start uploading. This may take some time, if it's\n";
	$ret .= "a big file and you have a slow Internet connection.</li>\n";
	$ret .= "<li>A message will tell you when the file has successfully uploaded.</li>\n";
	$ret .= "<li>You can upload as many files you like. Please don't try to crash our server, ha ha.</li>\n";
	$ret .= "<li>All uploads and deletions are logged in the <a href=\"$THESCRIPT?title=Log:Uploads\">uploads log</a>.</li>\n";
	$ret .= "</ul>\n";

	$ret .= " <form enctype=\"multipart/form-data\" action=\"$THESCRIPT?title=special:upload\" method=post>\n";
	$ret .= " <input type=hidden name=max value=20096>\n";
	$ret .= " <input name=Upload type=\"file\"><br>\n";
	$ret .= " <input type=hidden name=update value=1>\n";
	$ret .= " <input type=hidden name=step value=$step>\n";
	$ret .= "<INPUT TYPE=checkbox NAME=\"no_copyright\" VALUE=\"AFFIRMED\">I hereby affirm that this file is <b>not copyrighted</b>, or that I own the copyright for this file and donate it to Wikipedia.<br>\n" ;
	$ret .= " <input type=submit value=UPLOAD>\n";
	$ret .= "</form>\n";

	if (is_dir("upload")) {
		$mydir = dir("upload");
			while ($entry = $mydir->read()) {
			if ($entry != "." and $entry != "..")
				$file = "yes";
			}
		$mydir->close();

		if ($file == "yes") {
			$ret .= "<h2>Previously-uploaded files:</h2>";
			$mydir = opendir("upload");
			$i = 0;
			$ret .= "<table border=1 width=\"100%\">\n";
			$ret .= "<tr><th>File</th><th>Size (byte)</th>";
			if ( $isSysop )
				$ret .= "<th>File removal</th>";
			$ret .= "</tr>\n" ;
			while ($entry = readdir($mydir)) {
				if ($entry != '.' && $entry != '..') {
					$ret .= "<tr><td align=center>" ;
					$ret .= "<a href=upload/$entry>$entry</a></td>";
					$ret .= "<td align=center>".filesize("upload/$entry")." bytes</td>";
					if ( $isSysop )
						$ret .= "<td align=center><a href=\"$THESCRIPT?title=special:upload&removeFile=$entry\">Click here to remove $entry.</a></td>" ;
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
	global $THESCRIPT ;
	global $vpage ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;
	$vpage->special ( "History of $title" ) ;
	$vpage->makeSecureTitle () ;

	$a = array () ;
	$connection = getDBconnection () ;
	mysql_select_db ( "wikipedia" , $connection ) ;
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
	mysql_close ( $connection ) ;

	$i = count ( $a ) ;
	$k = array_keys ( $a ) ;
	foreach ( $k as $x ) {
		if ( $i != count ( $a ) ) $a[$x]->version = $i ;
		else $a[$x]->version = "current" ;
		$i-- ;
		}

	$t = recentChangesLayout ( $a ) ;
	$t = "<b>This is the history of <a href=\"$THESCRIPT?title=$title\">$title</a></b>".$t ;

	$ret = $vpage->getHeader() ;
	$ret .= $vpage->getMiddle($t) ;
	$ret .= $vpage->getFooter() ;
	return $ret ;
	}

function special_pages () {
	global $THESCRIPT ;
	global $vpage , $user ;
	$vpage->special ( "Special Pages" ) ;
	$ret = "<b>This is a list of special pages.</b><br>" ;
	$ret .= "Some of them are only available if you are logged in. If you are logged in, you can have this list automatically displayed on the right or left of each page as a QuickBar.<br><br>" ;
	$ret .= $vpage->getQuickBar () ;
	return $ret ;
	}

function ShortPages () {
	global $THESCRIPT ;
	global $user , $vpage , $startat ;
	if ( !isset ( $startat ) ) $startat = 1 ;
	$perpage = $user->options["resultsPerPage"] ;
	if ( $perpage == 0 ) $perpage = 20 ;
	
	$vpage->special ( "Short 'stub' articles" ) ;
	$vpage->namespace = "" ;
	$ret = "'''These are all the articles in the database, sorted by length!'''\n\n" ;
	$connection = getDBconnection () ;
	mysql_select_db ( "wikipedia" , $connection ) ;
	$sql = "SELECT COUNT(*) AS number FROM cur" ;
	$result = mysql_query ( $sql , $connection ) ;
	$s = mysql_fetch_object ( $result ) ;
	$total = $s->number ;
	$sql = "SELECT * FROM cur ORDER BY LENGTH(cur_text)" ;
	$result = mysql_query ( $sql , $connection ) ;
	$cnt = 1 ;
	$color1 = $user->options["tabLine1"] ;
	$color2 = $user->options["tabLine2"] ;
	$color = $color1 ;
	$ret .= "<table width=100%>\n" ;
	while ( $s = mysql_fetch_object ( $result ) and $cnt < $startat+$perpage ) {
		if ( $cnt >= $startat ) {
			$ret .= "<tr><td$color align=right nowrap>$cnt</td>" ;
			$ret .= "<td$color align=right nowrap>(".strlen($s->cur_text)." chars)</td>\n" ;
			$ret .= "<td$color width=100% valign=top>[[$s->cur_title]]</td></tr>\n";
			if ( $color == $color1 ) $color = $color2 ;
			else $color = $color1 ;
			}
		$cnt++ ;
		}
	$ret .= "</table>\n" ;
	
	$ret .= "<nowiki>" ;
	$before = $startat - $perpage ; $fin = $before + $perpage - 1 ;
	if ( $startat > 1 ) $ret .= "<a href=\"$THESCRIPT?title=special:ShortPages&startat=$before\">$before-$fin&lt;&lt;</a> &nbsp;" ;
	$after = $startat + $perpage ; $fin = $after+$perpage - 1 ; if ( $fin > $total ) $fin = $total ;
	if ( $after-1 < $total ) $ret .= "<a href=\"$THESCRIPT?title=special:ShortPages&startat=$after\">&gt;&gt;$after-$fin</a>" ;
	$ret .= "</nowiki>" ;
	mysql_free_result ( $result ) ;
	mysql_close ( $connection ) ;
	return $ret ;
	}

function removeFromLinkList ( $item , $link ) {
	$connection = getDBconnection () ;
	mysql_select_db ( "wikipedia" , $connection ) ;
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

	mysql_close ( $connection ) ;	
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
	global $vpage ;
	$target = str_replace ( "\\\\" , "\\" , $target ) ;
	$target = str_replace ( "\\\\" , "\\" , $target ) ;
	$vpage = new WikiPage ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$ti = $vpage->secureTitle ;
	$vpage->special ( "Deleting article '$target'" ) ;
	$vpage->makeSecureTitle () ;
	if ( !in_array ( "is_sysop" , $user->rights ) ) return "<h1>You are not allowed to delete this page!</h1>" ;
	if ( $iamsure == "yes" ) {
		$ret = "<h2>'$target' has been removed.</h2>" ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
		$sql = "DELETE FROM cur WHERE cur_title=\"$target\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		mysql_close ( $connection ) ;

		# Appending log page "log:Page Deletions"
		$now = date ( "Y-m-d H:i:s" , time () ) ;
		$logTarget = $vpage->getNiceTitle ( $target ) ;
		$logText = "*On $now, [[user:$user->name|$user->name]] permanently deleted page '''$logTarget'''\n" ;
		makeLog ( "log:Page Deletions" , $logText , "Permanent deletion of $logTarget" ) ;

		removeFromLinkList ( "cur_linked_links" , $target ) ;
		removeFromLinkList ( "cur_unlinked_links" , $target ) ;
	} else {
		$ret = "<h2>You are about to delete the article \"$target\" and its complete history!<br>\n" ;
		$ret .= "If you are absolutely sure you want to do this, " ;
		$ret .= "<a href=\"$THESCRIPT?title=special:deletepage&target=$target&iamsure=yes\">click here</a>.</h2>" ;
		}
	return "<nowiki>$ret</nowiki>" ;
	}

# A little hack for direct MySQL access; for sysops only!
function askSQL () {
	global $THESCRIPT ;
	global $Save , $question ;
	$ret = "" ;
	if ( isset ( $Save ) ) {
		$ret .= "$question<br>" ;
		unset ( $Save ) ;
		$connection = getDBconnection () ;
		mysql_select_db ( "wikipedia" , $connection ) ;
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
		mysql_close ( $connection ) ;

		$ret .= "<table width=100% border=1 bordercolor=black cellspacing=0 cellpadding=2><tr>" ;
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