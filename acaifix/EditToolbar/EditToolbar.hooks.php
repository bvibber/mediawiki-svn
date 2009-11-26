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
		global $wgUser, $wgEditToolbarGlobalEnable;
		global $wgEditToolbarUserEnable, $wgEditToolbarCGDGlobalEnable;
		global $wgEditToolbarCGDUserEnable;
		
		// Only proceed if some specific conditions are met
		if ( $wgEditToolbarGlobalEnable || ( $wgEditToolbarUserEnable && $wgUser->getOption( 'usebetatoolbar' ) ) ) {
			// Add JS and CSS
			global $wgEditToolbarStyleVersion;
			UsabilityInitiativeHooks::initialize();
			UsabilityInitiativeHooks::addScript(
				'EditToolbar/EditToolbar.js', $wgEditToolbarStyleVersion
			);
			UsabilityInitiativeHooks::addVariables( array(
				'wgEditToolbarCGD' => $wgEditToolbarCGDGlobalEnable || ( $wgEditToolbarCGDUserEnable && $wgUser->getOption( 'usebetatoolbar-cgd' ) )
			) );
			// Internationalization
			wfLoadExtensionMessages( 'EditToolbar' );
			UsabilityInitiativeHooks::addMessages(
				array(
					'wikieditor-toolbar-loading',
					/* Main Section */
					'wikieditor-toolbar-tool-bold',
					'wikieditor-toolbar-tool-bold-example',
					'wikieditor-toolbar-tool-italic',
					'wikieditor-toolbar-tool-italic-example',
					'wikieditor-toolbar-tool-ilink',
					'wikieditor-toolbar-tool-ilink-example',
					'wikieditor-toolbar-tool-xlink',
					'wikieditor-toolbar-tool-xlink-example',
					'wikieditor-toolbar-tool-link',
					'wikieditor-toolbar-tool-link-title',
					'wikieditor-toolbar-tool-link-int',
					'wikieditor-toolbar-tool-link-int-target',
					'wikieditor-toolbar-tool-link-int-text',
					'wikieditor-toolbar-tool-link-ext',
					'wikieditor-toolbar-tool-link-ext-target',
					'wikieditor-toolbar-tool-link-ext-text',
					'wikieditor-toolbar-tool-link-insert',
					'wikieditor-toolbar-tool-link-cancel',
					'wikieditor-toolbar-tool-link-int-target-status-exists',
					'wikieditor-toolbar-tool-link-int-target-status-notexists',
					'wikieditor-toolbar-tool-link-int-target-status-invalid',
					'wikieditor-toolbar-tool-link-int-target-status-loading',
					'wikieditor-toolbar-tool-link-int-invalid',
					'wikieditor-toolbar-tool-link-ext-invalid',
					'wikieditor-toolbar-tool-link-empty',
					'wikieditor-toolbar-tool-file',
					'wikieditor-toolbar-tool-file-pre',
					'wikieditor-toolbar-tool-file-example',
					'wikieditor-toolbar-tool-reference',
					'wikieditor-toolbar-tool-reference-example',
					'wikieditor-toolbar-tool-signature',
					/* Formatting Section */
					'wikieditor-toolbar-section-advanced',
					'wikieditor-toolbar-tool-heading',
					'wikieditor-toolbar-tool-heading-1',
					'wikieditor-toolbar-tool-heading-2',
					'wikieditor-toolbar-tool-heading-3',
					'wikieditor-toolbar-tool-heading-4',
					'wikieditor-toolbar-tool-heading-5',
					'wikieditor-toolbar-tool-heading-example',
					'wikieditor-toolbar-group-list',
					'wikieditor-toolbar-tool-ulist',
					'wikieditor-toolbar-tool-ulist-example',
					'wikieditor-toolbar-tool-olist',
					'wikieditor-toolbar-tool-olist-example',
					'wikieditor-toolbar-group-size',
					'wikieditor-toolbar-tool-big',
					'wikieditor-toolbar-tool-big-example',
					'wikieditor-toolbar-tool-small',
					'wikieditor-toolbar-tool-small-example',
					'wikieditor-toolbar-group-baseline',
					'wikieditor-toolbar-tool-superscript',
					'wikieditor-toolbar-tool-superscript-example',
					'wikieditor-toolbar-tool-subscript',
					'wikieditor-toolbar-tool-subscript-example',
					'wikieditor-toolbar-group-insert',
					'wikieditor-toolbar-tool-gallery',
					'wikieditor-toolbar-tool-gallery-example',
					'wikieditor-toolbar-tool-indent',
					'wikieditor-toolbar-tool-indent-example',
					'wikieditor-toolbar-tool-newline',
					'wikieditor-toolbar-tool-table',
					'wikieditor-toolbar-tool-table-example-old',
					'wikieditor-toolbar-tool-table-example',
					'wikieditor-toolbar-tool-table-example-header',
					'wikieditor-toolbar-tool-table-title',
					'wikieditor-toolbar-tool-table-dimensions',
					'wikieditor-toolbar-tool-table-dimensions-rows',
					'wikieditor-toolbar-tool-table-dimensions-columns',
					'wikieditor-toolbar-tool-table-dimensions-header',
					'wikieditor-toolbar-tool-table-insert',
					'wikieditor-toolbar-tool-table-cancel',
					'wikieditor-toolbar-tool-table-toomany',
					'wikieditor-toolbar-tool-table-invalidnumber',
					'wikieditor-toolbar-tool-table-zero',
					'wikieditor-toolbar-tool-replace',
					'wikieditor-toolbar-tool-replace-title',
					'wikieditor-toolbar-tool-replace-search',
					'wikieditor-toolbar-tool-replace-replace',
					'wikieditor-toolbar-tool-replace-case',
					'wikieditor-toolbar-tool-replace-regex',
					'wikieditor-toolbar-tool-replace-button-findnext',
					'wikieditor-toolbar-tool-replace-button-replacenext',
					'wikieditor-toolbar-tool-replace-button-replaceall',
					'wikieditor-toolbar-tool-replace-close',
					'wikieditor-toolbar-tool-replace-nomatch',
					'wikieditor-toolbar-tool-replace-success',
					'wikieditor-toolbar-tool-replace-emptysearch',
					'wikieditor-toolbar-tool-replace-invalidregex',
					/* Special Characters Section */
					'wikieditor-toolbar-section-characters',
					'wikieditor-toolbar-characters-page-latin',
					'wikieditor-toolbar-characters-page-latinextended',
					'wikieditor-toolbar-characters-page-ipa',
					'wikieditor-toolbar-characters-page-symbols',
					'wikieditor-toolbar-characters-page-greek',
					'wikieditor-toolbar-characters-page-cyrillic',
					'wikieditor-toolbar-characters-page-arabic',
					'wikieditor-toolbar-characters-page-hebrew',
					'wikieditor-toolbar-characters-page-telugu',
					/* Help Section */
					'wikieditor-toolbar-section-help',
					'wikieditor-toolbar-help-heading-description',
					'wikieditor-toolbar-help-heading-syntax',
					'wikieditor-toolbar-help-heading-result',
					'wikieditor-toolbar-help-page-format',
					'wikieditor-toolbar-help-page-link',
					'wikieditor-toolbar-help-page-heading',
					'wikieditor-toolbar-help-page-list',
					'wikieditor-toolbar-help-page-file',
					'wikieditor-toolbar-help-page-reference',
					'wikieditor-toolbar-help-page-discussion',
					'wikieditor-toolbar-help-content-bold-description',
					'wikieditor-toolbar-help-content-bold-syntax',
					'wikieditor-toolbar-help-content-bold-result',
					'wikieditor-toolbar-help-content-italic-description',
					'wikieditor-toolbar-help-content-italic-syntax',
					'wikieditor-toolbar-help-content-italic-result',
					'wikieditor-toolbar-help-content-bolditalic-description',
					'wikieditor-toolbar-help-content-bolditalic-syntax',
					'wikieditor-toolbar-help-content-bolditalic-result',
					'wikieditor-toolbar-help-content-ilink-description',
					'wikieditor-toolbar-help-content-ilink-syntax',
					'wikieditor-toolbar-help-content-ilink-result',
					'wikieditor-toolbar-help-content-xlink-description',
					'wikieditor-toolbar-help-content-xlink-syntax',
					'wikieditor-toolbar-help-content-xlink-result',
					'wikieditor-toolbar-help-content-heading1-description',
					'wikieditor-toolbar-help-content-heading1-syntax',
					'wikieditor-toolbar-help-content-heading1-result',
					'wikieditor-toolbar-help-content-heading2-description',
					'wikieditor-toolbar-help-content-heading2-syntax',
					'wikieditor-toolbar-help-content-heading2-result',
					'wikieditor-toolbar-help-content-heading3-description',
					'wikieditor-toolbar-help-content-heading3-syntax',
					'wikieditor-toolbar-help-content-heading3-result',
					'wikieditor-toolbar-help-content-heading4-description',
					'wikieditor-toolbar-help-content-heading4-syntax',
					'wikieditor-toolbar-help-content-heading4-result',
					'wikieditor-toolbar-help-content-heading5-description',
					'wikieditor-toolbar-help-content-heading5-syntax',
					'wikieditor-toolbar-help-content-heading5-result',
					'wikieditor-toolbar-help-content-ulist-description',
					'wikieditor-toolbar-help-content-ulist-syntax',
					'wikieditor-toolbar-help-content-ulist-result',
					'wikieditor-toolbar-help-content-olist-description',
					'wikieditor-toolbar-help-content-olist-syntax',
					'wikieditor-toolbar-help-content-olist-result',
					'wikieditor-toolbar-help-content-file-description',
					'wikieditor-toolbar-help-content-file-syntax',
					'wikieditor-toolbar-help-content-file-result',
					'wikieditor-toolbar-help-content-reference-description',
					'wikieditor-toolbar-help-content-reference-syntax',
					'wikieditor-toolbar-help-content-reference-result',
					'wikieditor-toolbar-help-content-rereference-description',
					'wikieditor-toolbar-help-content-rereference-syntax',
					'wikieditor-toolbar-help-content-rereference-result',
					'wikieditor-toolbar-help-content-showreferences-description',
					'wikieditor-toolbar-help-content-showreferences-syntax',
					'wikieditor-toolbar-help-content-showreferences-result',
					'wikieditor-toolbar-help-content-signaturetimestamp-description',
					'wikieditor-toolbar-help-content-signaturetimestamp-syntax',
					'wikieditor-toolbar-help-content-signaturetimestamp-result',
					'wikieditor-toolbar-help-content-signature-description',
					'wikieditor-toolbar-help-content-signature-syntax',
					'wikieditor-toolbar-help-content-signature-result',
					'wikieditor-toolbar-help-content-indent-description',
					'wikieditor-toolbar-help-content-indent-syntax',
					'wikieditor-toolbar-help-content-indent-result',
				)
			);
		}
		return true;
	}

	/**
	 * GetPreferences hook
	 * Add toolbar related items to the preferences
	 */
	public static function addPreferences( $user, &$defaultPreferences ) {
		global $wgEditToolbarGlobalEnable, $wgEditToolbarUserEnable;
		global $wgEditToolbarCGDGlobalEnable, $wgEditToolbarCGDUserEnable;

		if ( !$wgEditToolbarGlobalEnable && $wgEditToolbarUserEnable ) {
			wfLoadExtensionMessages( 'EditToolbar' );
			// Adds preference for opting in
			$defaultPreferences['usebetatoolbar'] =
			array(
				'type' => 'toggle',
				'label-message' => 'wikieditor-toolbar-preference',
				'section' => 'editing/experimental',
			);
		}
		
		if ( !$wgEditToolbarCGDGlobalEnable && $wgEditToolbarCGDUserEnable ) {
			wfLoadExtensionMessages( 'EditToolbar' );
			$defaultPreferences['usebetatoolbar-cgd'] = array(
				'type' => 'toggle',
				'label-message' => 'wikieditor-toolbar-cgd-preference',
				'section' => 'editing/experimental',
			);
		}
		return true;
	}
}
