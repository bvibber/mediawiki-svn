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
	$note = str_replace( "$1", $limit, wfMsg( "rcnote" ) );
	$note = str_replace( "$2", $days, $note );
	$wgOut->addHTML( "<hr>\n{$note}\n<br>" );

	$cl = rcCountLink( 50, $days ) . " | " . rcCountLink( 100, $days ) . " | " .
	  rcCountLink( 250, $days ) . " | " . rcCountLink( 500, $days ) . " | " .
	  rcCountLink( 1000, $days ) . " | " . rcCountLink( 2500, $days ) . " | " .
	  rcCountLink( 5000, $days );
	$dl = rcDaysLink( $limit, 1 ) . " | " . rcDaysLink( $limit, 3 ) . " | " .
	  rcDaysLink( $limit, 7 ) . " | " . rcDaysLink( $limit, 14 ) . " | " .
	  rcDaysLink( $limit, 30 ) . " | " . rcDaysLink( $limit, 90 );
	$note = str_replace( "$1", $cl, wfMsg( "rclinks" ) );
	$note = str_replace( "$2", $dl, $note );
	$wgOut->addHTML( "{$note}\n" );

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

function rcCountLink( $lim, $d )
{
	global $wgUser;
	$sk = $wgUser->getSkin();
	$s = $sk->makeLink( "Special:Recentchanges", "{$lim}",
	  "days={$d}&limit={$lim}" );
	return $s;
}

function rcDaysLink( $lim, $d )
{
	global $wgUser;
	$sk = $wgUser->getSkin();
	$s = $sk->makeLink( "Special:Recentchanges", "{$d}",
	  "days={$d}&limit={$lim}" );
	return $s;
}

?>
