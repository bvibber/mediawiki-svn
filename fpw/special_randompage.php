<?
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

	return $ret ;
	}
?>
