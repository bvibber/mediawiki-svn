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

include_once ( "special_userlogin.php" ) ;
include_once ( "special_userlogout.php" ) ;
include_once ( "special_allpages.php" ) ;
include_once ( "special_listusers.php" ) ;
include_once ( "special_randompage.php" ) ;
include_once ( "special_special_pages.php" ) ;
include_once ( "special_deletepage.php" ) ;


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
