<?php

/**
 * Hooks for Usability Initiative ClickTracking extension
 *
 * @file
 * @ingroup Extensions
 */

class ClickTrackingHooks {

	/* Static Functions */
	public static function schema() {
		global $wgExtNewTables, $wgExtNewFields;
		
		$wgExtNewTables[] = array(
			'click_tracking',
			dirname( __FILE__ ) . '/ClickTracking.sql'
		);
		
		return true;
	}

	/**
	 * Track particular event
	 * @param $is_logged_in whether or not the user is logged in
	 * @param $namespace namespace the user is editing
	 * @param $event_id event type
	 * @param $contribs contributions the user has made (or NULL if user not logged in)
	 * @return true if the event was stored in the DB
	 */
	public static function trackEvent($is_logged_in, $namespace, $event_id, $contribs=-1){
		
		$dbw = wfGetDB( DB_MASTER );
		if ($contribs < 0) { //meaning the user is not logged in
			$contribs = "NULL"; 
		}
		
		$dbw->begin();
		// Builds insert information
		$data = array(
			'is_logged_in' => $is_logged_in,
			'namespace' => (int) $namespace,
			'event_id' => (int) $event_id,
			'user_contribs' => $contribs
		);
		
		$db_status = $dbw->insert('click_tracking', $data, __METHOD__);
		$dbw->commit();
		return $db_status;
	}
	
	
}