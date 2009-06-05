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
		global $wgUser, $wgOut, $wgJsMimeType;
		
		// Internationalization
		wfLoadExtensionMessages( 'EditToolbar' );
		// Checks if the user has not opted to use this toolbar
		if ( !$wgUser->getOption( 'usebetatoolbar' ) ) {
			// Exists the function without doing anything
			return true;
		}
		// Adds toolbar container
		$toolbar = '<div id="edittoolbar"></div>';
		// List of messages to be sent to the client for use in the toolbar
		$messages = array(
			/* Sections */
			'edittoolbar-section-format',
			'edittoolbar-section-insert',
			'edittoolbar-section-characters',
			'edittoolbar-section-help',
			/* Main Section */
			'edittoolbar-format-bold',
			'edittoolbar-format-bold-example',
			'edittoolbar-format-italic',
			'edittoolbar-format-italic-example',
			'edittoolbar-insert-ilink',
			'edittoolbar-insert-ilink-example',
			'edittoolbar-insert-xlink',
			'edittoolbar-insert-xlink-example',
			'edittoolbar-insert-file',
			'edittoolbar-insert-file-pre',
			'edittoolbar-insert-file-example',
			'edittoolbar-insert-reference',
			'edittoolbar-insert-reference-example',
			'edittoolbar-insert-signature',
			/* Formatting Section */
			'edittoolbar-format-ulist',
			'edittoolbar-format-ulist-example',
			'edittoolbar-format-olist',
			'edittoolbar-format-olist-example',
			'edittoolbar-format-heading',
			'edittoolbar-format-heading-1',
			'edittoolbar-format-heading-2',
			'edittoolbar-format-heading-3',
			'edittoolbar-format-heading-4',
			'edittoolbar-format-heading-5',
			'edittoolbar-format-heading-example',
			'edittoolbar-format-superscript',
			'edittoolbar-format-superscript-example',
			'edittoolbar-format-subscript',
			'edittoolbar-format-subscript-example',
			'edittoolbar-format-big',
			'edittoolbar-format-big-example',
			'edittoolbar-format-small',
			'edittoolbar-format-small-example',
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
		// Appends javascript message setting code
		$toolbar .= Xml::element(
			'script', array( 'type' => $wgJsMimeType ), $messagesJs
		);
		// Continue
		return true;
	}
	
	/**
	 * GetPreferences hook
	 * Add toolbar related items to the preferences
	 */
	public static function addPreferences( $user, $defaultPreferences ) {
		wfLoadExtensionMessages( 'EditToolbar' );
		$defaultPreferences['usebetatoolbar'] =
		array(
			'type' => 'toggle',
			'label-message' => 'edittoolbar-preference',
			'section' => 'editing/advancedediting',
		);
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
