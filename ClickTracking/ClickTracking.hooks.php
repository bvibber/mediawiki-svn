<?php

/**
 * Hooks for Usability Initiative ClickTracking extension
 *
 * @file
 * @ingroup Extensions
 */

class ClickTrackingHooks {

	/* Static Functions */

	/* initializations */

	/* 3 tables for click tracking */
	public static function schema() {
		global $wgExtNewTables;

		$wgExtNewTables[] = array(
			'click_tracking',
			dirname( __FILE__ ) . '/ClickTracking.sql'
		);

		$wgExtNewTables[] = array(
			'user_daily_contribs',
			dirname( __FILE__ ) . '/UserDailyContribs.sql'
		);

		$wgExtNewTables[] = array(
			'click_tracking_events',
			dirname( __FILE__ ) . '/ClickTrackingEvents.sql'
		);

		return true;
	}

	/**
	 * Adds JavaScript
	 */
	public static function addJS(){
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addScript( 'ClickTracking/ClickTracking.js' );
		UsabilityInitiativeHooks::addVariables(
			array( 'wgTrackingToken' => ClickTrackingHooks::get_session_id() )
		);
		return true;
	}

	/**
	 * Gets the session ID...we just want a unique random ID for the page load
	 * @return session ID
	 */
	public static function get_session_id(){
		global $wgUser;
		return wfGenerateToken( array( $wgUser->getName(), time() ) );
	}

	/**
	 * Get the number of revisions a user has made since a given time
	 * @param $ts beginning timestamp
	 * @return number of revsions this user has made
	 */
	public static function getEditCountSince( $ts ){
		global $wgUser;

		// convert to just the day
		$time = gmdate( 'Ymd', wfTimestamp( TS_UNIX, $ts ) );

		$dbr = wfGetDB( DB_SLAVE );

		$edits = $dbr->selectField(
			'user_daily_contribs',
			'SUM(contribs)',
			array(
				'user_id' => $wgUser->getId(),
				"day < '$time'"
			),
			__METHOD__
		);

		// user hasn't made any edits in whatever amount of time
		if( $edits == null ){
			$edits = 0;
		}

		return $edits;
	}

	/**
	 * Stores a new contribution
	 * @return true
	 */
	public static function storeNewContrib(){
		global $wgUser;
		$today = gmdate( 'Ymd', time() );
		$dbw = wfGetDB( DB_MASTER );
		$sql = 
		"INSERT INTO user_daily_contribs (user_id,day,contribs) VALUES ({$wgUser->getId()},$today,1) ON DUPLICATE KEY UPDATE contribs=contribs+1;";
		$dbw->query($sql, __METHOD__);
		return true;
	}

	/**
	 * Get event ID from name
	 * @param $event_name String: name of the event to get
	 * @return integer
	 */
	public static function getEventIDFromName( $event_name ){
		$dbr = wfGetDB( DB_SLAVE );

		$id_num = $dbr->selectField(
			'click_tracking_events',
			'id',
			array( 
				'event_name' => $event_name
			), 
			__METHOD__
		);

		// if this entry doesn't exist...
		// this will be incredibly rare as the whole database will only have a few hundred entries in it at most
		// and getting DB_MASTER up top would be wasteful
		if( $id_num === false ){
			$dbw = wfGetDB( DB_MASTER );
			$dbw->insert(
				'click_tracking_events',
				array( 'event_name' => (string) $event_name ),
				__METHOD__
			);

			// should be inserted now...if not, we return zero later
			$id_num = $dbr->selectField(
				'click_tracking_events',
				'id',
				array(
					'event_name' => $event_name
				), 
				__METHOD__
			);
		}

		if( $id_num === false ){
			return 0;
		}

		return $id_num;
	}

	/**
	 * Track particular event
	 * @param $session_id String: unique session id for this editing sesion
	 * @param $is_logged_in Boolean: whether or not the user is logged in
	 * @param $namespace Integer: namespace the user is editing
	 * @param $event_id Integer: event type
	 * @param $contribs Integer: contributions the user has made (or NULL if user not logged in)
	 * @param $contribs_in_timespan Integer: number of contributions user has made in a given timespan
	 * @return true if the event was stored in the DB
	 */
	public static function trackEvent( $session_id, $is_logged_in, $namespace, $event_id, $contribs = 0, $contribs_in_timespan = 0 ){
		$dbw = wfGetDB( DB_MASTER );

		$dbw->begin();

		// Builds insert information
		$data = array(
			'session_id' => (string) $session_id,
			'is_logged_in' => (bool) $is_logged_in,
			'user_total_contribs' => ( $is_logged_in ? (int) $contribs : null ),
			'user_contribs_span' => ( $is_logged_in ? (int) $contribs_in_timespan : null ),
			'namespace' => (int) $namespace,
			'event_id' => (int) $event_id
		);

		$db_status = $dbw->insert( 'click_tracking', $data, __METHOD__ );
		$dbw->commit();
		return $db_status;
	}
}
