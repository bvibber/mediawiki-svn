<?php
/**
 * Hooks for Usability Initiative PrefStats extension
 *
 * @file
 * @ingroup Extensions
 */

class PrefStatsHooks {

	/* Static Functions */
	public static function schema() {
		global $wgExtNewTables;
		$wgExtNewTables[] = array( 'prefstats',
			dirname( __FILE__ ) . '/PrefStats.sql' );
	}

	public static function save( $user, &$options ) {
		global $wgPrefStatsEnable, $wgPrefStatsTrackPrefs;
		if ( !$wgPrefStatsEnable )
			return;

		$dbw = wfGetDb( DB_MASTER );
		foreach ( $wgPrefStatsTrackPrefs as $pref => $value ) {
			if ( isset( $options[$pref] ) && $options[$pref] == $value )
				// FIXME: if the user disables and re-enables,
				// we're not tracking that
				$dbw->insert( 'prefstats', array(
						'ps_user' => $user->getId(),
						'ps_pref' => $pref,
						'ps_value' => $value,
						'ps_start' => $dbw->timestamp( wfTimestamp() ),
						'ps_end' => null,
						'ps_duration' => - 1 // hack
					), __METHOD__, array( 'IGNORE' ) );
			else {
				$start = $dbw->selectField( 'prefstats',
					'ps_start', array(
						'ps_user' => $user->getId(),
						'ps_pref' => $pref
					), __METHOD__ );
				if ( $start ) {
					$duration = wfTimestamp( TS_UNIX ) -
						wfTimestamp( TS_UNIX, $start );
					$dbw->update( 'prefstats', array(
						'ps_end' => $dbw->timestamp( wfTimestamp() ),
						'ps_duration' => $dbw->timestamp( $duration )
						), __METHOD__ );
				}
			}
		}
	}

}
