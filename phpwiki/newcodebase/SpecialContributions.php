<?

function wfSpecialContributions()
{
	global $wgUser, $wgOut, $wgLang, $target, $limit, $days;
	$fname = "wfSpecialContributions";

	if ( "" == $target ) {
		$wgOut->errorpage( "notargettitle", "notargettext" );
		return;
	}
	if ( ! $days ) { $days = 14; }
	if ( ! $limit ) { $limit = 50; }
	$cutoff = date( "YmdHis", time() - ( $days * 86400 ) );

	$target = wfCleanQueryVar( $target );
	$nt = Title::newFromURL( $target );
	$nt->setNamespace( Namespace::getIndex( "User" ) );

	$sk = $wgUser->getSkin();
	$id = User::idFromName( $nt->getDBkey() );

	$ul = $sk->makeKnownLink( $nt->getPrefixedText(), $nt->getText() );
	$sub = str_replace( "$1", $ul, wfMsg( "contribsub" ) );
	$wgOut->setSubtitle( $sub );

	if ( 0 == $id ) {
		$sql = "SELECT cur_namespace,cur_title,cur_timestamp FROM cur " .
		  "WHERE cur_timestamp > '{$cutoff}' AND cur_user_text='" .
		  wfStrencode( $nt->getText() ) . "' " .
		  "ORDER BY cur_timestamp DESC LIMIT {$limit}";
		$res1 = wfQuery( $sql, $fname );

		$sql = "SELECT old_namespace,old_title,old_timestamp FROM old " .
		  "WHERE old_timestamp > '{$cutoff}' AND old_user_text='" .
		  wfStrencode( $nt->getText() ) . "' " .
		  "ORDER BY old_timestamp DESC LIMIT {$limit}";
		$res2 = wfQuery( $sql, $fname );
	} else {
		$sql = "SELECT cur_namespace,cur_title,cur_timestamp FROM cur " .
		  "WHERE cur_timestamp > '{$cutoff}' AND cur_user={$id} " .
		  "ORDER BY cur_timestamp DESC LIMIT {$limit}";
		$res1 = wfQuery( $sql, $fname );

		$sql = "SELECT old_namespace,old_title,old_timestamp FROM old " .
		  "WHERE old_timestamp > '{$cutoff}' AND old_user={$id} " .
		  "ORDER BY old_timestamp DESC LIMIT {$limit}";
		$res2 = wfQuery( $sql, $fname );
	}
	$nCur = wfNumRows( $res1 );
	$nOld = wfNumRows( $res2 );

	$note = str_replace( "$1", $limit, wfMsg( "ucnote" ) );
	$note = str_replace( "$2", $days, $note );
	$wgOut->addHTML( "<p>{$note}\n<br>" );

	$cl = ucCountLink( 50, $days ) . " | " . ucCountLink( 100, $days ) . " | " .
	  ucCountLink( 250, $days ) . " | " . ucCountLink( 500, $days ) . " | " .
	  ucCountLink( 1000, $days ) . " | " . ucCountLink( 2500, $days ) . " | " .
	  ucCountLink( 5000, $days );
	$dl = ucDaysLink( $limit, 1 ) . " | " . ucDaysLink( $limit, 3 ) . " | " .
	  ucDaysLink( $limit, 7 ) . " | " . ucDaysLink( $limit, 14 ) . " | " .
	  ucDaysLink( $limit, 30 ) . " | " . ucDaysLink( $limit, 90 );
	$note = str_replace( "$1", $cl, wfMsg( "rclinks" ) );
	$note = str_replace( "$2", $dl, $note );
	$wgOut->addHTML( "{$note}\n<p>" );

	if ( 0 == $nCur && 0 == $nOld ) {
		$wgOut->addHTML( wfMsg( "nocontribs" ) );
		return;
	}
	if ( 0 != $nCur ) { $obj1 = wfFetchObject( $res1 ); }
	if ( 0 != $nOld ) { $obj2 = wfFetchObject( $res2 ); }

	while ( $limit ) {
		if ( 0 == $nCur && 0 == $nOld ) { break; }

		if ( ( 0 == $nOld ) ||
		  ( ( 0 != $nCur ) &&
		  ( $obj1->cur_timestamp >= $obj2->old_timestamp ) ) ) {
			$ns = $obj1->cur_namespace;
			$t = $obj1->cur_title;
			$ts = $obj1->cur_timestamp;

			$obj1 = wfFetchObject( $res1 );
			--$nCur;
		} else {
			$ns = $obj2->old_namespace;
			$t = $obj2->old_title;
			$ts = $obj2->old_timestamp;

			$obj2 = wfFetchObject( $res2 );
			--$nOld;
		}
		$page = Title::makeName( $ns, $t );
		$link = $sk->makeKnownLink( $page, "" );
		$d = $wgLang->timeanddate( $ts );

		$wgOut->addHTML( "<li>{$d} {$link}</li>\n" );

		--$limit;
	}
	$wgOut->addHTML( "</ul>\n" );
}

function ucCountLink( $lim, $d )
{
	global $wgUser, $target;

	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( "Special:Contributions", "{$lim}",
	  "target={$target}&days={$d}&limit={$lim}" );
	return $s;
}

function ucDaysLink( $lim, $d )
{
	global $wgUser, $target;

	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( "Special:Contributions", "{$d}",
	  "target={$target}&days={$d}&limit={$lim}" );
	return $s;
}
?>
