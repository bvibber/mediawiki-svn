<?php
/**
 * Hooks for Usability Initiative EditWarning extension
 *
 * @file
 * @ingroup Extensions
 */

class EditWarningHooks {

	/* Static Functions */

	/**
	 * AjaxAddScript hook
	 * Initializes the component
	 */
	public static function initialize( $out ) {
		global $wgEditWarningStyleVersion, $wgRequest, $wgUser;
		
		$skin = $wgUser->getSkin();
		if ( $skin->skinname == 'vector' ) {
			UsabilityInitiativeHooks::initialize();
			// Adds script to document
			UsabilityInitiativeHooks::addScript(
				'EditWarning/EditWarning.js', $wgEditWarningStyleVersion
			);
			// Internationalization
			wfLoadExtensionMessages( 'EditWarning' );
			// Adds messages to page
			UsabilityInitiativeHooks::addMessages(
				array( 'editwarning-warning' )
			);
		}
		// Continue
		return true;
	}
}
