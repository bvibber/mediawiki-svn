<?

function wfSpecialUnusedimages()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $limit, $offset; # From query string
	$fname = "wfSpecialUnusedimages";

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 50; }
	}
	if ( ! $offset ) { $offset = 0; }

	$sql = "SELECT img_name,img_user,img_user_text,img_timestamp,img_description " .
	  "FROM image LEFT JOIN imagelinks ON img_name=il_to WHERE il_to IS NULL " .
	  "ORDER BY img_timestamp LIMIT {$offset}, {$limit}";
	$res = wfQuery( $sql, $fname );

	$sk = $wgUser->getSkin();

	$top = SearchEngine::showingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = SearchEngine::viewPrevNext( $offset, $limit, "Special:Unusedimages" );
	$wgOut->addHTML( "<br>{$sl}\n" );

	$s = "<ol start=" . ( $offset + 1 ) . ">";
	while ( $obj = wfFetchObject( $res ) ) {
		$name = $obj->img_name;
		$dlink = $sk->makeKnownLink( "Image:{$name}", wfMsg( "imgdesc" ) );
		$ilink = "<a href=\"" . wfImageUrl( $name ) . "\">{$name}</a>";

		$d = $wgLang->timeanddate( $obj->img_timestamp, true );
		$u = $obj->img_user;
		$ut = $obj->img_user_text;
		$c = $obj->img_description;

		if ( 0 == $u ) { $ul = $ut; }
		else { $ul = $sk->makeLink( "User:{$ut}", $ut ); }

		$s .= "<li>({$dlink}) {$ilink} . . {$d} . . {$ul}";

		if ( "" != $c && "*" != $c ) { $s .= " <em>({$c})</em>"; }
		$s .= "</li>\n";
	}
	wfFreeResult( $res );
	$s .= "</ol>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

?>
