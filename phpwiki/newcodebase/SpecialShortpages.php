<?

function wfSpecialShortpages()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $limit, $offset; # From query string
	$fname = "wfSpecialShortpages";

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "searchlimit" );
		if ( ! $limit ) { $limit = 20; }
	}
	if ( ! $offset ) { $offset = 0; }

	$sql = "SELECT cur_title, LENGTH(cur_text) AS len FROM cur " .
	  "WHERE cur_namespace=0 AND cur_is_redirect=0 ORDER BY " .
	  "LENGTH(cur_text) LIMIT {$offset}, {$limit}";
	$res = wfQuery( $sql, $fname );

	$sk = $wgUser->getSkin();

	$top = SearchEngine::showingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = SearchEngine::viewPrevNext( $offset, $limit,
	  "title=Special%3AShortpages" );
	$wgOut->addHTML( "<br>{$sl}\n" );

	$s = "<ul>";
	while ( $obj = wfFetchObject( $res ) ) {
		$nb = str_replace( "$1", $obj->len, wfMsg( "nbytes" ) );
		$link = $sk->makeKnownLink( $obj->cur_title, "" );
		$s .= "<li>{$link} ({$nb})</li>\n";
	}
	wfFreeResult( $res );
	$s .= "</ul>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

?>
