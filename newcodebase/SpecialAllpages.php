<?

function wfSpecialAllpages()
{
	global $wgUser, $wgOut;

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 50; }
	}
	if ( ! $offset ) { $offset = 0; }

	$top = SearchEngine::showingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = SearchEngine::viewPrevNext( $offset, $limit,
	  "title=Special%3AAllpages" );
	$wgOut->addHTML( "<br>{$sl}\n<ul>" );

	$sql = "SELECT cur_namespace,cur_title FROM cur ORDER BY " .
	  "cur_namespace,cur_title LIMIT {$offset}, {$limit}";
	$res = wfQuery( $sql, "wfSpecialAllpages" );

	$sk = $wgUser->getSkin();
	while ( $s = wfFetchObject( $res ) ) {
		$l = $sk->makeKnownLink( Title::makeName( $s->cur_namespace,
		  $s->cur_title ), "" );
		$wgOut->addHTML( "<li>{$l}</li>\n" );
	}
	wfFreeResult( $res );
	$wgOut->addHTML( "</ul><p>{$sl}\n" );
}

?>
