<?

function wfSpecialUserlogout()
{
	global $wgUser, $wgOut, $returnto;

	$wgUser->logout();
	$wgOut->setPageTitle( wfMsg( "userlogout" ) );
	$wgOut->setRobotpolicy( "noindex,nofollow" );
	$wgOut->addHTML( wfMsg( "logouttext" ) . "\n<p>" );
	$wgOut->returnToMain();
}

?>
