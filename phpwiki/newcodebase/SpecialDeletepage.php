<?

function wfSpecialDeletepage()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "deletepage" ) );
	$wgOut->addHTML( "(TODO: Delete page)" );
}

?>
