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
		$h = substr( $t, 8, 2 ) . ":" . substr( $t, 10, 2 );
		$c = $line->cur_comment;

		if ( 0 == $line->cur_user ) {
			$u = $line->cur_user_text;
		} else {
			$u = $sk->makeInternalLink( "User:{$line->cur_user_text}",
			  "{$line->cur_user_text}" );
		}
		$t = Title::makeName( $line->cur_namespace, $line->cur_title );
		$tl = $sk->makeInternalLink( "$t", "", "oldid={$id}" );

		if ( $d != $lastdate ) {
			if ( "" != $lastdate ) { $s .= "</ul>\n"; }
			$s .= "<h4>{$d}</h4>\n<ul>";
			$lastdate = $d;
		}
		$s .= "<li>{$tl}; {$h} . . . {$u}";
		if ( "" != $c && "*" != $c ) {
			$s .= " <em>({$c})</em>";
		}
		$s .= "</li>\n";
	}
	$s .= "</ul>\n";
	$wgOut->addHTML( $s );
}

?>
