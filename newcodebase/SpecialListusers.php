<?

function wfSpecialListusers()
{
	global $wgUser, $wgOut;

	set_time_limit( 120 ); # 2 minutes ought to be plenty

	$conn = wfGetDB();
	$sql = "SELECT user_name,user_rights FROM user ORDER BY " .
	  "user_name";
	$res = wfQuery( $sql, $conn, "wfSpecialListusers" );

	$wgOut->addHTML( wfMsg( "userlisttext" ) . "\n<p>" );

	$sk = $wgUser->getSkin();
	while ( $s = mysql_fetch_object( $res ) ) {
		$n = $s->user_name;
		$r = $s->user_rights;

		$l = $sk->makeLink( "User:{$n}", $n );

		if ( "" != $r ) { $l .= " ({$r})"; }
		$l .= "<br>\n";
		$wgOut->addHTML( $l );
	}
	mysql_free_result( $res );
}

?>
