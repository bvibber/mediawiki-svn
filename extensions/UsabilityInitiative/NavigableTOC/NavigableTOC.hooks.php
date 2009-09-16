<?php
/**
 * Hooks for Usability Initiative NavigableTOC extension
 *
 * @file
 * @ingroup Extensions
 */

class NavigableTOCHooks {

	/* Static Functions */

	/**
	 * EditPage::showEditForm:initial hook
	 * Adds the TOC to the edit form
	 */
	 public static function addTOC( &$toolbar ) {
		global $wgNavigableTOCStyleVersion, $wgUser;
		global $wgNavigableTOCGlobalEnable, $wgNavigableTOCUserEnable;
		
		if ( $wgNavigableTOCGlobalEnable || ( $wgNavigableTOCUserEnable && $wgUser->getOption( 'usenavigabletoc' ) ) ) {		
			// Adds script to document
			UsabilityInitiativeHooks::initialize();
			UsabilityInitiativeHooks::addScript(
				'NavigableTOC/NavigableTOC.js', $wgNavigableTOCStyleVersion
			);
		}
		return true;
	}
	
	/**
	 * GetPreferences hook
	 * Add NTOC-related items to the preferences
	 */
	public static function addPreferences( $user, &$defaultPreferences ) {
		global $wgNavigableTOCGlobalEnable, $wgNavigableTOCUserEnable;

		if ( !$wgNavigableTOCGlobalEnable && $wgNavigableTOCUserEnable ) {
			wfLoadExtensionMessages( 'NavigableTOC' );
			// Adds preference for opting in
			$defaultPreferences['usenavigabletoc'] =
			array(
				'type' => 'toggle',
				'label-message' => 'navigabletoc-preference',
				'section' => 'editing/advancedediting',
			);
		}
		return true;
	}
}
