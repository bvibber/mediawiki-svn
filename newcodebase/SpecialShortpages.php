<?

function wfSpecialShortpages()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "shortpages" ) );
	$wgOut->addHTML( "<p>(TODO: Short pages)" );
}

?>
