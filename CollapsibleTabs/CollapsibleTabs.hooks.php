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
		global $wgCollapsibleTabsStyleVersion;
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addScript(
			'CollapsibleTabs/CollapsibleTabs.js', $wgCollapsibleTabsStyleVersion
		);
		return true;
	}
	
}
