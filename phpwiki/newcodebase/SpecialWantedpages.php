<?

function wfSpecialWantedpages()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $limit, $offset; # From query string
	$fname = "wfSpecialWantedpages";

	$wgOut->setRobotpolicy( "noindex,nofollow" );
	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 50; }
	}
	if ( ! $offset ) { $offset = 0; }

	$sql = "SELECT bl_to, COUNT( DISTINCT bl_from ) as nlinks " .
	  "FROM brokenlinks GROUP BY bl_to HAVING nlinks > 1 " .
	  "ORDER BY nlinks DESC LIMIT {$offset}, {$limit}";
	$res = wfQuery( $sql, $fname );

	$sk = $wgUser->getSkin();

	$top = SearchEngine::showingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = SearchEngine::viewPrevNext( $offset, $limit,
	  $wgLang->specialpage( "Wantedpages" ) );
	$wgOut->addHTML( "<br>{$sl}\n" );

	$s = "<ol start=" . ( $offset + 1 ) . ">";
	while ( $obj = wfFetchObject( $res ) ) {
		$nt = Title::newFromDBkey( $obj->bl_to );

		$plink = $sk->makeBrokenLink( $nt->getPrefixedText(), "" );
		$nl = str_replace( "$1", $obj->nlinks, wfMsg( "nlinks" ) );
		$nlink = $sk->makeKnownLink( $wgLang->specialPage(
		  "Whatlinkshere" ), $nl, "target=" . $nt->getPrefixedURL() );

		$s .= "<li>{$plink} ({$nlink})</li>\n";
	}
	wfFreeResult( $res );
	$s .= "</ol>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

?>
