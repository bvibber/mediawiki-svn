<?

function wfSpecialRecentchanges()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "recentchanges" ) );
	$wgOut->addWikiText( wfMsg( "recentchangestext" ) );

	$wgOut->addHTML( "<p>(TODO: Recent changes list)\n" );
}

?>
