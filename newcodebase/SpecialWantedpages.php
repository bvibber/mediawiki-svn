<?

function wfSpecialWantedpages()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $limit, $offset; # From query string
	$fname = "wfSpecialWantedpages";

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 50; }
	}
	if ( ! $offset ) { $offset = 0; }

	$sql = "SELECT bl_to, COUNT( DISTINCT bl_from ) as nlinks " .
	  "FROM brokenlinks GROUP BY bl_to ORDER BY nlinks DESC " .
	  "LIMIT {$offset}, {$limit}";
	$res = wfQuery( $sql, $fname );

	$sk = $wgUser->getSkin();

	$top = SearchEngine::showingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = SearchEngine::viewPrevNext( $offset, $limit,
	  "title=Special%3AWantedpages" );
	$wgOut->addHTML( "<br>{$sl}\n" );

	$s = "<ul>";
	while ( $obj = wfFetchObject( $res ) ) {
		$nt = Title::newFromDBkey( $obj->bl_to );

		$plink = $sk->makeKnownLink( $nt->getPrefixedText(), "" );
		$nl = str_replace( "$1", $obj->nlinks, wfMsg( "nlinks" ) );
		$nlink = $sk->makeKnownLink( "Special:Whatlinkshere", $nl,
		  "target=" . $nt->getPrefixedURL() );

		$s .= "<li>{$plink} ({$nlink})</li>\n";
	}
	wfFreeResult( $res );
	$s .= "</ul>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

?>
