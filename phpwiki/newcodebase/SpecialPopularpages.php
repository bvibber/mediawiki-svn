<?

function wfSpecialPopularpages()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "popularpages" ) );
	$wgOut->addHTML( "<p>(TODO: Popular pages)" );
}

?>
