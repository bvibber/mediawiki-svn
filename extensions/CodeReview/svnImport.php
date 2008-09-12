<?php

require dirname(__FILE__) . "/../../maintenance/commandLine.inc";

$repo = 'http://svn.wikimedia.org/svnroot/mediawiki';
$svn = SubversionAdaptor::newFromRepo( $repo );
$chunkSize = 100;
$lastRev = 45000;

$startTime = microtime( true );
$revCount = 0;

for( $start = 1; $start < $lastRev; $start += $chunkSize ) {
	$log = $svn->getLog( '', $start, $start + $chunkSize - 1 );
	foreach( $log as $data ) {
		$revCount++;
		$delta = microtime( true ) - $startTime;
		$revSpeed = $revCount / $delta;
		
		$codeRev = CodeRevision::newFromSvn( $data );
		$codeRev->save();
		
		printf( "%d %s %s (%0.1f revs/sec)\n",
			$codeRev->mId,
			wfTimestamp( TS_DB, $codeRev->mTimestamp ),
			$codeRev->mAuthor,
			$revSpeed );
	}
}
