<?

function wfSpecialListusers()
{
	global $wgUser, $wgOut;

	$conn = wfGetDB();
	$sql = "SELECT user_name,user_rights FROM user ORDER BY " .
	  "user_name";
	wfDebug( "Ulist: $sql\n" );

	set_time_limit( 120 ); # 2 minutes ought to be plenty
	$res = mysql_query( $sql, $conn );
	if ( false === $res ) {
		$wgOut->databaseError( wfMsg( "getuserlist" ) );
		return;
	}
	$wgOut->addHTML( wfMsg( "userlisttext" ) . "\n<p>" );

	$sk = $wgUser->getSkin();
	while ( $s = mysql_fetch_object( $res ) ) {
		$n = $s->user_name;
		$r = $s->user_rights;

		$l = $sk->makeInternalLink( "User:{$n}", $n );

		if ( "" != $r ) { $l .= " ({$r})"; }
		$l .= "<br>\n";
		$wgOut->addHTML( $l );
	}
	mysql_free_result( $res );
}

?>
