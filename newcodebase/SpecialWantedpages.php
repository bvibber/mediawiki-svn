<?

function wfSpecialWantedpages()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "wantedpages" ) );
	$wgOut->addHTML( "<p>(TODO: Wanted pages)" );
}

?>
