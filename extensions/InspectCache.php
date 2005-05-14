<?php

# This is a simple debugging tool to inspect the contents of the shared cache
# It is unrestricted and insecure, do not enable it on a public site.


# Not a valid entry point, skip unless MEDIAWIKI is defined
if (defined('MEDIAWIKI')) {
$wgExtensionFunctions[] = "wfInspectCache";

function wfInspectCache() {
global $IP;
require_once( "$IP/includes/SpecialPage.php" );

class InspectCache extends SpecialPage
{
	function InspectCache() {
		SpecialPage::SpecialPage("InspectCache");
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle, $wgMemc;

		$this->setHeaders();

		$key = $wgRequest->getVal( 'key' );
		$encQ = htmlspecialchars( $key );
		$action = $wgTitle->escapeLocalUrl();
		$ok = htmlspecialchars( wfMsg( "ok" ) );

		$wgOut->addHTML( <<<END
<form name="ucf" method="post" action="$action">
<input type="text" size="80" name="key" value="$encQ"/><br />
<input type="submit" name="submit" value="$ok" /><br /><br />
</form>
END
);

		if ( !is_null( $key ) ) {
			$value = var_export( $wgMemc->get( $key ), true );
			$wgOut->addHTML( "<pre>" . htmlspecialchars( $value ) . "</pre>" );
		}
	}
}

global $wgMessageCache;
SpecialPage::addPage( new InspectCache );
$wgMessageCache->addMessage( "inspectcache", "Inspect cache" );

} # End of extension function
} # End of invocation guard
?>
