<?

function wfSpecialListusers()
{
	global $wgUser, $wgOut;

	set_time_limit( 120 ); # 2 minutes ought to be plenty

	$sql = "SELECT user_name,user_rights FROM user ORDER BY " .
	  "user_name";
	$res = wfQuery( $sql, "wfSpecialListusers" );

	$wgOut->addHTML( wfMsg( "userlisttext" ) . "\n<p>" );

	$sk = $wgUser->getSkin();
	while ( $s = wfFetchObject( $res ) ) {
		$n = $s->user_name;
		$r = $s->user_rights;

		$l = $sk->makeLink( "User:{$n}", $n );

		if ( "" != $r ) {
			$link = $sk->makeKnownLink(
			  "Wikipedia:Administrators", $r );
			$l .= " ({$link})";
		}
		$l .= "<br>\n";
		$wgOut->addHTML( $l );
	}
	wfFreeResult( $res );
}

?>
