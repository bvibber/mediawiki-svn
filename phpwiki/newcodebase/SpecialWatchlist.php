<?
include_once( "SpecialRecentchanges.php" );

function wfSpecialWatchlist()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $days, $limit, $target; # From query string
	$fname = "wfSpecialWatchlist";

	$wgOut->setPagetitle( wfMsg( "watchlist" ) );
	$sub = str_replace( "$1", $wgUser->getName(), wfMsg( "watchlistsub" ) );
	$wgOut->setSubtitle( $sub );
	$wgOut->setRobotpolicy( "index,follow" );

	if ( ! $days ) {
		$days = $wgUser->getOption( "rcdays" );
		if ( ! $days ) { $days = 3; }
	}
	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 100; }
	}
	$cutoff = date( "YmdHis", time() - ( $days * 86400 ) );
	#TODO: add links to change cutoffs

	$wl = $wgUser->getWatchlist();
	$nw = count( $wl );
	$sql = "SELECT cur_id,cur_namespace,cur_title,cur_user,cur_comment," .
	  "cur_user_text,cur_timestamp,cur_minor_edit,cur_is_new FROM cur WHERE " .
	  "cur_timestamp > '{$cutoff}' AND (";

	$note = str_replace( "$1", $limit, wfMsg( "rcnote" ) );
	$note = str_replace( "$2", $days, $note );
	$wgOut->addHTML( "\n<hr>\n{$note}\n<br>" );
	$note = rcLimitlinks( $days, $limit, "Watchlist" );
	$wgOut->addHTML( "{$note}\n" );

	$first = true;
	foreach ( $wl as $title ) {
		if ( "" == trim( $title ) ) { continue; }
		$nt = Title::newFromDBkey( $title );
		if ( ! $first ) { $sql .= " OR "; }
		$first = false;

		$ns = $nt->getNamespace();
		$t = wfStrencode( $nt->getDBkey() );
		$sql .= "(cur_namespace={$ns} AND cur_title='{$t}')";

		if ( ! Namespace::isTalk( $ns ) ) {
			$tns = Namespace::getTalk( $ns );
			$sql .= " OR (cur_namespace={$tns} AND cur_title='{$t}')";
		}
	}
	if ( $first ) {
		$wgOut->addHTML( wfMsg( "nowatchlist" ) );
		return;
	}
	$sql .= ") ORDER BY cur_timestamp DESC LIMIT {$limit}";
	$res = wfQuery( $sql, $fname );

	$sk = $wgUser->getSkin();
	$s = $sk->beginRecentChangesList();

	while ( $obj = wfFetchObject( $res ) ) {
		$ts = $obj->cur_timestamp;
		$u = $obj->cur_user;
		$ut = $obj->cur_user_text;
		$ns = $obj->cur_namespace;
		$ttl = $obj->cur_title;
		$com = $obj->cur_comment;
		$me = ( $obj->cur_minor_edit > 0 );
		$new = ( $obj->cur_is_new  > 0 );

		$s .= $sk->recentChangesLine( $ts, $u, $ut, $ns, $ttl, $com, $me, $new );
	}
	$s .= $sk->endRecentChangesList();

	wfFreeResult( $res );
	$wgOut->addHTML( $s );
}

?>
