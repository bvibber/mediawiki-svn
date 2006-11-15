<?php

$basedir = '/mnt/static';

$wgNoDBParam = true;
require_once( '/home/wikipedia/common/php/maintenance/commandLine.inc' );

if ( !isset( $args[0] ) ) {
	echo "Usage: queueController.php <edition>\n";
}

$wikiList = array_map( 'trim', file( '/home/wikipedia/common/wikipedia.dblist' ) );
$yaseo = array_map( 'trim', file( '/home/wikipedia/common/yaseo.dblist' ) );
$wikiList = array_diff( $wikiList, $yaseo );

$targetQueueSize = 20;
$maxArticlesPerJob = 10000;
$jobTimeout = 86400;
$edition = $args[0];

$queueSock = fsockopen( 'localhost', 8200 );
if ( !$queueSock ) {
	echo "Unable to connect to queue server\n";
	die(1);
}

# Flush the queue
fwrite( $queueSock, "clear\n" );
fgets( $queueSock );

# Fetch wiki stats
$wikiSizes = @file_get_contents( "$basedir/checkpoints/wikiSizes" );
if ( $wikiSizes ) {
	$wikiSizes = unserialize( $wikiSizes );
} else {
	$wikiSizes = array();
	foreach ( $wikiList as $wiki ) {
		if ( $wgAlternateMaster[$wiki] ) {
			$db = new Database( $wgAlternateMaster[$wiki], $wgDBuser, $wgDBpassword, $wiki );
		} else {
			$db = wfGetDB( DB_SLAVE );
		}

		$wikiSizes[$wiki] = $db->selectField( "`$wiki`.site_stats", 'ss_total_pages' );
	}
	file_put_contents( "$basedir/checkpoints/wikiSizes", serialize( $wikiSizes ) );
}

# Compute job array
$jobs = array();
$jobsRemainingPerWiki = array();
foreach ( $wikiSizes as $wiki => $size ) {
	if ( in_array( $wiki, $yaseo ) ) {
		continue;
	}
	$numJobs = intval( ceil( $size / $maxArticlesPerJob ) );
	$jobsRemainingPerWiki[$wiki] = $numJobs;
	for ( $i = 1; $i <= $numJobs; $i++ ) {
		$jobs[] = "$wiki $i/$numJobs";
	}
}

$start = 0;
$doneCount = 0;
$queued = 0;
$jobCount = count( $jobs );
$queueTimes = array();
$initialisedWikis = array();

print "$jobCount jobs to do\n";

while ( $doneCount < $jobCount ) {
	for ( $i = $start; $i < $jobCount && getQueueSize() < $targetQueueSize; $i++ ) {
		if ( !isset( $jobs[$i] ) ) {
			# Already done and removed
			continue;
		}
		$job = $jobs[$i];
		list( $wiki ) = explode( ' ', $job );
		if ( !$wiki ) {
			die( "Invalid job: $job\n" );
		}
		$queueing = false;
		if ( isDone( $job ) ) {
			$doneCount++;
			print "Job $i done: $job ($doneCount of $jobCount)\n";
			$remaining = --$jobsRemainingPerWiki[$wiki];
			if ( !$remaining ) {
				finishWiki( $wiki );
			} else {
				print "$remaining jobs remaining for $wiki\n";
			}
			
			unset( $jobs[$i] );
			while ( !isset( $jobs[$start] ) && $start < $jobCount ) {
				$start++;
			}
		} elseif ( !isset( $queueTimes[$i] ) ) {
			print "Queueing job $i: $job\n";
			$queueing = true;
		} elseif ( time() > $queueTimes[$i] + $jobTimeout ) {
			print "Timeout, requeueing job $i: $job\n";
			$queueing = true;
		} else {
			$queueing = false;
		}
		if ( $queueing ) {
			if ( !isset( $initialisedWikis[$wiki] ) ) {
				startWiki( $wiki );
				$initialisedWikis[$wiki] = true;
			}
			enqueue( $job );
			$queueTimes[$i] = time();
		}
	}
	sleep(10);
}

//------------------------------------------------------------

function getQueueSize() {
	global $queueSock;
	if ( fwrite( $queueSock, "size\n" ) === false ) {
		die( "Unable to write to queue server\n" );
	}

	$response = fgets( $queueSock );
	if ( $response === false ) {
		die( "Unable to read from queue server\n" );
	}
	if ( !preg_match( "/^size (\d*)/", $response, $m ) ) {
		die( "Invalid response to size request\n" );
	}
	return $m[1];
}

function isDone( $job ) {
	global $basedir;
	$jobCpFile = "$basedir/checkpoints/" . strtr( $job, ' /', '__' );
	$lines = @file( $jobCpFile );
	if ( $lines === false ) {
		return false;
	}
	$test = 'everything=done';
	foreach ( $lines as $line ) {
		if ( substr( $line, 0, strlen( $test ) ) == $test ) {
			return true;
		}
	}
	return false;
}

function enqueue( $job ) {
	global $queueSock;
	if ( false === fwrite( $queueSock, "enq $job\n" ) ) {
		die( "Unable to write to queue server\n" );
	}
	
	# Read and throw away response
	$response = fgets( $queueSock );
}

function startWiki( $wiki ) {
	global $basedir;
	$lang = str_replace( 'wiki', '', $wiki );
	print "Starting language $lang\n";
	passthru( "$basedir/scripts/start-lang $lang" );
}

function finishWiki( $wiki ) {
	global $edition, $basedir;
	$lang = str_replace( 'wiki', '', $wiki );
	if ( !is_dir( "$basedir/wikipedia/$lang-new" ) ) {
		# Already compressed
		print "Already compressed $lang\n";
		return;
	}
	print "Finishing language $lang\n";
	passthru( "$basedir/scripts/finish-lang $lang $edition >> $basedir/logs/finish.log 2>&1 &" );
}

?>
