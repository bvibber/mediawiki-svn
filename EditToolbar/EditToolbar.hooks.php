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
		global $wgUser, $wgOut, $wgJsMimeType, $wgRequest;
		global $wgEditToolbarGlobalEnable, $wgEditToolbarUserEnable;
		
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
		// Internationalization
		wfLoadExtensionMessages( 'EditToolbar' );
		// Adds toolbar container
		$toolbar = '<div id="edittoolbar"></div>';
		// List of messages to be sent to the client for use in the toolbar
		$messages = array(
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
			/* Special Characters Section */
			'edittoolbar-section-characters',
			/* Help Section */
			'edittoolbar-section-help',
		);
		// Transforms messages into javascript object members
		foreach ( $messages as $i => $message ) {
			$escapedMessageValue = Xml::escapeJsString( wfMsg( $message ) );
			$escapedMessageKey = Xml::escapeJsString( $message );
			$messages[$i] = "'{$escapedMessageKey}':'{$escapedMessageValue}'";
		}
		// Converts array of object members to a comma delimited list
		$messagesList = implode( ',', $messages );
		// Encapsulates list in javascript code to set them durring load
		$messagesJs = "loadGM({{$messagesList}});";
		
		// Ensure persistency of tabs' show/hide status between submits
		$persistentTabs = array( 'format' );
		$tabsJs = "";
		foreach( $persistentTabs as $tab )
			if( $wgRequest->wasPosted() && $wgRequest->getInt( "ET$tab" ) == 1 )
				$tabsJs .= "editToolbarConfiguration['$tab'].showInitially = '1';";
		
		// Appends javascript message setting code
		$toolbar .= Xml::element(
			'script', array( 'type' => $wgJsMimeType ), $messagesJs . $tabsJs
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
	 * Add ajax support script
	 */
	public static function addJS( $out ) {
		global $wgScriptPath, $wgJsMimeType, $wgEditToolbarStyleVersion;
		// Add javascript version variable
		$out->addInlineScript(
			"var wgEditToolbarStyleVersion = \"$wgEditToolbarStyleVersion\";\n"
		);
		// Add javascript resources to document
		$out->addScript(
			Xml::element(
				'script',
				array(
					'type' => $wgJsMimeType,
					'src' => $wgScriptPath .
						'/extensions/UsabilityInitiative/EditToolbar/EditToolbar.js?' .
						$wgEditToolbarStyleVersion
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
	public static function addCSS( $out ) {
		global $wgScriptPath, $wgEditToolbarStyleVersion;
		// Add css for various styles
		$out->addLink(
			array(
				'rel' => 'stylesheet',
				'type' => 'text/css',
				'href' => $wgScriptPath .
					'/extensions/UsabilityInitiative/EditToolbar/EditToolbar.css?' .
					$wgEditToolbarStyleVersion,
			)
		);
		// Continue
		return true;
	}
}
