<?

function wfSpecialAsksql()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "asksql" ) );
	$wgOut->addHTML( "<p>(TODO: Ask SQL)" );
}

?>
