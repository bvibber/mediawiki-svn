<?

function wfSpecialImagelist()
{
	global $wgUser, $wgOut, $wgLang, $sort;

	$conn = wfGetDB();
	$sql = "SELECT img_size,img_name,img_user,img_user_text," .
	  "img_description,img_timestamp FROM image ORDER BY ";

	if ( "bysize" == $sort ) { $sql .= "img_size DESC"; }
	else if ( "bydate" == $sort ) { $sql .= "img_timestamp DESC"; }
	else { $sql .= "img_name"; }

	$res = wfQuery( $sql, $conn, "wfSpecialImagelist" );
	$wgOut->addHTML( wfMsg( "imagelisttext" ) . "\n" );

	$sortby = wfMsg( "sortby" );
	$byname = wfMsg( "byname" );
	$bydate = wfMsg( "bydate" );
	$bysize = wfMsg( "bysize" );
	$here = "Special:Imagelist";

	$sk = $wgUser->getSkin();
	$wgOut->addHTML( "<p>{$sortby} " );
	$wgOut->addHTML(
	  $sk->makeKnownLink( $here, $byname, "sort=byname" ) . ", " );
	$wgOut->addHTML(
	  $sk->makeKnownLink( $here, $bysize, "sort=bysize" ) . ", " );
	$wgOut->addHTML(
	  $sk->makeKnownLink( $here, $bydate, "sort=bydate" ) . "\n<p>" );

	while ( $s = mysql_fetch_object( $res ) ) {
		$name = $s->img_name;
		$ut = $s->img_user_text;
		if ( 0 == $s->img_user ) { $ul = $ut; }
		else { $ul = $this->makeLink( "User:{$ut}", $ut ); }

		$l = "(delete) (history) " .
		  $sk->makeLink( "Image:{$name}", $name ) .
		  " . . {$s->img_size} bytes . . {$ul} . . " .
		  $wgLang->timeanddate( $s->img_timestamp );

		if ( "" != $s->img_description ) {
			$l .= " <em>({$s->img_description})</em>";
		}
		$wgOut->addHTML( "{$l}<br>\n" );
	}
	mysql_free_result( $res );
}

?>
