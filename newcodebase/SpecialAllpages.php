<?

function wfSpecialAllpages()
{
	global $wgUser, $wgOut;

	set_time_limit( 600 ); # 10 minutes ought to be plenty

	$conn = wfGetDB();
	$sql = "SELECT cur_namespace,cur_title FROM cur ORDER BY " .
	  "cur_namespace,cur_title";
	$res = wfQuery( $sql, $conn, "wfSpecialAllpages" );

	$wgOut->addHTML( wfMsg( "allpagestext" ) . "\n<p>" );

	$sk = $wgUser->getSkin();
	while ( $s = mysql_fetch_object( $res ) ) {
		$l = $sk->makeKnownLink( Title::makeName( $s->cur_namespace,
		  $s->cur_title ), "" );
		$wgOut->addHTML( "{$l}<br>\n" );
	}
	mysql_free_result( $res );
}

?>
