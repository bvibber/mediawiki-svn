<?php

/**
 * Class to update the categorylinks cl_target ids when a category is moved.
 *
 * HTMLCacheUpdate-alike, if the query affects a small numbers of rows, process it
 * immediately; if not, split it into smaller queries, and push them to the JobQueue
 */
class CategoryLinksUpdateOnMove
{
	public $mTitle;
	public $mRowsPerJob, $mRowsPerQuery;
	public $mFromTarget, $mFromInline, $mToTargetID;

	/**
	 * Moving a category from ($fromInline, $fromTarget) to cl_target $toTargetID
	 *
	 * @param Title $title cat_title (unused, needed for Job::factory)
	 * @param int $fromInline
	 * @param int $fromTarget
	 * @param int $toID
	 */
	function __construct( $title, $fromInline, $fromTarget, $toTargetID ) {
		global $wgUpdateRowsPerJob, $wgUpdateRowsPerQuery;

		$this->mTitle = $title;
		$this->mFromTarget = $fromTarget;
		$this->mFromInline = $fromInline;
		$this->mToTargetID = $toTargetID;
		$this->mRowsPerJob = $wgUpdateRowsPerJob;
		$this->mRowsPerQuery = $wgUpdateRowsPerQuery;
	}

	/**
	 * Returns the cl_from ids that need to be changed
	 * 
	 * @param Array $conds additional 'where' conditions 
	 * @return ResultWrapper
	 */
	function fetchIDs( $conds = array() ) {
		$dbr = wfGetDB( DB_SLAVE );
		
		$conds['cl_target'] = $this->mFromTarget;
		$conds['cl_inline'] = $this->mFromInline;
		
		$res = $dbr->select( 'categorylinks', 
							 'cl_from', 
							 $conds, 
							 __METHOD__
							);
		return $res;
	}
	
	function doUpdate() {
		$res = $this->fetchIDs();

		if ( $res->numRows() != 0 ) {
			if ( $res->numRows() > $this->mRowsPerJob ) {
				$this->insertJobs( $res );
			} else {
				$this->updateIDs( $res );
			}
		}
	}

	/**
	 * Splits the ids into smaller queries and put them
	 * into JobQueue
	 *
	 * @param ResultWrapper $res
	 */
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
				'inline' => $this->mFromInline,
				'target' => $this->mFromTarget,
				'to'	 => $this->mToTargetID
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
				array( 'cl_target' => $this->mToTargetID ),
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
	public $inline, $target, $to;

	/**
	 * Construct a job
	 * @param Title $title The title linked to
	 * @param array $params Job parameters (table, start and end page_ids)
	 * @param integer $id job_id
	 */
	function __construct( $title, $params, $id = 0 ) {
		parent::__construct( 'categoryMoveJob', $title, $params, $id );
		
		foreach ($params as $name => $value ) {
			$this->{$name} = $value;
		}
	}

	function run() {
		$update = new CategoryLinksUpdateOnMove( $this->title, $this->inline, $this->target, $this->to );

		$conds = array();
		if ( $this->start ) {
			$conds[] = "$fromField >= {$this->start}";
		}
		if ( $this->end ) {
			$conds[] = "$fromField <= {$this->end}";
		}

		$res = $update->fetchIDs( $conds );
		$update->updateIDs( $res );

		return true;
	}
}
