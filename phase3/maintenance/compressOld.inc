<?php
/**
 * @package MediaWiki
 * @subpackage Maintenance
 */

/** */
function compressOldPages( $start = 0 ) {
	$fname = 'compressOldPages';

	$chunksize = 50;
	print "Starting from old_id $start...\n";
	$dbw =& wfGetDB( DB_MASTER );
	$old = $dbw->tableName( 'old' );
	do {
		$end = $start + $chunksize;
		$res = $dbw->select( 'old', array( 'old_id','old_flags','old_namespace','old_title','old_text' ),
			"old_id>=$start", $fname, array( 'ORDER BY' => 'old_id', 'LIMIT' => $chunksize, 'FOR UPDATE' ) );
		if( $dbw->numRows( $res ) == 0 ) {
			break;
		}
		$last = $start;
		while( $row = $dbw->fetchObject( $res ) ) {
			# print "  {$row->old_id} - {$row->old_namespace}:{$row->old_title}\n";
			compressPage( $row );
			$last = $row->old_id;
		}
		$dbw->freeResult( $res );
		$start = $last + 1; # Deletion may leave long empty stretches
		print "$start...\n";
	} while( true );
}

function compressPage( $row ) {
	$fname = 'compressPage';
	if( false !== strpos( $row->old_flags, "gzip" ) ) {
		print "Already compressed row {$row->old_id}?\n";
		return false;
	}
	$dbw =& wfGetDB( DB_MASTER );
	$flags = $row->old_flags ? "{$row->old_flags},gzip" : "gzip";
	$compress = gzdeflate( $row->old_text );
	$dbw->update( 'old', 
		array( /* SET */
			'old_flags' => $flags,
			'old_text' => $compress
		), array( /* WHERE */
			'old_id' => $row->old_id
		), $fname, 'LIMIT 1'
	);
	return true;
}

function compressWithConcat( $startId, $maxChunkSize, $maxChunkFactor, $factorThreshold, $beginDate, $endDate )
{
	$fname = 'compressWithConcat';
	$dbw =& wfGetDB( DB_MASTER );
	
	# First get a list of all pages
	$pageRes = $dbw->select( 'page', 'page_id', false, $fname );

	# For each of those, get a list of revisions which fit the criteria
	$conds = array();
	if ( $beginDate ) {
		$conds[] = "rev_timestamp>'" . $beginDate . "'";
	} 
	if ( $endDate )  {
		$conds[] = "rev_timestamp<'" . $endDate . "'";
	}
	if ( $startId ) {
		$conds[] = 'rev_id>=' . $startId;
	}

	while ( $pageRow = $dbw->fetchObject( $pageRes ) ) {
		$revRes = $dbw->select( 'revision', 'rev_id', 
			array( 'rev_page' => $pageRow->page_id ) + $conds, 
			$fname 
		);
		$revs = array();
		while ( $revRow = $dbw->fetchObject( $revRes ) ) {
			$revs[] = $revRow->rev_id;
		}
		
		if ( !count( $revs ) ) {
			# No revisions matching, no further processing
			continue;
		}

		# For each chunk
		$i = 0;
		while ( $i < count( $revs ) ) {
			if ( $i < count( $revs ) - $maxChunkSize ) {
				$thisChunkSize = $maxChunkSize;
			} else {
				$thisChunkSize = count( $revs ) - $i;
			}

			$chunk = new ConcatenatedGzipHistoryBlob();
			$stubs = array();
			$dbw->begin();
			# Get the text of each revision and add it to the object
			for ( $j = 0; $j < $thisChunkSize && $chunk->isHappy( $maxChunkFactor, $factorThreshold ); $j++ ) {
				$textRow = $dbw->selectRow( 'text', 
					array( 'old_flags', 'old_text' ),
					array( 'old_id' => $revs[$i + $j] ),
					$fname,
					'FOR UPDATE'
				);
				$text = Article::getRevisionText( $textRow );
				if ( $j == 0 ) {
					$chunk->setText( $text );
				} else {
					$stubs[$j] = $chunk->addItem( $text );
					$stubs[$j]->setLocation( $revs[$i] );
				}
			}
			$thisChunkSize = $j;

			# Store the main object
			$chunk->compress();
			$dbw->update( 'text',
				array( /* SET */
					'old_text' => serialize( $chunk ),
					'old_flags' => 'object',
				), array( /* WHERE */
					'old_id' => $revs[$i]
				)
			);

			# Store the stub objects
			for ( $j = 1; $j < $thisChunkSize; $j++ ) {
				$dbw->update( 'text',
					array( /* SET */
						'old_text' => serialize( $stubs[$j] ),
						'old_flags' => 'object',
					), array( /* WHERE */
						'old_id' => $revs[$i + $j]
					)
				);
			}
			
			# Done, next
			$dbw->commit();
			$i += $thisChunkSize;
		}
	}
}
?>
