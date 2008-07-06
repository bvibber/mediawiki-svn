<?php

/**
 * Class to update the categorylinks cl_target ids when a category is moved.
 *
 */
class CategoryMove
{
	public $mTitle;
	public $mRowsPerJob, $mRowsPerQuery;
	public $mFromID, $mToID;

	/**
	 * Moving a category from cat_id $fromID to cat_id $toID
	 *
	 * @param Title $title cat_title (unused, needed for Job::factory)
	 * @param int $fromID
	 * @param int $toID
	 */
	function __construct( $title, $fromID, $toID ) {
		global $wgUpdateRowsPerJob, $wgUpdateRowsPerQuery;

		$this->mTitle = $title;
		$this->mFromID = $fromID;
		$this->mToID = $toID;
		$this->mRowsPerJob = $wgUpdateRowsPerJob;
		$this->mRowsPerQuery = $wgUpdateRowsPerQuery;
	}

	function doUpdate() {
		# Fetch the IDs
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'categorylinks', 
							 'cl_from', 
							 array( 'cl_target' => $this->mFromID ), 
							 __METHOD__
							);

		if ( $dbr->numRows( $res ) != 0 ) {
			if ( $dbr->numRows( $res ) > $this->mRowsPerJob ) {
				$this->insertJobs( $res );
			} else {
				$this->updateIDs( $res );
			}
		}
	}

	function insertJobs( ResultWrapper $res ) {
		$numRows = $res->numRows();
		$numBatches = ceil( $numRows / $this->mRowsPerJob );
		$realBatchSize = $numRows / $numBatches;
		$start = false;
		$jobs = array();
		do {
			for ( $i = 0; $i < $realBatchSize - 1; $i++ ) {
				$row = $res->fetchRow();
				if ( $row ) {
					$id = $row[0];
				} else {
					$id = false;
					break;
				}
			}

			$params = array(
				'start' => $start,
				'end' => ( $id !== false ? $id - 1 : false ),
			);
			$jobs[] = new CategoryMoveJob( $this->mTitle, $params );

			$start = $id;
		} while ( $start );

		Job::batchInsert( $jobs );
	}
	
	/**
	 * Update a set of IDs, right now
	 */
	function updateIDs( ResultWrapper $res ) {

		if ( $res->numRows() == 0 ) {
			return;
		}

		$dbw = wfGetDB( DB_MASTER );
		$done = false;

		while ( !$done ) {
			# Get all IDs in this query into an array
			$ids = array();
			for ( $i = 0; $i < $this->mRowsPerQuery; $i++ ) {
				$row = $res->fetchRow();
				if ( $row ) {
					$ids[] = $row[0];
				} else {
					$done = true;
					break;
				}
			}

			if ( !count( $ids ) ) {
				break;
			}

			$dbw->update( 'categorylinks',
				array( 'cl_target' => $this->mToID ),
				array( 'cl_from' => $ids ),
				__METHOD__
			);

		}
	}
}

/**
 * 
 * @ingroup JobQueue
 */
class CategoryMoveJob extends Job {
	public $start, $end;

	/**
	 * Construct a job
	 * @param Title $title The title linked to
	 * @param array $params Job parameters (table, start and end page_ids)
	 * @param integer $id job_id
	 */
	function __construct( $title, $params, $id = 0 ) {
		parent::__construct( 'categoryMoveJob', $title, $params, $id );
		$this->start = $params['start'];
		$this->end = $params['end'];
	}

	function run() {
		$update = new HTMLCacheUpdate( $this->title, $this->table );

		$fromField = $update->getFromField();
		$conds = $update->getToCondition();
		if ( $this->start ) {
			$conds[] = "$fromField >= {$this->start}";
		}
		if ( $this->end ) {
			$conds[] = "$fromField <= {$this->end}";
		}

		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $tables, $fromField, $conds, __METHOD__ );
		$update->updateIDs( $res );

		return true;
	}
}
