<?php
/**
 * Makes the required database updates for Special:ProtectedPages
 * to show all protected pages, even ones before the page restrictions
 * schema change. All remaining page_restriction column values are moved
 * to the new table.
 *
 * @file
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class PopulateLogUsertext extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Populates the log_user_text";
		$this->setBatchSize( 100 );
	}

	public function execute() {
		$db = wfGetDB( DB_MASTER );
		$start = $db->selectField( 'logging', 'MIN(log_id)', false, __METHOD__ );
		if( !$start ) {
			$this->output( "Nothing to do.\n" );
			return true;
		}
		$end = $db->selectField( 'logging', 'MAX(log_id)', false, __METHOD__ );

		# Do remaining chunk
		$end += $this->mBatchSize - 1;
		$blockStart = $start;
		$blockEnd = $start + $this->mBatchSize - 1;
		while( $blockEnd <= $end ) {
			$this->output( "...doing log_id from $blockStart to $blockEnd\n" );
			$cond = "log_id BETWEEN $blockStart AND $blockEnd AND log_user = user_id";
			$res = $db->select( array('logging','user'), 
				array('log_id','user_name'), $cond, __METHOD__ );
			$batch = array();
			$db->begin();
			while( $row = $db->fetchObject( $res ) ) {
				$db->update( 'logging', array('log_user_text' => $row->user_name),
					array('log_id' => $row->log_id), __METHOD__ );
			}
			$db->commit();
			$blockStart += $this->mBatchSize;
			$blockEnd += $this->mBatchSize;
			wfWaitForSlaves( 5 );
		}
		if( $db->insert(
				'updatelog',
				array( 'ul_key' => 'populate log_usertext' ),
				__METHOD__,
				'IGNORE'
			)
		) {
			$this->output( "log_usertext population complete.\n" );
			return true;
		} else {
			$this->output( "Could not insert log_usertext population row.\n" );
			return false;
		}
	}
}

$maintClass = "PopulateLogUsertext";
require_once( DO_MAINTENANCE );
