<?

function wfSpecialMovepage()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "movepage" ) );
	$wgOut->addHTML( "<p>(TODO: Move page)" );
}

?>
