<?

function wfSpecialListusers()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "listusers" ) );
	$wgOut->addHTML( "<p>(TODO: List users)" );
}

?>
