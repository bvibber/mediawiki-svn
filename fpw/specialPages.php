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

include_once ( "special_wantedpages.php" ) ;
include_once ( "special_lonelypages.php" ) ;
include_once ( "special_editusersettings.php" ) ;
include_once ( "special_recentchangeslayout.php" ) ;
include_once ( "special_recentchanges.php" ) ;
include_once ( "special_newpages.php" ) ;
include_once ( "special_watchlist.php" ) ;
include_once ( "special_statistics.php" ) ;
include_once ( "special_shortpages.php" ) ;
include_once ( "special_dohistory.php" ) ;
include_once ( "special_upload.php" ) ;
include_once ( "special_dosearch.php" ) ;

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
		$ret .= "<a  href=\"".wikiLink(nurlencode($s->cur_title))."\">".$vpage->getNiceTitle($s->cur_title)."</a><br>\n" ;
	$ret .= "</nowiki>" ;
	mysql_free_result ( $result ) ;
	#mysql_close ( $connection ) ;
	return $ret ;
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
	$headerScript .= "<nowiki><META HTTP-EQUIV=Refresh CONTENT=\"0; URL=".wikiLink(nurlencode($thelink))."\"></nowiki>" ;
	mysql_free_result ( $result ) ;
	#mysql_close ( $connection ) ;

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
		$ret = "<font size=\"+2\">".str_replace(array("$1","$2"),array($target,nurlencode($target)),$wikiDeleteAsk)."</font>" ;
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
		$ret .= "<br><br><FORM action=\"".wikiLink("special:protectpage&target=".nurlencode($target)."&protecting=yes")."\" method=post>$wikiProtectCurrent\n" ;
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
