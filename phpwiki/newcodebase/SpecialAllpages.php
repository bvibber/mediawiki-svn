<?

function wfSpecialAllpages()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "allpages" ) );
	$wgOut->addHTML( "<p>(TODO: All pages)" );
}

?>
