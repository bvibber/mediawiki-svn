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
	 * addCollapsibleTabs hook
	 */
	public static function addCollapsibleTabs( $out ) {
		global $wgCollapsibleTabsStyleVersion, $wgScriptPath, $wgJsMimeType;
		
		$out->addScript( 
			Xml::element(
				'script',
				array(
					'type' => $wgJsMimeType,
					'src' => $wgScriptPath .
						"/extensions/UsabilityInitiative/" .
						'CollapsibleTabs/CollapsibleTabs.js?'.
							$wgCollapsibleTabsStyleVersion,
				),
				'',
				false
			)
		);
		
		return true;
	}
	
}
