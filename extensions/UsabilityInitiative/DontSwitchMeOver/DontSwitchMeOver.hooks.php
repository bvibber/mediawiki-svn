<?php

/**
 * Hooks for DontSwitchMeOver extension
 *
 * @file
 * @ingroup Extensions
 */

class DontSwitchMeOverHooks {
	public static function addPreferences( $user, &$defaultPreferences ) {
		$defaultPreferences['dontswitchmeover'] = array(
			'type' => 'toggle',
			'label-message' => 'dontswitchmeover-pref',
			'section' => 'rendering/skin', // May move after discussion
		);
		return true;
	}
}
