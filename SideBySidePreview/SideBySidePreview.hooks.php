<?php
/**
 * Hooks for Usability Initiative SideBySidePreview extension
 *
 * @file
 * @ingroup Extensions
 */

class SideBySidePreviewHooks {

	/* Static Functions */

	/**
	 * EditPageBeforeEditToolbar hook
	 * Adds the preview to the edit form
	 */
	 public static function addPreview( &$toolbar ) {
		global $wgSideBySidePreviewStyleVersion, $wgUser;
		global $wgSideBySidePreviewGlobalEnable, $wgSideBySidePreviewUserEnable;
		
		if ( $wgSideBySidePreviewGlobalEnable || ( $wgSideBySidePreviewUserEnable && $wgUser->getOption( 'sidebysidepreview' ) ) ) {
			// Adds script to document
			UsabilityInitiativeHooks::initialize();
			UsabilityInitiativeHooks::addScript(
				'SideBySidePreview/SideBySidePreview.js', $wgSideBySidePreviewStyleVersion
			);
			// Internationalization
			wfLoadExtensionMessages( 'SideBySidePreview' );
			UsabilityInitiativeHooks::addMessages( array(
				'sidebysidepreview-tab-edit',
				'sidebysidepreview-tab-preview',
				'sidebysidepreview-loading',
			) );
		}
		return true;
	}
	
	/**
	 * GetPreferences hook
	 * Add preference for SideBySidePreview
	 */
	public static function addPreferences( $user, &$defaultPreferences ) {
		global $wgSideBySidePreviewGlobalEnable, $wgSideBySidePreviewUserEnable;

		if ( !$wgSideBySidePreviewGlobalEnable && $wgSideBySidePreviewUserEnable ) {
			wfLoadExtensionMessages( 'SideBySidePreview' );
			// Adds preference for opting in
			$defaultPreferences['sidebysidepreview'] =
			array(
				'type' => 'toggle',
				'label-message' => 'sidebysidepreview-preference',
				'section' => 'editing/experimental',
			);
		}
		return true;
	}
}
