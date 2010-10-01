<?php
/**
 * Hooks for ClickTracking extension
 *
 * @file
 * @ingroup Extensions
 */

class ClickTrackingHooks {

	/* Static Methods */

	/**
	 * LoadExtensionSchemaUpdates hook
	 * @return Boolean: always true
	 */
	public static function loadExtensionSchemaUpdates() {
		global $wgExtNewTables, $wgExtNewIndexes, $wgExtNewFields;

		$dir = dirname( __FILE__ ) . '/';
		$wgExtNewTables[] = array( 'click_tracking', $dir . 'patches/ClickTracking.sql' );
		$wgExtNewTables[] = array( 'click_tracking_events', $dir . 'patches/ClickTrackingEvents.sql' );
		$wgExtNewIndexes[] = array(
			'click_tracking',
			'click_tracking_action_time',
			$dir . 'patches/patch-action_time.sql',
		);
		$wgExtNewFields[] = array(
			'click_tracking',
			'additional_info',
			$dir . 'patches/patch-additional_info.sql',
		);
		return true;
	}

	/**
	 * ParserTestTables hook
	 *
	 * @param $tables Array
	 * @return Boolean: always true
	 */
	public static function parserTestTables( &$tables ) {
		$tables[] = 'click_tracking';
		$tables[] = 'click_tracking_events';
		return true;
	}

	/**
	 * BeforePageDisplay hook
	 * Adds the modules to the page
	 *
	 * @param $out OutputPage output page
	 * @param $skin Skin current skin
	 * @return Boolean: always true
	 */
	public static function beforePageDisplay( $out, $skin ) {
		global $wgClickTrackThrottle;

		if ( $wgClickTrackThrottle >= 0 && rand() % $wgClickTrackThrottle == 0 ) {
			$out->addModules( 'ext.clickTracking' );
		}
		return true;
	}

	/**
	 * MakeGlobalVariablesScript hook
	 * Generates the random wgTrackingToken JS global variable
	 *
	 * @param $vars Array: existing JS globals
	 * @return Boolean: always true
	 */
	public static function makeGlobalVariablesScript( &$vars ) {
		global $wgUser;
		$vars['wgTrackingToken'] = wfGenerateToken( array( $wgUser->getName(), time() ) );
		return true;
	}

	/**
	 * ResourceLoaderRegisterModules hook
	 * Adds modules to ResourceLoader
	 *
	 * @param $resourceLoader Object: ResourceLoader object
	 * @return Boolean: always true
	 */
	public static function resourceLoaderRegisterModules( &$resourceLoader ) {
		$resourceLoader->register( array(
			'jquery.clickTracking' => new ResourceLoaderFileModule( array(
				'scripts' => 'extensions/ClickTracking/modules/jquery.clickTracking.js',
				'dependencies' => 'jquery.cookie',
			) ),
			'ext.clickTracking' => new ResourceLoaderFileModule( array(
				'scripts' => 'extensions/ClickTracking/modules/ext.clickTracking.js',
				'dependencies' => 'jquery.clickTracking',
			) ),
			'ext.clickTracking.special' => new ResourceLoaderFileModule( array(
				'scripts' => 'extensions/ClickTracking/modules/ext.clickTracking.special.js',
				'styles' => 'extensions/ClickTracking/modules/ext.clickTracking.special.css',
				'dependencies' => array( 'jquery.ui.datepicker', 'jquery.ui.dialog' ),
			) ),
		) );
		return true;
	}

	/**
	 * Get the number of revisions a user has made since a given time
	 *
	 * @param $ts beginning timestamp
	 * @return number of revsions this user has made
	 */
	public static function getEditCountSince( $ts ) {
		global $wgUser;

		// Convert to just the day
		$time = gmdate( 'Y-m-d', wfTimestamp( TS_UNIX, $ts ) );
		$dbr = wfGetDB( DB_SLAVE );
		$edits = $dbr->selectField(
			'user_daily_contribs',
			'SUM(contribs)',
			array( 'user_id' => $wgUser->getId(), 'day >= ' . $dbr->addQuotes( $time ) ),
			__METHOD__
		);
		// User hasn't made any edits in whatever amount of time
		return $edits == null ? 0 : $edits;
	}

	/**
	 * Get event ID from name
	 *
	 * @param $event_name String: name of the event to get
	 * @return integer
	 */
	public static function getEventIDFromName( $event_name ) {
		// Replication lag means sometimes a new event will not exist in the table yet
		$dbw = wfGetDB( DB_MASTER );
		$id_num = $dbw->selectField(
			'click_tracking_events',
			'id',
			array( 'event_name' => $event_name ),
			__METHOD__
		);
		// If this entry doesn't exist, which will be incredibly rare as the whole database will only have a few hundred
		// entries in it at most and getting DB_MASTER up top would be wasteful
		// FIXME: Use replace() instead of this selectField --> insert or update logic
		if ( $id_num === false ) {
			$dbw->insert(
				'click_tracking_events',
				array( 'event_name' => (string) $event_name ),
				__METHOD__
			);
			$id_num = $dbw->insertId();
		}
		return $id_num === false ? 0 : $id_num;
	}

	/**
	 * Track particular event
	 *
	 * @param $sessionId String: unique session id for this editing sesion
	 * @param $isLoggedIn Boolean: whether or not the user is logged in
	 * @param $namespace Integer: namespace the user is editing
	 * @param $eventId Integer: event type
	 * @param $contribs Integer: contributions the user has made (or NULL if user not logged in)
	 * @param $contribs_in_timespan1 Integer: number of contributions user has made in timespan of granularity 1
	 * (defined by ClickTracking/$wgClickTrackContribGranularity1)
	 * @param $contribs_in_timespan2 Integer: number of contributions user has made in timespan of granularity 2
	 * (defined by ClickTracking/$wgClickTrackContribGranularity2)
	 * @param $contribs_in_timespan3 Integer: number of contributions user has made in timespan of granularity 3
	 * (defined by ClickTracking/$wgClickTrackContribGranularity3)
	 * @return Boolean: true if the event was stored in the DB
	 */
	public static function trackEvent( $sessionId, $isLoggedIn, $namespace, $eventId, $contribs = 0,
	$contribs_in_timespan1 = 0, $contribs_in_timespan2 = 0, $contribs_in_timespan3 = 0, $additional = null ) {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();
		// Builds insert information
		$data = array(
			'action_time' => $dbw->timestamp(),
			'session_id' => (string) $sessionId,
			'is_logged_in' => (bool) $isLoggedIn,
			'user_total_contribs' => ( $isLoggedIn ? (int) $contribs : null ),
			'user_contribs_span1' => ( $isLoggedIn ? (int) $contribs_in_timespan1 : null ),
			'user_contribs_span2' => ( $isLoggedIn ? (int) $contribs_in_timespan2 : null ),
			'user_contribs_span3' => ( $isLoggedIn ? (int) $contribs_in_timespan3 : null ),
			'namespace' => (int) $namespace,
			'event_id' => (int) $eventId,
			'additional_info' => ( isset( $additional ) ? (string) $additional : null )
		);
		$db_status = $dbw->insert( 'click_tracking', $data, __METHOD__ );
		$dbw->commit();
		return $db_status;
	}
}
