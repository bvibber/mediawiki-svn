<?

function wfSpecialWatchlist()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $days, $limit, $target; # From query string
	$fname = "wfSpecialWatchlist";

	$wgOut->setPagetitle( wfMsg( "watchlist" ) );
	$sub = str_replace( "$1", $wgUser->getName(), wfMsg( "watchlistsub" ) );
	$wgOut->setSubtitle( $sub );
	$wgOut->setRobotpolicy( "index,follow" );

	$wl = $wgUser->getWatchlist();
	$nw = count( $wl );
	$sql = "SELECT cur_id,cur_namespace,cur_title,cur_user,cur_comment," .
	  "cur_user_text,cur_timestamp,cur_minor_edit FROM cur WHERE (";

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
	$sql .= ") ORDER BY cur_timestamp DESC";
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
