<?

function wfSpecialAllpages()
{
	global $wgUser, $wgOut;

	$conn = wfGetDB();
	$sql = "SELECT cur_namespace,cur_title FROM cur ORDER BY " .
	  "cur_namespace,cur_title";
	wfDebug( "All: $sql\n" );

	set_time_limit( 600 ); # 10 minutes ought to be plenty
	$res = mysql_query( $sql, $conn );
	if ( false === $res ) {
		$wgOut->databaseError( wfMsg( "getarticlelist" ) );
		return;
	}
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
