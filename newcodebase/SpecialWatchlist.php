<?

function wfSpecialWatchlist()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "watchlist" ) );
	$wgOut->addHTML( "<p>(TODO: Watch list)" );
}

?>
