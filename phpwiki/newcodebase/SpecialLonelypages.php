<?

function wfSpecialLonelypages()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $wgServer, $wgScript;
	global $limit, $offset; # From query string
	$fname = "wfSpecialRecentchanges";

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "searchlimit" );
		if ( ! $limit ) { $limit = 20; }
	}
	if ( ! $offset ) { $offset = 0; }

	$sql = "SELECT cur_title FROM cur LEFT JOIN links ON " .
	  "cur_id=l_to WHERE l_to IS NULL AND cur_namespace=0 AND " .
	  "cur_is_redirect=0 ORDER BY cur_title LIMIT {$offset}, {$limit}";
	$res = wfQuery( $sql, $fname );

	$sk = $wgUser->getSkin();

	$top = str_replace( "$1", $limit, wfMsg( "showingorphans" ) );
	$top = str_replace( "$2", $offset+1, $top );
	$wgOut->addHTML( "<p>{$top}\n" );

	$prev = str_replace( "$1", $limit, wfMsg( "orphanprev" ) );
	$next = str_replace( "$1", $limit, wfMsg( "orphannext" ) );

	if ( 0 != $offset ) {
		$po = $offset - $limit;
		if ( $po < 0 ) { $po = 0; }

		$plink = "<a href=\"$wgServer$wgScript?title=Special%3ALonelypages" .
		  "&amp;limit={$limit}&amp;offset={$po}\">{$prev}</a>";
	} else { $plink = $prev; }

	$no = $offset + $limit;
	$nlink = "<a href=\"$wgServer$wgScript?title=Special%3ALonelypages" .
	  "&amp;limit={$limit}&amp;offset={$no}\">{$next}</a>";

	$sl = str_replace( "$1", $plink, wfMsg( "orphanlinks" ) );
	$sl = str_replace( "$2", $nlink, $sl );
	$wgOut->addHTML( "<br>{$sl}\n" );

	$s = "<ul>";
	while ( $obj = wfFetchObject( $res ) ) {
		$link = $sk->makeKnownLink( $obj->cur_title, "" );
		$s .= "<li>{$link}</li>\n";
	}
	wfFreeResult( $res );
	$s .= "</ul>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

?>
