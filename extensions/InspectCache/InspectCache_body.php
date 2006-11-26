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
		global $wgRequest, $wgOut, $wgTitle;

		$this->setHeaders();

		$key = $wgRequest->getVal( 'key' );
		$delete = $wgRequest->getBool( 'delete' ) && $wgRequest->wasPosted();
		$group = $wgRequest->getVal( 'group' );
		
		$encQ = htmlspecialchars( $key );
		$action = $wgTitle->escapeLocalUrl();
		$ok = htmlspecialchars( wfMsg( "ok" ) );
		
		$groups = array(
			'main' => array( 'General cache', 'wfGetMainCache' ),
			'parser' => array( 'Parser cache', 'wfGetParserCacheStorage' ),
			'message' => array( 'Message cache', 'wfGetMessageCacheStorage' ),
		);
		if( !isset( $groups[$group] ) ) {
			$group = 'main';
		}
		$cache = $groups[$group][1]();
		
		$radios = '';
		foreach( $groups as $type => $bits ) {
			list( $desc ) = $bits;
			$radios .=
				Xml::radioLabel( $desc, 'group', $type, "mw-cache-$type",
					$group == $type ) . " ";
		}
			

		$wgOut->addHTML( <<<END
<form name="ucf" method="post" action="$action">
<div>$radios</div>
<input type="text" size="80" name="key" value="$encQ"/><br />
<input type="submit" name="submit" value="Get" />
<input type="submit" name="delete" value="Delete" /><br /><br />
</form>
END
);

		if ( $delete && !is_null( $key ) ) {
			$cache->delete( $key );
			$wgOut->addHTML( "Deleted " . htmlspecialchars( $key ) . "\n" );
		} else if ( !is_null( $key ) ) {
			$value = $cache->get( $key );
			if ( !is_string( $value ) ) {
				$value = var_export( $value, true );
			}
			$wgOut->addHTML( "<pre>" . htmlspecialchars( $value ) . "</pre>" );
		}
	}
}

?>
