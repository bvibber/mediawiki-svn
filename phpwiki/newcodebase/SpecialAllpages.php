<?

function wfSpecialAllpages()
{
	global $wgUser, $wgOut, $wgLang, $limit, $offset;

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 50; }
	}
	if ( ! $offset ) { $offset = 0; }

	$top = wfShowingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = wfViewPrevNext( $offset, $limit,
	  $wgLang->specialPage( "Allpages" ) );
	$wgOut->addHTML( "<br>{$sl}\n<ol start=" . ( $offset + 1 ) . ">" );

	$sql = "SELECT cur_namespace,cur_title FROM cur ORDER BY " .
	  "cur_title LIMIT {$offset}, {$limit}";
	$res = wfQuery( $sql, "wfSpecialAllpages" );

	$sk = $wgUser->getSkin();
	while ( $s = wfFetchObject( $res ) ) {
		$l = $sk->makeKnownLink( Title::makeName( $s->cur_namespace,
		  $s->cur_title ), "" );
		$wgOut->addHTML( "<li>{$l}</li>\n" );
	}
	wfFreeResult( $res );
	$wgOut->addHTML( "</ol><p>{$sl}\n" );
}

?>
