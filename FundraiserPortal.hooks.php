<?php
/**
 * Hooks for FundraiserPortal extension
 *
 * @file
 * @ingroup Extensions
 */

class FundraiserPortalHooks {

	/* Static Functions */

	/**
	 * SkinBuildSidebar hook
	 * Adds please donate button to sidebar
	 */
	public static function buildSidebar( $skin, &$bar ) {
		global $wgFundraiserPortalURL, $wgFundraiserPortalShow;
		
		if ( !$wgFundraiserPortalShow ) {
			return true;
		}
		
		wfLoadExtensionMessages( 'FundraiserPortal' );
		
		$css = <<<CSS
/* Monobook Style */
body.skin-monobook div#p-DONATE h5 {
	display: none;
}
body.skin-monobook div#p-DONATE div.pBody a {
	display: block;
	margin: 0.5em;
	margin-bottom: 0.25em;
}
/* Vector Style */
body.skin-vector div#p-DONATE {
	padding-top: 1em;
}
body.skin-vector div#p-DONATE h5 {
	display: none;
}
body.skin-vector div#p-DONATE div.body {
	background: none;
	padding: 0;
	margin: 0;
	margin-left: 0.5em;
}
body.skin-vector div#p-DONATE div a {
	display: block;
	margin: 0.5em;
	margin-bottom: 0;
}
CSS;
		$portal = Xml::element(
			'style',
			array( 'type' => 'text/css' ),
			$css
		);
		$portal .= Xml::element(
			'a',
			array( 'href' => $wgFundraiserPortalURL ),
			wfMsg( 'fundraiserportal-donate' )
		);
		$bar = array_merge( array( 'DONATE' => $portal ), $bar );
		
		return true;
	}
}
