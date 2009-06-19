<?php
/**
 * Hooks for Usability Initiative OptIn extension
 *
 * @file
 * @ingroup Extensions
 */

class OptInHooks {

	/* Static Functions */
	public static function schema() {
		global $wgExtNewTables;
		$wgExtNewTables[] = array( 'optin_survey',
			dirname( __FILE__ ) . '/OptIn.sql' );
		return true;
	}
}
