<?

function wfSpecialRecentchanges()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $days, $limit, $hideminor, $from; # From query string
	$fname = "wfSpecialRecentchanges";

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
	if(preg_match('/^[0-9]{14}$/', $from) and $from > $cutoff) {
		$cutoff = $from;
	} else {
		unset($from);
	}

	$sk = $wgUser->getSkin();

	if ( ! isset( $hideminor ) ) {
		$hideminor = $wgUser->getOption( "hideminor" );
	}
	if ( $hideminor ) {
		$hidem = "AND rc_minor=0";
		$mlink = $sk->makeKnownLink( $wgLang->specialPage( "Recentchanges" ),
	  	  WfMsg( "show" ), "days={$days}&limit={$limit}&hideminor=0" );
	} else {
		$hidem = "";
		$mlink = $sk->makeKnownLink( $wgLang->specialPage( "Recentchanges" ),
	  	  WfMsg( "hide" ), "days={$days}&limit={$limit}&hideminor=1" );
	}

	$sql = "SELECT rc_cur_id,rc_namespace,rc_title,rc_user,rc_new," .
	  "rc_comment,rc_user_text,rc_timestamp,rc_minor FROM recentchanges " .
	  "WHERE rc_timestamp > '{$cutoff}' {$hidem} " .
	  "ORDER BY rc_timestamp DESC LIMIT {$limit}";
	$res = wfQuery( $sql, $fname );

	if(isset($from)) {
		$note = str_replace( "$1", $limit, wfMsg( "rcnotefrom" ) );
		$note = str_replace( "$2", $wgLang->timeanddate( $from, true ), $note );
	} else {
		$note = str_replace( "$1", $limit, wfMsg( "rcnote" ) );
		$note = str_replace( "$2", $days, $note );
	}
	$wgOut->addHTML( "\n<hr>\n{$note}\n<br>" );

	$cl = rcCountLink( 50, $days ) . " | " . rcCountLink( 100, $days ) . " | " .
	  rcCountLink( 250, $days ) . " | " . rcCountLink( 500, $days );
	$dl = rcDaysLink( $limit, 1 ) . " | " . rcDaysLink( $limit, 3 ) . " | " .
	  rcDaysLink( $limit, 7 ) . " | " . rcDaysLink( $limit, 14 ) . " | " .
	  rcDaysLink( $limit, 30 );
	$note = str_replace( "$1", $cl, wfMsg( "rclinks" ) );
	$note = str_replace( "$2", $dl, $note );

	$note = str_replace( "$3", $mlink, $note);

	$now = date( "YmdHis" );
	$note .= "<br>\n" . str_replace( "$1",
	  $sk->makeKnownLink( $wgLang->specialPage( "Recentchanges" ),
	  $wgLang->timeanddate( $now, true ), "from=$now" ),
	  wfMsg( "rclistfrom" ) );

	$wgOut->addHTML( "{$note}\n" );

	$count1 = wfNumRows( $res );
	$obj1 = wfFetchObject( $res );

	$s = $sk->beginRecentChangesList();
	while ( $limit ) {
		if ( ( 0 == $count1 ) ) { break; }

			$ts = $obj1->rc_timestamp;
			$u = $obj1->rc_user;
			$ut = $obj1->rc_user_text;
			$ns = $obj1->rc_namespace;
			$ttl = $obj1->rc_title;
			$com = $obj1->rc_comment;
			$me = ( $obj1->rc_minor > 0 );
			$new = ( $obj1->rc_new > 0 );

			$obj1 = wfFetchObject( $res );
			--$count1;
		if ( ! ( $hideminor && $me ) ) {
			$s .= $sk->recentChangesLine( $ts, $u, $ut, $ns, $ttl,
			  $com, $me, $new );
			--$limit;
		}
	}
	$s .= $sk->endRecentChangesList();

	wfFreeResult( $res );
	$wgOut->addHTML( $s );
}

function rcCountLink( $lim, $d )
{
	global $wgUser, $wgLang;
	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( $wgLang->specialPage( "Recentchanges" ),
	  "{$lim}", "days={$d}&limit={$lim}" );
	return $s;
}

function rcDaysLink( $lim, $d )
{
	global $wgUser, $wgLang;
	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( $wgLang->specialPage( "Recentchanges" ),
	  "{$d}", "days={$d}&limit={$lim}" );
	return $s;
}

?>
