<?
include_once ( "special_recentchangeslayout.php" ) ;

function doHistory ( $title ) {
	global $THESCRIPT , $vpage , $wikiSQLServer , $wikiHistoryTitle , $wikiCurrentVersion , $wikiHistoryHeader ;
	$vpage = new WikiPage ;
	$vpage->SetTitle ( $title ) ;
	$ti = $vpage->secureTitle ;
	$url = $vpage->url;
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
	$t = "<b>".str_replace(array("$1","$2"),array($url,$title),$wikiHistoryHeader)."</b>".$t ;

	$ret = $vpage->getHeader() ;
	$ret .= $vpage->getMiddle($t) ;
	$ret .= $vpage->getFooter() ;
	return $ret ;
	}

?>