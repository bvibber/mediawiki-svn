<?

function wfSpecialContributions()
{
	global $wgUser, $wgOut, $wgLang, $target, $limit, $days, $hideminor;
	$fname = "wfSpecialContributions";
	$sysop = $wgUser->isSysop();

	if ( "" == $target ) {
		$wgOut->errorpage( "notargettitle", "notargettext" );
		return;
	}
	if ( ! $days ) { $days = 14; }
	if ( ! $limit ) { $limit = 50; }
	$cutoff = date( "YmdHis", time() - ( $days * 86400 ) );

	$target = wfCleanQueryVar( $target );
	$nt = Title::newFromURL( $target );
	$nt->setNamespace( Namespace::getUser() );

	$sk = $wgUser->getSkin();
	$id = User::idFromName( $nt->getText() );

	if ( 0 == $id ) { $ul = $nt->getText(); }
	else {
		$ul = $sk->makeKnownLink( $nt->getPrefixedText(), $nt->getText() );
	}
	$sub = str_replace( "$1", $ul, wfMsg( "contribsub" ) );
	$wgOut->setSubtitle( $sub );

	if ( ! isset( $hideminor ) ) {
		$hideminor = $wgUser->getOption( "hideminor" );
	}
	if ( $hideminor ) {
		$mlink = $sk->makeKnownLink( $wgLang->specialPage( "Contributions" ),
	  	  WfMsg( "show" ), "target=" . wfEscapeHTML( $nt->getPrefixedURL() ) .
		  "&days={$days}&limit={$limit}&hideminor=0" );
	} else {
		$mlink = $sk->makeKnownLink( $wgLang->specialPage( "Contributions" ),
	  	  WfMsg( "hide" ), "target=" . wfEscapeHTML( $nt->getPrefixedURL() ) .
		  "&days={$days}&limit={$limit}&hideminor=1" );
	}
	if ( $hideminor ) {
		$cmq = "AND cur_minor_edit=0";
		$omq = "AND old_minor_edit=0";
	} else { $cmq = $omq = ""; }

	if ( 0 == $id ) {
		$sql = "SELECT cur_namespace,cur_title,cur_timestamp FROM cur " .
		  "WHERE cur_timestamp > '{$cutoff}' AND cur_user_text='" .
		  wfStrencode( $nt->getText() ) . "' {$cmq} " .
		  "ORDER BY cur_timestamp DESC LIMIT {$limit}";
		$res1 = wfQuery( $sql, $fname );

		$sql = "SELECT old_namespace,old_title,old_timestamp FROM old " .
		  "WHERE old_timestamp > '{$cutoff}' AND old_user_text='" .
		  wfStrencode( $nt->getText() ) . "' {$omq} " .
		  "ORDER BY old_timestamp DESC LIMIT {$limit}";
		$res2 = wfQuery( $sql, $fname );
	} else {
		$sql = "SELECT cur_namespace,cur_title,cur_timestamp FROM cur " .
		  "WHERE cur_timestamp > '{$cutoff}' AND cur_user={$id} " .
		  "{$cmq} ORDER BY cur_timestamp DESC LIMIT {$limit}";
		$res1 = wfQuery( $sql, $fname );

		$sql = "SELECT old_namespace,old_title,old_timestamp FROM old " .
		  "WHERE old_timestamp > '{$cutoff}' AND old_user={$id} " .
		  "{$omq} ORDER BY old_timestamp DESC LIMIT {$limit}";
		$res2 = wfQuery( $sql, $fname );
	}
	$nCur = wfNumRows( $res1 );
	$nOld = wfNumRows( $res2 );

	$note = str_replace( "$1", $limit, wfMsg( "ucnote" ) );
	$note = str_replace( "$2", $days, $note );
	$wgOut->addHTML( "<p>{$note}\n<br>" );

	$cl = ucCountLink( 50, $days ) . " | " . ucCountLink( 100, $days ) . " | " .
	  ucCountLink( 250, $days ) . " | " . ucCountLink( 500, $days );
	$dl = ucDaysLink( $limit, 1 ) . " | " . ucDaysLink( $limit, 3 ) . " | " .
	  ucDaysLink( $limit, 7 ) . " | " . ucDaysLink( $limit, 14 ) . " | " .
	  ucDaysLink( $limit, 30 );
	$note = str_replace( "$1", $cl, wfMsg( "rclinks" ) );
	$note = str_replace( "$2", $dl, $note );
	$note = str_replace( "$3", $mlink, $note );
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
			$topmark = wfMsg ( "uctop" ) ;
			--$nCur;
		} else {
			$ns = $obj2->old_namespace;
			$t = $obj2->old_title;
			$ts = $obj2->old_timestamp;

			$obj2 = wfFetchObject( $res2 );
			$topmark = "" ;
			--$nOld;
		}
		$page = Title::makeName( $ns, $t );
		$link = $sk->makeKnownLink( $page, "" );
		if($sysop && $topmark != "") {
			$topmark .= " [". $sk->makeKnownLink( $page,
			  wfMsg( "rollbacklink" ), "action=rollback" ) ."]";
		}
		$d = $wgLang->timeanddate( $ts, true );

		$wgOut->addHTML( "<li>{$d} {$link}{$topmark}</li>\n" );

		--$limit;
	}
	$wgOut->addHTML( "</ul>\n" );
}

function ucCountLink( $lim, $d )
{
	global $wgUser, $wgLang, $target;

	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( $wgLang->specialPage( "Contributions" ),
	  "{$lim}", "target={$target}&days={$d}&limit={$lim}" );
	return $s;
}

function ucDaysLink( $lim, $d )
{
	global $wgUser, $wgLang, $target;

	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( $wgLang->specialPage( "Contributions" ),
	  "{$d}", "target={$target}&days={$d}&limit={$lim}" );
	return $s;
}
?>
