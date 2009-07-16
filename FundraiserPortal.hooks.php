<?php
/**
 * Hooks for FundraiserPortal extension
 *
 * @file
 * @ingroup Extensions
 */

class FundraiserPortalHooks {
	
	/* Static Members */
	
	// Only one of these templates will be allowed
	static $mTemplates = array(
		'Plain', 'Ruby', 'RubyText', 'Sapphire'
	);
	
	/* Static Functions */
	
	/**
	 * SkinBuildSidebar hook
	 * Adds please donate button to sidebar
	 */
	public static function buildSidebar( $skin, &$bar ) {
		global $wgScriptPath, $wgFundraiserPortalURL, $wgFundraiserPortalShow;
		global $wgFundraiserPortalTemplate;
		
		// Only proceed if we are configured to show the portal
		if ( !$wgFundraiserPortalShow ) {
			return true;
		}
		// Only proceed if the template we are being asked to use is allowed
		if ( !in_array( $wgFundraiserPortalTemplate, self::$mTemplates ) ) {
			return true;
		}
		// Render the portal and insert it at the begining of the sidebar
		wfLoadExtensionMessages( 'FundraiserPortal' );
		$template = dirname( __FILE__ ) . '/Templates/' .
			$wgFundraiserPortalTemplate .
			'.php';
		$imageUrl = $wgScriptPath . '/extensions/FundraiserPortal/images';
		if ( file_exists( $template ) ) {
			ob_start();
			require_once( $template );
			$bar = array_merge( array( 'DONATE' => ob_get_clean() ), $bar );
		}
		
		return true;
	}
}
