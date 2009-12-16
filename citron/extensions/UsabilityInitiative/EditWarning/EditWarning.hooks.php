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
		if ( $skin->getSkinName() == 'vector' && $wgUser->getOption( 'useeditwarning' ) ) {
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
	
	/**
	 * GetPreferences hook
	 * Add EditWarning-related items to the preferences
	 */
	public static function addPreferences( $user, &$defaultPreferences ) {
		global $wgEditToolbarGlobalEnable, $wgEditToolbarUserEnable;

		wfLoadExtensionMessages( 'EditWarning' );
		// Adds preference for enabling/disabling EditWarning
		$defaultPreferences['useeditwarning'] =
		array(
			'type' => 'toggle',
			'label-message' => 'editwarning-preference',
			'section' => 'editing/advancedediting',
		);
		return true;
	}
}
