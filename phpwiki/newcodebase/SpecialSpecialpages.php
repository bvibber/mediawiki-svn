<?

function wfSpecialSpecialpages()
{
	global $wgUser, $wgOut, $wgLang;

	$sk = $wgUser->getSkin();
	$validSP = $wgLang->getValidSpecialPages();
	$wgOut->addHTML( "<h2>" . wfMsg( "spheading" ) . "</h2>\n<ul>" );

	foreach ( $validSP as $name => $desc ) {
		if ( "" == $desc ) { continue; }
		$link = $sk->makeKnownLink( "Special:{$name}", $desc );
		$wgOut->addHTML( "<li>{$link}</li>\n" );
	}
	$wgOut->addHTML( "</ul>\n" );

	if ( ! $wgUser->isSysop() ) { return; }

	$sysopSP = $wgLang->getSysopSpecialPages();
	$wgOut->addHTML( "<h2>" . wfMsg( "sysopspheading" ) . "</h2>\n<ul>" );

	foreach ( $sysopSP as $name => $desc ) {
		if ( "" == $desc ) { continue; }
		$link = $sk->makeKnownLink( "Special:{$name}", $desc );
		$wgOut->addHTML( "<li>{$link}</li>\n" );
	}
	$wgOut->addHTML( "</ul>\n" );
}

?>
