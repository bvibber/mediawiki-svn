<?php

class Snapshot {
	function __construct() {
		$this->mRevs = array();
	}
	
	/**
	 * Get the revision number for a given page resource as embedded
	 * in the snapshot. Note that this particular revision may no
	 * longer exist, for instance if the referred page has been
	 * deleted.
	 *
	 * Will return 0 for resources not listed in the snapshot; this
	 * usually will allow for loading the current version.
	 *
	 * @param Title $title
	 * @return int
	 */
	public function getPageRevision( $title ) {
		$key = $title->getPrefixedDbKey();
		if( isset( $this->mRevs[$key] ) ) {
			return $this->mRevs[$key];
		}
		return 0;
	}
	
	/**
	 * Load a particular snapshot out of the database.
	 */
	public static function newFromId( $snapId ) {
		return Snapshot::fetchBy(
			array( 'snap_id' => $snapId ) );
	}
	
	/**
	 * Load the most recent snapshot with a given tag name on this page.
	 * @return Snapshot or null if no snapshot exists
	 */
	public static function newFromTag( $pageId, $tag ) {
		return Snapshot::fetchBy(
			array(
				'snap_page' => $pageId,
				'snap_tag' => $tag
			),
			array( 'ORDER BY' => 'snap_timestamp DESC' ) );
	}
	
	
	/**
	 * Load a Snapshot object from a particluar snapshot ID
	 */
	private static function fetchBy( $where, $options ) {
		$dbr = wfGetDB( DB_SLAVE );
		$revs = Snapshot::loadFromId( $dbr, $id );
		
		if( !$revs ) {
			$dbw = wfGetDB( DB_MASTER );
			$revs = Snapshot::loadFromId( $dbw, $id );
		}
		
		$this->mRevs = $revs;
	}
	
	private static function loadFromId( $db, $id ) {
		$result = $db->select( 'snapshot_revs',
			array( 'sr_namespace', 'sr_title', 'sr_rev' ),
			array( 'sr_snap' => $id ),
			__METHOD__ );
		
		$revs = array();
		while( $row = $db->fetchObject( $result ) ) {
			$title = Title::makeTitle( $row->sr_namespace, $row->sr_title );
			$key = $title->getPrefixedDbKey();
			$revs[$key] = $row->sr_rev;
		}
		
		$db->freeResult( $result );
		return $revs;
	}
}

?>