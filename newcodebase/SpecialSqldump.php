<?

function wfSpecialSqldump()
{
	global $wgUser, $wgOut;

	$wgOut->setPageTitle( wfMsg( "sqldup" ) );
	$wgOut->addHTML( "<p>(TODO: SQL dump)" );
}

?>
