<?php
/**
 * Hooks for Usability Initiative WikiEditorCode extension
 *
 * @file
 * @ingroup Extensions
 */

class WikiEditorCodeHooks {

	/* Static Functions */

	/**
	 * EditPage::showEditForm:initial hook
	 * Adds the TOC to the edit form
	 */
	 public static function addCode( &$toolbar ) {
		global $wgWikiEditorCodeStyleVersion, $wgUser;
		global $wgWikiEditorCodeGlobalEnable, $wgWikiEditorCodeUserEnable;
		
		if ( $wgWikiEditorCodeGlobalEnable || ( $wgWikiEditorCodeUserEnable && $wgUser->getOption( 'usewikieditorcode' ) ) ) {		
			// Adds script to document
			UsabilityInitiativeHooks::initialize();
			UsabilityInitiativeHooks::addScript(
				'WikiEditorCode/WikiEditorCode.js', $wgWikiEditorCodeStyleVersion
			);
		}
		return true;
	}
	
	/**
	 * GetPreferences hook
	 * Add WikiEditorCode-related items to the preferences
	 */
	public static function addPreferences( $user, &$defaultPreferences ) {
		global $wgWikiEditorCodeGlobalEnable, $wgWikiEditorCodeUserEnable;

		if ( !$wgWikiEditorCodeGlobalEnable && $wgWikiEditorCodeUserEnable ) {
			wfLoadExtensionMessages( 'WikiEditorCode' );
			// Adds preference for opting in
			$defaultPreferences['usewikieditorcode'] =
			array(
				'type' => 'toggle',
				'label-message' => 'wikieditorcode-preference',
				'section' => 'editing/experimental',
			);
		}
		return true;
	}
}
