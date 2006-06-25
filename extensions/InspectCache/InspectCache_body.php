<?php

if (!defined('MEDIAWIKI')) {
	echo "InspectCache extension";
	exit(1);
}

global $wgMessageCache;
$wgMessageCache->addMessage( "inspectcache", "Inspect cache" );

class InspectCache extends SpecialPage
{
	function InspectCache() {
		SpecialPage::SpecialPage("InspectCache");
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle, $wgMemc;

		$this->setHeaders();

		$key = $wgRequest->getVal( 'key' );
		$delete = $wgRequest->getBool( 'delete' ) && $wgRequest->wasPosted();
		$encQ = htmlspecialchars( $key );
		$action = $wgTitle->escapeLocalUrl();
		$ok = htmlspecialchars( wfMsg( "ok" ) );

		$wgOut->addHTML( <<<END
<form name="ucf" method="post" action="$action">
<input type="text" size="80" name="key" value="$encQ"/><br />
<input type="submit" name="submit" value="Get" />
<input type="submit" name="delete" value="Delete" /><br /><br />
</form>
END
);

		if ( $delete && !is_null( $key ) ) {
			$wgMemc->delete( $key );
			$wgOut->addHTML( "Deleted $key\n" );
		} else if ( !is_null( $key ) ) {
			$value = $wgMemc->get( $key );
			if ( !is_string( $value ) ) {
				$value = var_export( $value, true );
			}
			$wgOut->addHTML( "<pre>" . htmlspecialchars( $value ) . "</pre>" );
		}
	}
}

?>
