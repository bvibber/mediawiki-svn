<?php

/**
 * Hooks for "Update My Prefs" extension
 *
 * @file
 * @ingroup Extensions
 */

class UpdateMyPrefsHooks {
	
	public static function addPreferences( $user, &$defaultPreferences ) {
		wfLoadExtensionMessages( 'UpdateMyPrefs' );
		$ui = array(
			'type' => 'toggle',
			'label-message' => 'updatemyprefs-label-message',
			'section' => 'personal/updates',
		);
		$defaultPreferences['updatemyprefs'] = $ui;
		return true;
	}
		
}