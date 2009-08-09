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
	public static function addToolbar( &$toolbar ) {
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
		// Blanks out the stock toolbar, since the new one will be created
		// by javascript, and we don't want to waste the client's time building
		// and downloading the icons for the old toolbar.
		$toolbar = '';
		
		// Add JS and CSS
		global $wgEditToolbarStyleVersion;
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addScript(
			'EditToolbar/EditToolbar.js', $wgEditToolbarStyleVersion
		);
		// Internationalization
		wfLoadExtensionMessages( 'EditToolbar' );
		// Adds messages to page
		UsabilityInitiativeHooks::addMessages(
			array(
				'edittoolbar-loading',
				/* Main Section */
				'edittoolbar-tool-bold',
				'edittoolbar-tool-bold-example',
				'edittoolbar-tool-italic',
				'edittoolbar-tool-italic-example',
				'edittoolbar-tool-ilink',
				'edittoolbar-tool-ilink-example',
				'edittoolbar-tool-xlink',
				'edittoolbar-tool-xlink-example',
				'edittoolbar-tool-file',
				'edittoolbar-tool-file-pre',
				'edittoolbar-tool-file-example',
				'edittoolbar-tool-reference',
				'edittoolbar-tool-reference-example',
				'edittoolbar-tool-signature',
				/* Formatting Section */
				'edittoolbar-section-advanced',
				'edittoolbar-tool-heading',
				'edittoolbar-tool-heading-1',
				'edittoolbar-tool-heading-2',
				'edittoolbar-tool-heading-3',
				'edittoolbar-tool-heading-4',
				'edittoolbar-tool-heading-5',
				'edittoolbar-tool-heading-example',
				'edittoolbar-group-list',
				'edittoolbar-tool-ulist',
				'edittoolbar-tool-ulist-example',
				'edittoolbar-tool-olist',
				'edittoolbar-tool-olist-example',
				'edittoolbar-group-size',
				'edittoolbar-tool-big',
				'edittoolbar-tool-big-example',
				'edittoolbar-tool-small',
				'edittoolbar-tool-small-example',
				'edittoolbar-group-baseline',
				'edittoolbar-tool-superscript',
				'edittoolbar-tool-superscript-example',
				'edittoolbar-tool-subscript',
				'edittoolbar-tool-subscript-example',
				'edittoolbar-group-insert',
				'edittoolbar-tool-gallery',
				'edittoolbar-tool-gallery-example',
				'edittoolbar-tool-newline',
				'edittoolbar-tool-table',
				'edittoolbar-tool-table-example',
				/* Special Characters Section */
				'edittoolbar-section-characters',
				'edittoolbar-characters-page-latin',
				'edittoolbar-characters-page-latinextended',
				'edittoolbar-characters-page-ipa',
				'edittoolbar-characters-page-symbols',
				'edittoolbar-characters-page-greek',
				'edittoolbar-characters-page-cyrillic',
				'edittoolbar-characters-page-arabic',
				'edittoolbar-characters-page-hebrew',
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
				'edittoolbar-help-content-ilink-description',
				'edittoolbar-help-content-ilink-syntax',
				'edittoolbar-help-content-ilink-result',
				'edittoolbar-help-content-xlink-description',
				'edittoolbar-help-content-xlink-syntax',
				'edittoolbar-help-content-xlink-result',
				'edittoolbar-help-content-heading1-description',
				'edittoolbar-help-content-heading1-syntax',
				'edittoolbar-help-content-heading1-result',
				'edittoolbar-help-content-heading2-description',
				'edittoolbar-help-content-heading2-syntax',
				'edittoolbar-help-content-heading2-result',
				'edittoolbar-help-content-heading3-description',
				'edittoolbar-help-content-heading3-syntax',
				'edittoolbar-help-content-heading3-result',
				'edittoolbar-help-content-heading4-description',
				'edittoolbar-help-content-heading4-syntax',
				'edittoolbar-help-content-heading4-result',
				'edittoolbar-help-content-heading5-description',
				'edittoolbar-help-content-heading5-syntax',
				'edittoolbar-help-content-heading5-result',
				'edittoolbar-help-content-ulist-description',
				'edittoolbar-help-content-ulist-syntax',
				'edittoolbar-help-content-ulist-result',
				'edittoolbar-help-content-olist-description',
				'edittoolbar-help-content-olist-syntax',
				'edittoolbar-help-content-olist-result',
				'edittoolbar-help-content-file-description',
				'edittoolbar-help-content-file-syntax',
				'edittoolbar-help-content-file-result',
				'edittoolbar-help-content-reference-description',
				'edittoolbar-help-content-reference-syntax',
				'edittoolbar-help-content-reference-result',
				'edittoolbar-help-content-rereference-description',
				'edittoolbar-help-content-rereference-syntax',
				'edittoolbar-help-content-rereference-result',
				'edittoolbar-help-content-showreferences-description',
				'edittoolbar-help-content-showreferences-syntax',
				'edittoolbar-help-content-showreferences-result',
				'edittoolbar-help-content-signaturetimestamp-description',
				'edittoolbar-help-content-signaturetimestamp-syntax',
				'edittoolbar-help-content-signaturetimestamp-result',
				'edittoolbar-help-content-signature-description',
				'edittoolbar-help-content-signature-syntax',
				'edittoolbar-help-content-signature-result',
				'edittoolbar-help-content-indent-description',
				'edittoolbar-help-content-indent-syntax',
				'edittoolbar-help-content-indent-result',
			)
		);
		// Continue
		return true;
	}

	/**
	 * GetPreferences hook
	 * Add toolbar related items to the preferences
	 */
	public static function addPreferences( $user, &$defaultPreferences ) {
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
}
