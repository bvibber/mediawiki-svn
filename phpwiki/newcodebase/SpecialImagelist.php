<?

function wfSpecialImagelist()
{
	global $wgUser, $wgOut, $wgLang, $sort, $limit;

	$conn = wfGetDB();
	$sql = "SELECT img_size,img_name,img_user,img_user_text," .
	  "img_description,img_timestamp FROM image ORDER BY ";

	$byname = wfMsg( "byname" );
	$bydate = wfMsg( "bydate" );
	$bysize = wfMsg( "bysize" );

	if ( "bysize" == $sort ) {
		$sql .= "img_size DESC";
		$st = $bysize;
	} else if ( "byname" == $sort ) {
		$sql .= "img_name";
		$st = $byname;
	} else {
		$sql .= "img_timestamp DESC";
		$st = $bydate;
	}
	if ( ! isset( $limit ) ) { $limit = 50; }
	if ( 0 == $limit ) {
		$lt = wfMsg( "all" );
	} else {
		$lt = "${limit}";
		$sql .= " LIMIT {$limit}";
	}
	$res = wfQuery( $sql, $conn, "wfSpecialImagelist" );

	$wgOut->addHTML( "<p>" . wfMsg( "imglegend" ) . "\n" );

	$text = str_replace( "$1", "<strong>{$lt}</strong>", wfMsg( "imagelisttext" ) );
	$text = str_replace( "$2", "<strong>{$st}</strong>", $text );
	$wgOut->addHTML( "<p>{$text}\n<p>" );

	$sk = $wgUser->getSkin();
	$here = "Special:Imagelist";
	$link = $sk->makeKnownLink( $here, $byname, "sort=byname&amp;limit=0" );

	$text = str_replace( "$1", $link, wfMsg( "showall" ) );
	$wgOut->addHTML( "{$text}<br>\n" );

	$nums = array( 50, 100, 250, 500, 1000, 2500, 5000 );

	$fill = "";
	$first = true;
	foreach ( $nums as $num ) {
		if ( ! $first ) { $fill .= " | "; }
		$first = false;

		$fill .= $sk->makeKnownLink( $here, "{$num}",
		  "sort=bysize&amp;limit={$num}" );
	}
	$text = str_replace( "$1", $fill, wfMsg( "showlast" ) );
	$text = str_replace( "$2", $bysize, $text );
	$wgOut->addHTML( "{$text}<br>\n" );

	$fill = "";
	$first = true;
	foreach ( $nums as $num ) {
		if ( ! $first ) { $fill .= " | "; }
		$first = false;

		$fill .= $sk->makeKnownLink( $here, $num,
		  "sort=bydate&amp;limit={$num}" );
	}
	$text = str_replace( "$1", $fill, wfMsg( "showlast" ) );
	$text = str_replace( "$2", $bydate, $text );
	$wgOut->addHTML( "{$text}<br>\n<p>" );

	while ( $s = mysql_fetch_object( $res ) ) {
		$name = $s->img_name;
		$ut = $s->img_user_text;
		if ( 0 == $s->img_user ) { $ul = $ut; }
		else { $ul = $sk->makeLink( "User:{$ut}", $ut ); }

		$ilink = "<a href=\"" . wfImageUrl( $name ) .
		  "\">{$name}</a>";

		$l = "(del) (" .
		  $sk->makeKnownLink( "Image:{$name}", wfMsg( "imgdesc" ) ) .
		  ") {$ilink} . . {$s->img_size} bytes . . {$ul} . . " .
		  $wgLang->timeanddate( $s->img_timestamp );

		if ( "" != $s->img_description ) {
			$l .= " <em>({$s->img_description})</em>";
		}
		$wgOut->addHTML( "{$l}<br>\n" );
	}
	mysql_free_result( $res );
}

?>
