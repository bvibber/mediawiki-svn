<?

function wfSpecialBlockip()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "blockip" ) );
	$wgOut->addHTML( "<p>(TODO: Block IP)" );
}

?>
