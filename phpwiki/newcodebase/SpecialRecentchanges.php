<?

function wfSpecialRecentchanges()
{
	global $wgUser, $wgOut, $wgLang;
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
	$cutoff = time() - ( $days * 86400 );

	$conn = wfGetDB();
	$sql = "SELECT cur_id,cur_namespace,cur_title,cur_user," .
	  "cur_comment,cur_user_text,cur_timestamp,cur_minor_edit FROM cur " .
	  "WHERE (UNIX_TIMESTAMP(cur_timestamp) > {$cutoff}) " .
	  "ORDER BY cur_timestamp DESC LIMIT {$limit}";
	wfDebug( "SC: 1: $sql\n" );

	$res = mysql_query( $sql, $conn );
	if ( ! $res ) {
		$wgOut->databaseError( wfMsg( "rcloaderr" ) );
		return;
	}
	$s = "";
	$lastdate = "";
	while ( $line = mysql_fetch_object( $res ) ) {
		$t = $line->cur_timestamp;
		$d = $wgLang->dateFromTimestamp( $t );
		$h = substr( $t, 8, 2 ) . ":" . substr( $t, 10, 2 );
		$c = $line->cur_comment;

		if ( 0 == $line->cur_user ) {
			$u = $line->cur_user_text;
		} else {
			$u = "[[User:{$line->cur_user_text}|{$line->cur_user_text}]]";
		}
		$nt = Title::newFromDBkey( $line->cur_title );
		$nt->setNamespace( $line->cur_namespace );
		$t = $nt->getPrefixedText();

		if ( $d != $lastdate ) {
			$s .= "'''{$d}'''\n";
			$lastdate = $d;
		}
		$s .= "* [[{$t}]]; {$h} . . . {$u}";
		if ( "" != $c && "*" != $c ) {
			$s .= "''' ({$c})'''";
		}
		$s .= "\n";
	}
	$wgOut->addWikiText( $s );
}

?>
