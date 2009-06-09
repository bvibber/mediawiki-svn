<?php
/**
 * Hooks for Usability Initiative EditWarning extension
 *
 * @file
 * @ingroup Extensions
 */

class EditWarningHooks {
	/**
	 * AjaxAddScript hook
	 * Add ajax support script
	 */
	public static function addJS( $out ) {
		global $wgScriptPath, $wgJsMimeType, $wgEditWarningStyleVersion;
		// Add javascript resources to document
		$out->addScript(
			Xml::element(
				'script',
				array(
					'type' => $wgJsMimeType,
					'src' => $wgScriptPath .
						'/extensions/UsabilityInitiative/EditWarning/EditWarning.js?' .
						$wgEditWarningStyleVersion
				),
				'',
				false
			)
		);
		wfLoadExtensionMessages( 'EditWarning' );
		$key = Xml::escapeJsString( 'editwarning-warning' );
		$value = Xml::escapeJsString( wfMsg( 'editwarning-warning' ) );
		$messagesList = "'$key': '$value'";
		$out->addInlineScript("loadGM({{$messagesList}});");
		// Continue
		return true;
	}
}
