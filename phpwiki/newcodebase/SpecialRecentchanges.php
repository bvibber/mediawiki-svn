<?

function wfSpecialRecentchanges()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $days, $limit; # From query string

	$wgOut->addWikiText( wfMsg( "recentchangestext" ) );

	if ( ! $days ) {
		$days = $wgUser->getOption( "rcdays" );
		if ( ! $days ) { $days = 3; }
	}
	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 100; }
	}
	$cutoff = date( "YmdHis", time() - ( $days * 86400 ) );

	$conn = wfGetDB();
	$sql = "SELECT cur_id,cur_namespace,cur_title,cur_user," .
	  "cur_comment,cur_user_text,cur_timestamp,cur_minor_edit FROM cur " .
	  "WHERE cur_timestamp > '{$cutoff}' " .
	  "ORDER BY cur_timestamp DESC LIMIT {$limit}";
	wfDebug( "SC: 1: $sql\n" );

	$res = mysql_query( $sql, $conn );
	if ( ! $res ) {
		$wgOut->databaseError( wfMsg( "rcloaderr" ) );
		return;
	}
	$sk = $wgUser->getSkin();
	$s = $lastdate = "";
	while ( $line = mysql_fetch_object( $res ) ) {
		$t = $line->cur_timestamp;
		$d = $wgLang->dateFromTimestamp( $t );

		if ( $d != $lastdate ) {
			if ( "" != $lastdate ) { $s .= "</ul>\n"; }
			$s .= "<h4>{$d}</h4>\n<ul>";
			$lastdate = $d;
		}
		$s .= $sk->historyLine( 0, $t, $line->cur_user,
		  $line->cur_user_text, $line->cur_namespace, $line->cur_title,
		  "", $line->cur_comment, ( $line->cur_minor_edit > 0 ) );
	}
	$s .= "</ul>\n";
	$wgOut->addHTML( $s );
}

?>
