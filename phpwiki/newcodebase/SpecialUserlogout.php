<?

function wfSpecialUserlogout()
{
	global $wgUser, $wgOut, $returnto;

	$wgUser->logout();
	$wgOut->setPageTitle( wfMsg( "userlogout" ) );
	$wgOut->setRobotpolicy( "noindex,nofollow" );
	$wgOut->addHTML( wfMsg( "logouttext" ) . "\n<p>" );

	if ( "" == $returnto ) {
		$r = wfMsg( "returntomain" );
	} else {
		$r = str_replace( "$1", $returnto, wfMsg( "returnto" ) );
		$wgOut->addMeta( "http:Refresh", "5;url=" . wfLocalLink( $returnto ) );
	}
	$wgOut->addWikiText( $r );
}

?>
