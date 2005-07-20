<?php

// symlink me into maintenance/

$options = array( 'skip' );
require_once( 'commandLine.inc' );


if( !isset( $args[0] ) ) {
	print "Call MWUpdateDaemon remotely for status or updates.\n";
	print "Usage: php luceneUpdate.php [database] [--skip=n] {status|flush|stop|start|restart|rebuild}\n";
	exit( -1 );
}

switch( $args[0] ) {
case 'stop':
	$ret = MWSearchUpdater::stop();
	break;
case 'restart':
case 'flushall':
case 'flush':
	$ret = MWSearchUpdater::flushAll();
	break;
case 'start':
	$ret = MWSearchUpdater::start();
	break;
//case 'flush':
//	global $wgDBname;
//	$ret = MWSearchUpdater::flush( $wgDBname );
//	break;
case 'status':
	// no-op
	$ret = true;
	break;
case 'quit':
	$ret = MWSearchUpdater::quit();
	break;
case 'update':
	$title = Title::newFromText( $args[1] );
	if( is_null( $title ) ) {
		die( "Invalid title\n" );
	}
	$rev = Revision::newFromTitle( $title );
	if( $rev ) {
		$text = $rev->getText();
		$ret = MWSearchUpdater::updatePage( $wgDBname, $title, $text );
	} else {
		$ret = MWSearchUpdater::deletePage( $wgDBname, $title );
	}
	break;
case 'rebuild':
	$builder = new LuceneBuilder();
	if( isset( $options['skip'] ) ) {
		$builder->skip( intval( $options['skip'] ) );
	}
	$ret = $builder->rebuildAll();
	break;
case 'deleted':
	$builder = new LuceneBuilder();
	$ret = $builder->rebuildDeleted();
	break;
default:
	echo "Unknown command.\n";
	exit( -1 );
}

if( WikiError::isError( $ret ) ) {
	echo $ret->getMessage() . "\n";
	exit( -1 );
}

$status = MWSearchUpdater::getStatus();
if( WikiError::isError( $status ) ) {
	echo $status->getMessage() . "\n";
	exit( -1 );
} else {
	echo $status . "\n";
	exit( 0 );
}


///

class LuceneBuilder {
	function LuceneBuilder() {
		$this->db       =& wfGetDB( DB_SLAVE );
		$this->dbstream =& $this->streamingSlave( $this->db );
		$this->offset = 0;
	}
	
	function &streamingSlave( $db ) {
		global $wgDBname;
		$stream = new Database( $db->mServer, $db->mUser, $db->mPassword, $wgDBname );
		$stream->bufferResults( false );
		
		$timeout = 3600 * 24;
		$stream->query( "SET net_read_timeout=$timeout" );
		$stream->query( "SET net_write_timeout=$timeout" );
		return $stream;
	}
	
	function skip( $offset ) {
		$this->offset = intval( $offset );
	}
	
	function init( $max ) {
		$this->max = $max;
		$this->count = 0;
		$this->startTime = wfTime();
	}
	
	function progress() {
		global $wgDBname;
		$this->count++;
		if( $this->count % 100 == 0 ) {
			$now = wfTime();
			$delta = $now - $this->startTime;
			$portion = $this->count / $this->max;
			$eta = $this->startTime + ($delta / $portion);
			$rate = $this->count / $delta;
			
			printf( "%s: %6.3f%% on %s, ETA %s [%d/%d] %.2f/sec\n",
				wfTimestamp( TS_DB, intval( $now ) ),
				$portion * 100.0,
				$wgDBname,
				wfTimestamp( TS_DB, intval( $eta ) ),
				$this->count,
				$this->max,
				$rate );
			
			$this->wait();
		}
	}
	
	/**
	 * See if the daemon's getting overloaded and pause if so
	 */
	function wait() {
		$cutoff = 500;
		$waittime = 10;
		
		while( true ) {
			$status = MWSearchUpdater::getStatus();
			if( WikiError::isError( $status ) ) {
				echo $status->getMessage() . "\n";
				sleep( $waittime );
				continue;
			}
			
			// Updater IS running; 90418 items queued.
			if( !preg_match( '/([0-9]+) items queued/', $status, $matches ) ) {
				// ?! confused
				break;
			}
			
			$count = intval( $matches[1] );
			if( $count < $cutoff ) {
				break;
			}
			
			printf( "%s: %s\n",
				wfTimestamp( TS_DB ),
				$status );
			
			sleep( $waittime );
		}
	}
	
	function final() {
		global $wgDBname;
		$now = wfTime();
		$delta = $now - $this->startTime;
		$portion = $this->count / $this->max;
		$eta = $now + ($delta / $portion);
		$rate = $this->count / $delta;
		
		printf( "%s: done on %s, [%d/%d] %.2f/sec\n",
			wfTimestamp( TS_DB, intval( $now ) ),
			$wgDBname,
			$this->count,
			$this->max,
			$rate );
	}
	
	function rebuildAll() {
		$fname = 'LuceneBuilder::rebuildAll';
		global $wgDBname;
		
		$lastError = true;
		
		$maxId = $this->db->selectField( 'page', 'MAX(page_id)', '', $fname );
		$maxId -= $this->offset; // hack for percentages
		$this->init( $maxId );
		if( $maxId < 1 ) {
			echo "Nothing to do.\n";
			return;
		}
		
		$limit = array();
		if( $this->offset ) {
			$limit = array( 'LIMIT $offset, 10000000' );
		}
		
		$result = $this->dbstream->select( array( 'page' ),
			array( 'page_namespace', 'page_title', 'page_latest' ),
			'',
			$fname,
			$limit );
		
		$errorCount = 0;
		while( $row = $this->dbstream->fetchObject( $result ) ) {
			$this->progress();
			
			$title = Title::makeTitle( $row->page_namespace, $row->page_title );
			$rev = Revision::newFromId( $row->page_latest );
			if( is_null( $rev ) ) {
				// Page was probably deleted while we were running
				continue;
			}
			
 			$text = $rev->getText();
			$hit = MWSearchUpdater::updatePage( $wgDBname, $title, $text );
			
			if( WikiError::isError( $hit ) ) {
				echo "ERROR: " . $hit->getMessage() . "\n";
				$lastError = $hit;
				$errorCount++;
				if( $errorCount > 20 ) {
					echo "Lots of errors, giving up. :(\n";
					return $lastError;
				}
			}
		}
		$this->final();
		$this->dbstream->freeResult( $result );
		
		return $lastError;
	}
	
	/**
	 * Trawl thorugh the deletion log entries to remove any deleted pages
	 * that might have been missed when the index updater daemon was broken.
	 */
	function rebuildDeleted( $since = null ) {
		global $wgDBname, $options;
		$fname   = 'LuceneBuilder::rebuildDeleted';
		
		if( is_null( $since ) ) {
			$since = '20010115000000';
		}

		// Turn buffering back on; these are relatively small.
		$this->dbstream->bufferResults( true );
		
		$cutoff  = $this->dbstream->addQuotes( $this->dbstream->timestamp( $since ) );
		$logging = $this->dbstream->tableName( 'logging' );
		$page    = $this->dbstream->tableName( 'page' );
		$result  = $this->dbstream->query(
			"SELECT log_namespace,log_title
			 FROM $logging
			 LEFT OUTER JOIN $page
			 ON log_namespace=page_namespace AND log_title=page_title
			 WHERE log_type='delete'
			 AND log_timestamp > $cutoff
			 AND page_namespace IS NULL", $fname );
		
		$max = $this->dbstream->numRows( $result );
		if( $max == 0 ) {
			echo "Nothing to do.\n";
			return;
		}
		$this->init( $max );
		$lastError = true;
		
		while( $row = $this->dbstream->fetchObject( $result ) ) {
			$this->progress();
			$title = Title::makeTitle( $row->log_namespace, $row->log_title );
			$hit = MWSearchUpdater::deletePage( $wgDBname, $title );
			if( WikiError::isError( $hit ) ) {
				echo "ERROR: " . $hit->getMessage() . "\n";
				$lastError = $hit;
			}
		}
		
		$this->final();
		$this->dbstream->freeResult( $result );
		
		return $lastError;
	}
}

?>
