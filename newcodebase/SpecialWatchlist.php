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

	if ( ! isset( $days ) ) {
		$days = $wgUser->getOption( "rcdays" );
		if ( ! $days ) { $days = 3; }
	}
	if ( ! isset( $limit ) ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 100; }
	}
	if ( $days == 0 ) {
		$docutoff = '';
	} else {
		$docutoff = "cur_timestamp > '" .
		  date( "YmdHis", time() - ( $days * 86400 ) )
		  . "' AND";
	}
	if ( $limit == 0 ) {
		$dolimit = "";
	} else {
		$dolimit = "LIMIT $limit";
	}
	
	$uid = $wgUser->getID();
	if( $uid == 0 ) {
		$wgOut->addHTML( wfMsg( "nowatchlist" ) );
		return;
	}

	$sql = "SELECT DISTINCT wl_page,talk.cur_id AS id,talk.cur_namespace AS namespace,talk.cur_title AS title,
	  talk.cur_user AS user,talk.cur_comment AS comment,talk.cur_user_text AS user_text,
	  talk.cur_timestamp AS timestamp,talk.cur_minor_edit AS minor_edit,talk.cur_is_new AS is_new
	  FROM cur as page, cur as talk, watchlist
	  WHERE wl_user={$uid} AND wl_page=page.cur_id AND page.cur_title=talk.cur_title
	  AND talk.cur_namespace | 1=page.cur_namespace | 1
	  ORDER BY talk.cur_timestamp DESC {$dolimit}";
	$res = wfQuery( $sql, $fname );
	if ( wfNumRows( $res ) == 0 ) {
		$wgOut->addHTML( wfMsg( "nowatchlist" ) );
		return;
	}

	$note = str_replace( "$1", $limit, wfMsg( "rcnote" ) );
	$note = str_replace( "$2", $days, $note );
	$wgOut->addHTML( "\n<hr>\n{$note}\n<br>" );
	$note = rcDayLimitlinks( $days, $limit, "Watchlist", "", true );
	$wgOut->addHTML( "{$note}\n" );

	$sk = $wgUser->getSkin();
	$s = $sk->beginRecentChangesList();

	while ( $obj = wfFetchObject( $res ) ) {
		$ts = $obj->timestamp;
		$u = $obj->user;
		$ut = $obj->user_text;
		$ns = $obj->namespace;
		$ttl = $obj->title;
		$com = $obj->comment;
		$me = ( $obj->minor_edit > 0 );
		$new = ( $obj->is_new  > 0 );
		$watched = ($obj->id == $obj->wl_page);

		$s .= $sk->recentChangesLine( $ts, $u, $ut, $ns, $ttl, $com, $me, $new, $watched );
	}
	$s .= $sk->endRecentChangesList();

	wfFreeResult( $res );
	$wgOut->addHTML( $s );
}

?>
