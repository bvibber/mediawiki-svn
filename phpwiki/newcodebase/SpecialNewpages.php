<?

function wfSpecialNewpages()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "newpages" ) );
	$wgOut->addHTML( "<p>(TODO: New pages)" );
}

?>
