<?
include_once ( "special_recentchangeslayout.php" ) ;

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

	$connection=getDBconnection() ;

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
	
	uasort ( $arr , "newPages_timeSort" ) ;
	while ( count ( $arr ) > $maxcnt ) array_pop ( $arr ) ;

	$ret .= recentChangesLayout($arr) ;
	return $ret ;
	}

?>
