<?

function wfSpecialWhatlinkshere()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "whatlinkshere" ) );
	$wgOut->addHTML( "<p>(TODO: What links here)" );
}

?>
