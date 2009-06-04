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
		
		// Replaces toolbar with new toolbar container
		if ( $wgUser->getOption( 'usebetatoolbar' ) ) {
			$toolbar = '<div id="edittoolbar"></div>';
		}
		// List of messages to be sent to the client for use in the toolbar
		// all of which will get a prefix of "toolbar"
		$messages = array(
			'format-bold',
			'format-bold-example',
			'format-italic',
			'format-italic-example',
			'insert-ilink',
			'insert-ilink-example',
			'insert-xlink',
			'insert-xlink-example',
			'insert-image',
			'insert-image-example',
			'insert-reference',
			'insert-reference-example',
		);
		// Transforms messages into javascript object members
		foreach ( $messages as $i => $message ) {
			$escapedMessageValue = Xml::escapeJsString(
				wfMsg( 'edittoolbar-' . $message )
			);
			$escapedMessageKey = Xml::escapeJsString( $message );
			$messages[$i] = "'{$escapedMessageKey}':'{$escapedMessageValue}'";
		}
		// Converts array of object members to a comma delimited list
		$messagesList = implode( ',', $messages );
		// Encapsulates list in javascript code to set them durring load
		$messagesJs = "editToolbar.setMessages({{$messagesList}});";
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
