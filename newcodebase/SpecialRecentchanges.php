<?

function wfSpecialRecentchanges()
{
	global $wgUser, $wgOut;

	$wgOut->addWikiText( wfMsg( "recentchangestext" ) );

	$wgOut->addHTML( "<p>(TODO: Recent changes list)\n" );
}

?>
