<?php
/**
 * Hooks for Usability Initiative Collapsing Tabs extension
 *
 * @file
 * @ingroup Extensions
 */

class CollapsibleTabsHooks {

	/* Static Functions */
	
	/**
	 * intialize hook
	 */
	public static function initialize( $out ) {
		global $wgCollapsibleTabsStyleVersion, $wgUser;
		// HACK: Don't include this JS on non-Vector skins, won't work anyway
		if ( !$wgUser->getSkin() instanceof SkinVector ) {
			return true;
		}
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addScript(
			'CollapsibleTabs/CollapsibleTabs.js', $wgCollapsibleTabsStyleVersion
		);
		return true;
	}
	
}
