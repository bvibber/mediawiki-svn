<?php

if( php_sapi_name() !== 'cli' ) {
	die( "Command-line script only, sorry.");
}

$base = dirname(__FILE__);
require "$base/config.php";
require "$base/Subversion.php";

// Read out queued revisions, if any...
$revs = array();
$dir = dir( $queueDir );
while( ( $filename = $dir->read() ) !== false ) {
	if( preg_match( '/^\d+$/', $filename ) ) {
		$revs[] = intval( $filename );
	}
}
$dir->close();

// Run through them in order and run tests, if applicable.
sort( $revs );
foreach( $revs as $revId ) {
	checkCommit( $revId );
}




function checkCommit( $revId ) {
	global $targetRepo, $testSuites, $queueDir;
	$repo = SubversionAdaptor::newFromRepo( $targetRepo );
	
	// Check for changes which trigger our test suites...
	foreach( $testSuites as $suite ) {
		// Potentially expensive with multiple test sets, since we hit SVN for each one
		$log = $repo->getLog( $suite['path'], $revId, $revId );
		if( isset( $log[0]['paths'] ) && count( $log[0]['paths'] ) ) {
			// There were changes to thsi path in this revision.
			// Schedule it for testing!
			echo "Running $suite[name] on $suite[path] r$revId...\n";
			runTests( $suite, $revId );
		} else {
			echo "Skipping $suite[name] on $suite[path] r$revId...\n";
		}
		
		echo "De-queueing r$revId...\n";
		unlink( "$queueDir/$revId" );
	}
}

function runTests( $suite, $revId ) {
	$ok = chdir( $suite['localpath'] );
	if( !$ok ) {
		echo "Abort! chdir failed.\n";
		return false;
	}
	passthru( "svn up -r$revId", $retval );
	if( $retval ) {
		echo "Abort! SVN up failed.\n";
		return false;
	}
	passthru( "php maintenance/update.php --quick", $retval );
	if( $retval ) {
		echo "Abort! MW updaters failed.\n";
		return false;
	}
	passthru( $suite['command'], $retval );
	if( $retval ) {
		// Fixme... find a way to distinguish between failure to run
		// and failure of some test cases. We want to be able to mark
		// output as aborted if we had a fatal error for instance.
		echo "Test case reported failure.\n";
		return false;
	}
	return true;
}