<?

function wfSpecialContributions()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "contributions" ) );
	$wgOut->addHTML( "<p>(TODO: Contributions)" );
}

?>
