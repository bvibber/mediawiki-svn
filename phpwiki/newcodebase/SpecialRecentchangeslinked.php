<?

function wfSpecialRecentchangeslinked()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "recentchangeslinked" ) );
	$wgOut->addHTML( "<p>(TODO: Recent changes linked)" );
}

?>
