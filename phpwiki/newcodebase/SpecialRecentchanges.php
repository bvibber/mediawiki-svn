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
	$sql = "SELECT old_id,old_namespace,old_title,old_user," .
	  "old_comment,old_user_text,old_timestamp,old_minor_edit FROM old " .
	  "WHERE old_timestamp > '{$cutoff}' " .
	  "ORDER BY old_timestamp DESC LIMIT {$limit}";
	wfDebug( "SC: 2: $sql\n" );

	$res2 = mysql_query( $sql, $conn );
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

	$count1 = mysql_num_rows( $res );
	$obj1 = mysql_fetch_object( $res );
	--$count1;
	$count2 = mysql_num_rows( $res2 );
	$obj2 = mysql_fetch_object( $res2 );
	--$count2;

	$sk = $wgUser->getSkin();
	$s = $sk->beginRecentChangesList();

	while ( $limit ) {
		if ( ( 0 == $count1 ) && ( 0 == $count2 ) ) { break; }

		if ( ( 0 == $count2 ) ||
		  ( ( 0 != $count1 ) && 
		  ( $obj1->cur_timestamp >= $obj2->old_timestamp ) ) ) {
			$ts = $obj1->cur_timestamp;
			$u = $obj1->cur_user;
			$ut = $obj1->cur_user_text;
			$ns = $obj1->cur_namespace;
			$ttl = $obj1->cur_title;
			$com = $obj1->cur_comment;
			$me = ( $obj1->cur_minor_edit > 0 );

			$obj1 = mysql_fetch_object( $res );
			--$count1;
		} else {
			$ts = $obj2->old_timestamp;
			$u = $obj2->old_user;
			$ut = $obj2->old_user_text;
			$ns = $obj2->old_namespace;
			$ttl = $obj2->old_title;
			$com = $obj2->old_comment;
			$me = ( $obj2->old_minor_edit > 0 );

			$obj2 = mysql_fetch_object( $res2 );
			--$count2;
		}
		$s .= $sk->recentChangesLine( $ts, $u, $ut, $ns, $ttl, $com, $me );
		--$limit;
	}
	$s .= $sk->endRecentChangesList();

	mysql_free_result( $res2 );
	mysql_free_result( $res );
	$wgOut->addHTML( $s );
}

function rcCountLink( $lim, $d )
{
	global $wgUser;
	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( "Special:Recentchanges", "{$lim}",
	  "days={$d}&amp;limit={$lim}" );
	return $s;
}

function rcDaysLink( $lim, $d )
{
	global $wgUser;
	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( "Special:Recentchanges", "{$d}",
	  "days={$d}&amp;limit={$lim}" );
	return $s;
}

?>
