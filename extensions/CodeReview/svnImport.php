<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if( $IP === false )
	$IP = dirname( __FILE__ ). '/../..';
require "$IP/maintenance/commandLine.inc";

if( !isset( $args[0] ) ){
	echo "Usage: php svnImport.php <repo>\n";
	die;
}

$repo = CodeRepository::newFromName( $args[0] );

if( !$repo ){
	echo "Invalid repo {$args[0]}\n";
	die;
}

$svn = SubversionAdaptor::newFromRepo( $repo->getPath() );
$lastStoredRev = $repo->getLastStoredRev();

$chunkSize = 200;
$lastRev = 45000;

$startTime = microtime( true );
$revCount = 0;

echo "Syncing repo {$args[0]} from r$lastStoredRev to HEAD...\n";
for( $start = $lastStoredRev + 1; $start < $lastRev; $start += $chunkSize ) {
	$log = $svn->getLog( '', $start, $start + $chunkSize - 1 );
	if( empty($log) ) {
		# Repo seems to give a blank when max rev is invalid, which 
		# stops new revisions from being added. Try to avoid this
		# by trying less at a time from the last point.
		if( $chunkSize <= 1 ) {
			die(); // done!
		}
		$start = max( $lastStoredRev + 1, $start - $chunkSize ); // Go back!
		$chunkSize = max( 1, floor($chunkSize/3) );
	}
	foreach( $log as $data ) {
		$revCount++;
		$delta = microtime( true ) - $startTime;
		$revSpeed = $revCount / $delta;

		$codeRev = CodeRevision::newFromSvn( $repo, $data );
		$codeRev->save();

		printf( "%d %s %s (%0.1f revs/sec)\n",
			$codeRev->mId,
			wfTimestamp( TS_DB, $codeRev->mTimestamp ),
			$codeRev->mAuthor,
			$revSpeed );
	}
}
