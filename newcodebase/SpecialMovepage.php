<?

function wfSpecialMovepage()
{
	global $wgUser, $wgOut;

	if ( ! $wgUser->isSysop() ) {
		$wgOut->sysopRequired();
		return;
	}
	$wgOut->addHTML( "<p>(TODO: Move page)" );
}

?>
