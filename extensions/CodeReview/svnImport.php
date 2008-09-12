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

$chunkSize = 100;
$lastRev = 45000;

$startTime = microtime( true );
$revCount = 0;

for( $start = $lastStoredRev + 1; $start < $lastRev; $start += $chunkSize ) {
	$log = $svn->getLog( '', $start, $start + $chunkSize - 1 );
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
