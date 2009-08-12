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
		global $wgExtNewTables;
		
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
	public static function trackEvent($is_logged_in, $namespace, $event_id, $contribs=0){
		
		$dbw = wfGetDB( DB_MASTER );
			
		$dbw->begin();
		// Builds insert information
		
		$data = array(
			'is_logged_in' => (bool) $is_logged_in,
			'namespace' => (int) $namespace,
			'event_id' => (int) $event_id,
			'user_contribs' => ($is_logged_in?$contribs:null)
		);
		
		$db_status = $dbw->insert('click_tracking', $data, __METHOD__);
		$dbw->commit();
		return $db_status;
	}
		
}