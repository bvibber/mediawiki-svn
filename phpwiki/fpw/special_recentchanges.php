<?
include_once ( "special_recentchangeslayout.php" ) ;

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

?>