<?php

// symlink me into maintenance/

require_once( 'commandLine.inc' );


if( !isset( $args[0] ) ) {
	print "Call MWUpdateDaemon remotely for status or updates.\n";
	print "Usage: php luceneUpdate.php [database] {status|stop|start|restart}\n";
	exit( -1 );
}

switch( $args[0] ) {
case 'stop':
	$ret = MWSearchUpdater::stop();
	break;
case 'flush':
case 'restart':
	$ret = MWSearchUpdater::stop();
	// fallthrough
case 'start':
	$ret = MWSearchUpdater::start();
	break;
case 'status':
	// no-op
	$ret = true;
	break;
default:
	echo "Unknown command.\n";
	exit( -1 );
}

if( WikiError::isError( $ret ) ) {
	echo $ret->getMessage() . "\n";
	exit( -1 );
}

echo MWSearchUpdater::getStatus() . "\n";
exit( 0 );

?>
