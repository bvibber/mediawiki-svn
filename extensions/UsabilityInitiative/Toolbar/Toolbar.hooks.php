<?php
/**
 * Hooks for Usability Initiative Toolbar extension
 *
 * @file
 * @ingroup Extensions
 */

class ToolbarHooks {

	/* Static Functions */

	/**
	 * EditPageBeforeEditToolbar hook
	 * Intercept the display of the toolbar, replacing the content of $toolbar
	 */
	public static function interceptToolbar(
		&$toolbar
	) {
		global $wgUser;
		
		if ( $wgUser->getOption('usebetatoolbar') ) {
			$toolbar = '<div id="editing-toolbar"></div>';
		}
		// Continue
		return true;
	}
	
	/**
	 * GetPreferences hook
	 * Add toolbar related items to the preferences
	 */
	public static function addPreferences(
		$user,
		$defaultPreferences
	) {
		wfLoadExtensionMessages( 'Toolbar' );
		$defaultPreferences['usebetatoolbar'] =
		array(
			'type' => 'toggle',
			'label-message' => 'toolbar-preference',
			'section' => 'editing/advancedediting',
		);
		return true;
	}
	
	
	/**
	 * AjaxAddScript hook
	 * Add ajax support script
	 */
	public static function addJS(
		$out
	) {
		global $wgScriptPath, $wgJsMimeType, $wgToolbarStyleVersion;
		// Add javascript version variable
		$out->addInlineScript(
			"var wgToolbarStyleVersion = \"$wgToolbarStyleVersion\";\n"
		);
		// Add javascript resources to document
		$out->addScript(
			Xml::element(
				'script',
				array(
					'type' => $wgJsMimeType,
					'src' => $wgScriptPath .
						'/extensions/UsabilityInitiative/Toolbar/Toolbar.js?' .
						$wgToolbarStyleVersion
				),
				'',
				false
			)
		);
		// Continue
		return true;
	}

	/**
	 * BeforePageDisplay hook
	 * Add css style sheet
	 */
	public static function addCSS(
		$out
	) {
		global $wgScriptPath, $wgToolbarStyleVersion;
		// Add css for various styles
		$out->addLink(
			array(
				'rel' => 'stylesheet',
				'type' => 'text/css',
				'href' => $wgScriptPath .
					'/extensions/UsabilityInitiative/Toolbar/Toolbar.css?' .
					$wgToolbarStyleVersion,
			)
		);
		// Continue
		return true;
	}

}
