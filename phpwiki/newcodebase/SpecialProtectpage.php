<?

function wfSpecialProtectpage()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "protectpage" ) );
	$wgOut->addHTML( "<p>(TODO: Protect page)" );
}

?>
