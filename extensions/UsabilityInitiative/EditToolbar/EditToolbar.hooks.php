<?php
/**
 * Hooks for Usability Initiative Toolbar extension
 *
 * @file
 * @ingroup Extensions
 */

class EditToolbarHooks {

	/* Static Functions */
	
	/**
	 * EditPageBeforeEditToolbar hook
	 * Intercept the display of the toolbar, replacing the content of $toolbar
	 */
	public static function intercept( &$toolbar ) {
		global $wgUser, $wgEditToolbarGlobalEnable, $wgEditToolbarUserEnable;
		
		// Checks if...
		if (
			// The following is NOT true
			!(
				// Toolbar is globablly enabled
				$wgEditToolbarGlobalEnable ||
				// Or...
				(
					// Toolbar is per-user enablable
					$wgEditToolbarUserEnable &&
					// And this user has enabled it
					$wgUser->getOption( 'usebetatoolbar' ) 
				)
			)
		) {
			// Returns without using the toolbar
			return true;
		}
		// Replaces stock toolbar with new toolbar container
		$toolbar = '<div id="edittoolbar"></div>';
		// Internationalization
		wfLoadExtensionMessages( 'EditToolbar' );
		// Adds messages to page
		UsabilityInitiativeHooks::addMessages(
			array(
				/* Main Section */
				'edittoolbar-tool-format-bold',
				'edittoolbar-tool-format-bold-example',
				'edittoolbar-tool-format-italic',
				'edittoolbar-tool-format-italic-example',
				'edittoolbar-tool-insert-ilink',
				'edittoolbar-tool-insert-ilink-example',
				'edittoolbar-tool-insert-xlink',
				'edittoolbar-tool-insert-xlink-example',
				'edittoolbar-tool-insert-file',
				'edittoolbar-tool-insert-file-pre',
				'edittoolbar-tool-insert-file-example',
				'edittoolbar-tool-insert-reference',
				'edittoolbar-tool-insert-reference-example',
				'edittoolbar-tool-insert-signature',
				/* Formatting Section */
				'edittoolbar-section-format',
				'edittoolbar-tool-format-heading',
				'edittoolbar-tool-format-heading-1',
				'edittoolbar-tool-format-heading-2',
				'edittoolbar-tool-format-heading-3',
				'edittoolbar-tool-format-heading-4',
				'edittoolbar-tool-format-heading-5',
				'edittoolbar-tool-format-heading-example',
				'edittoolbar-group-format-list',
				'edittoolbar-tool-format-ulist',
				'edittoolbar-tool-format-ulist-example',
				'edittoolbar-tool-format-olist',
				'edittoolbar-tool-format-olist-example',
				'edittoolbar-group-format-size',
				'edittoolbar-tool-format-big',
				'edittoolbar-tool-format-big-example',
				'edittoolbar-tool-format-small',
				'edittoolbar-tool-format-small-example',
				'edittoolbar-group-format-baseline',
				'edittoolbar-tool-format-superscript',
				'edittoolbar-tool-format-superscript-example',
				'edittoolbar-tool-format-subscript',
				'edittoolbar-tool-format-subscript-example',
				/* Insert Section */
				'edittoolbar-section-insert',
				'edittoolbar-group-insert-media',
				'edittoolbar-tool-insert-gallery',
				'edittoolbar-tool-insert-gallery-example',
				'edittoolbar-tool-insert-newline',
				/* Special Characters Section */
				'edittoolbar-section-characters',
				/* Help Section */
				'edittoolbar-section-help',
				'edittoolbar-help-heading-description',
				'edittoolbar-help-heading-syntax',
				'edittoolbar-help-heading-result',
				'edittoolbar-help-page-format',
				'edittoolbar-help-page-link',
				'edittoolbar-help-page-heading',
				'edittoolbar-help-page-list',
				'edittoolbar-help-page-file',
				'edittoolbar-help-page-reference',
				'edittoolbar-help-page-discussion',
				'edittoolbar-help-content-bold-description',
				'edittoolbar-help-content-bold-syntax',
				'edittoolbar-help-content-bold-result',
				'edittoolbar-help-content-italic-description',
				'edittoolbar-help-content-italic-syntax',
				'edittoolbar-help-content-italic-result',
				'edittoolbar-help-content-bolditalic-description',
				'edittoolbar-help-content-bolditalic-syntax',
				'edittoolbar-help-content-bolditalic-result',
			)
		);
		// Continue
		return true;
	}
	
	/**
	 * GetPreferences hook
	 * Add toolbar related items to the preferences
	 */
	public static function addPreferences( $user, $defaultPreferences ) {
		global $wgEditToolbarGlobalEnable, $wgEditToolbarUserEnable;
		
		// Checks if...
		if (
			// Toolbar is NOT globablly enabled
			!$wgEditToolbarGlobalEnable &&
			// And Toolbar is per-user enablable
			$wgEditToolbarUserEnable
		) {
			// Internationalization
			wfLoadExtensionMessages( 'EditToolbar' );
			// Adds preference for opting in
			$defaultPreferences['usebetatoolbar'] =
			array(
				'type' => 'toggle',
				'label-message' => 'edittoolbar-preference',
				'section' => 'editing/advancedediting',
			);
		}
		return true;
	}
	
	/**
	 * AjaxAddScript hook
	 * Initializes the component
	 */
	public static function initialize() {
		global $wgEditToolbarStyleVersion;
		
		// Add script to document
		UsabilityInitiativeHooks::addScript(
			'EditToolbar/EditToolbar.js', $wgEditToolbarStyleVersion
		);
		// Add style to document
		UsabilityInitiativeHooks::addStyle(
			'EditToolbar/EditToolbar.css', $wgEditToolbarStyleVersion
		);
		// Continue
		return true;
	}
}
