<?

function wfSpecialVote()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "vote" ) );
	$wgOut->addHTML( "<p>(TODO: Vote)" );
}

?>
