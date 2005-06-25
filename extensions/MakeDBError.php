<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "Not a valid entry point\n" );
}

$wgExtensionFunctions[] = "wfMakeDBErrorExt";

function wfMakeDBErrorExt() {

require_once( "SpecialPage.php" );

class MakeDBErrorPage extends UnlistedSpecialPage
{
	function MakeDBErrorPage() {
		UnlistedSpecialPage::UnlistedSpecialPage("MakeDBError");
	}

	function execute( $par ) {
		global $wgOut, $wgLoadBalancer;
		$this->setHeaders();
		if ( $par == 'connection' ) {
			$wgLoadBalancer->mServers[1234] = $wgLoadBalancer->mServers[0];
			$wgLoadBalancer->mServers[1234]['user'] = 'chicken';
			$wgLoadBalancer->mServers[1234]['password'] = 'cluck cluck';
			$db =& wfGetDB( 1234 );
			$wgOut->addHTML("<pre>" . var_export( $db, true ) . "</pre>" );
		} else {
			wfQuery( "test", DB_READ );
		}
	}
}

SpecialPage::addPage( new MakeDBErrorPage );



} # End of extension function

?>
