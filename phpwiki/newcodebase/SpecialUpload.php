<?

function wfSpecialUpload()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "upload" ) );
	$wgOut->addHTML( "<p>(TODO: Upload)" );
}

?>
