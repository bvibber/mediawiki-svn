<?

function wfSpecialAsksql()
{
	global $wgUser, $wgOut;

	if ( ! $wgUser->isSysop() ) {
		$wgOut->sysopRequired();
		return;
	}
	$wgOut->addHTML( "<p>(TODO: Ask SQL)" );
}

?>
