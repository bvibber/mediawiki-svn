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
		$nl = str_replace( "$1", $obj->nlinks, wfMsg( "nlinks" ) );
		$link = $sk->makeKnownLink( $obj->bl_to, "" );
		$s .= "<li>{$link} ({$nl})</li>\n";
	}
	wfFreeResult( $res );
	$s .= "</ul>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

?>
