<?

function wfSpecialLonelypages()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "lonelypages" ) );
	$wgOut->addHTML( "<p>(TODO: Orphaned pages)" );
}

?>
