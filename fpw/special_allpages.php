<?
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
	return $ret ;
	}
?>
