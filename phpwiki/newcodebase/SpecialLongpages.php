<?

function wfSpecialLongpages()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "longpages" ) );
	$wgOut->addHTML( "<p>(TODO: Long pages)" );
}

?>
