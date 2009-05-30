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
		// Add javascript resources to document
		$out->addScript(
			Xml::element(
				'script',
				array(
					'type' => $wgJsMimeType,
					'src' => $wgScriptPath .
						'/extensions/UsabilityInitiative/Resources/jquery.js'
				),
				'',
				false
			)
		);
		// Continue
		return true;
	}
}
