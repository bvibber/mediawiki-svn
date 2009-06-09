<?php
/**
 * Hooks for Usability Initiative extension
 *
 * @file
 * @ingroup Extensions
 */

class UsabilityInitiativeHooks {

	/* Static Functions */

	/**
	 * AjaxAddScript hook
	 * Add ajax support script
	 */
	public static function addJS( $out ) {
		global $wgScriptPath, $wgJsMimeType;
		$scripts = array(
			'/extensions/UsabilityInitiative/Resources/jquery.js',
			'/extensions/UsabilityInitiative/Resources/jquery.textSelection.js',
			'/extensions/UsabilityInitiative/Resources/jquery.cookie.js',
			'/extensions/UsabilityInitiative/Resources/messages.js',
		);
		foreach ( $scripts as $script ) {
			// Add javascript resources to document
			$out->addScript(
				Xml::element(
					'script',
					array(
						'type' => $wgJsMimeType, 
						'src' => $wgScriptPath . $script
					),
					'',
					false
				)
			);
		}
		// Continue
		return true;
	}
}
