<?

function wfSpecialUserlogout()
{
	global $wgUser, $wgOut;

	$wgUser->logout();
	$wgOut->setPageTitle( wfMsg( "userlogout" ) );
	$wgOut->addHTML( wfMsg( "logouttext" ) );
}

?>
