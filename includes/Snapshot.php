<?php

class Snapshot {
	/**
	 * Create an empty snapshot object
	 */
	function __construct() {
		$this->mRevs = array();
		$this->mRevisionId = 0;
	}
	
	/**
	 * Get the revision ID for the primary page resources, if stored.
	 */
	public function getSnapRevision() {
		return $this->mRevisionId;
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
	 * Store a page title <-> revision relationship in the snapshot
	 * object. If saved to the database, this can be used in future
	 * to pull the same versions of resources originally used.
	 *
	 * @param Title $title
	 * @param int $revision
	 */
	public function addPage( $title, $revision ) {
		$key = $title->getPrefixedDbKey();
		$this->mRevs[$key] = $revision;
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
			array(
				'ORDER BY' => 'snap_timestamp DESC',
				'LIMIT' => 1
			)
		);
	}
	
	/**
	 * Tag and save a page snapshot state to the database.
	 * Returns the snapshot ID number.
	 * @param int $pageId
	 * @param string $tag
	 * @param string $comment
	 * @return int
	 */
	public function insertTag( $pageId, $revId, $tag, $comment ) {
		global $wgUser;
		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();
		$dbw->insert( 'snapshot',
			array(
				'snap_page'      => $pageId,
				'snap_rev'       => $revId,
				'snap_tag'       => $tag,
				'snap_timestamp' => $dbw->timestamp(),
				'snap_user'      => $wgUser->getId(),
				'snap_comment'   => $comment,
			),
			__METHOD__ );
		$snapId = $dbw->insertId();

		if( $this->mRevs ) {
			$data = array();
			foreach( $this->mRevs as $page => $rev ) {
				// @fixme: hack
				$title = Title::newFromText( $page );
				$data[] = array(
					'sr_snap'      => $snapId,
					'sr_namespace' => $title->getNamespace(),
					'sr_title'     => $title->getDbKey(),
					'sr_rev'       => $rev
				);
			}
			$dbw->insert( 'snapshot_revs', $data, __METHOD__ );
		}
		$dbw->commit();
		
		return $snapId;
	}
	
	
	/**
	 * Load a Snapshot object from a particluar snapshot ID
	 * @fixme fallback to master
	 */
	private static function fetchBy( $where, $options=array() ) {
		$db = wfGetDB( DB_SLAVE );
		$row = $db->selectRow( 'snapshot',
			array(
				'snap_id',
				'snap_page',
				'snap_rev',
				'snap_tag',
				'snap_timestamp',
				'snap_user',
				'snap_comment',
			),
			$where,
			__METHOD__,
			$options );
		
		if( $row ) {
			$snap = new Snapshot();
			$snap->mPageId = $row->snap_page;
			$snap->mRevisionId = $row->snap_rev;
			$snap->mTag = $row->snap_tag;
			$snap->mTimestamp = $row->snap_timestamp;
			$snap->mUser = $row->snap_user;
			$snap->mComment = $row->snap_comment;
			$snap->linksFromId( $db, $row->snap_id );
			return $snap;
		}
	}
	
	/**
	 * Load frozen snapshot version data from database into this object
	 * @param Database $db
	 * @param int $id snapshot id
	 */
	private function linksFromId( $db, $id ) {
		$this->mId = $id;
		
		$result = $db->select( 'snapshot_revs',
			array( 'sr_namespace', 'sr_title', 'sr_rev' ),
			array( 'sr_snap' => $id ),
			__METHOD__ );
		
		while( $row = $db->fetchObject( $result ) ) {
			$title = Title::makeTitle( $row->sr_namespace, $row->sr_title );
			$this->addPage( $title, $row->sr_rev );
		}
		
		$db->freeResult( $result );
	}
	
	public function fetchRevisionData( $db ) {
		$result = $db->select(
			array( 'snapshot_revs', 'revision', 'page' ),
			'*',
			array(
				'sr_snap' => $this->mId,
				'sr_rev=rev_id',
				'rev_page=page_id',
			),
			__METHOD__ );
		return $db->resultObject( $result );
	}
}

?>