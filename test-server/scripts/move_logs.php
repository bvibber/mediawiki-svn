<?php 
/*
 * This script moves all old cruisecontrol logs that it finds into an archive directory.
 * TODO: Make this a cron job on the ci server
 */

#main 
$options = getopt( 'c:l:h', array( 'ccdir:', 'logArchive', 'help' ) );

if ( isset( $options['h'] ) ) {
	usage();
	exit(0);
}

if ( !isset( $options['c'] ) ) {
	exitOnError( 'Missing required argument -c', true );
}
if ( !isset( $options['l'] ) ) {
	exitOnError( 'Missing required argument -l', true );
}

$ccDir = $options['c'];
$ccLogDir = $ccDir . '/logs';
$logArchiveDir = $options['l'];

if ( !is_readable( $ccDir ) ) {
	exitOnError( 'Could not read cruisecontrol home directory : ' . $ccDir );
} 
if ( !is_readable( $ccLogDir ) ) {
	exitOnError( 'Could not read cruisecontrol log directory : ' . $ccLogir );
}

if ( !is_writable( $logArchiveDir ) ) {
	exitOnError( 'Could not write to the log archive destination : ' . $logArchiveDir );
}

//create a subdirectory for this run. 
$newArchiveDir = $logArchiveDir . date('Y-m-d_G-i-s');
mkdir( $newArchiveDir );
//all logs archived by cruisecontrol
$command = 'find ' .  $ccLogDir . ' -type f -and -name "log*\.gz"';
findAndMove( $command, $newArchiveDir);
//all old cruisecontrol run logs
$command = 'find ' .  $ccDir . ' -name "cruisecontrol\.log\.*"';
findAndMove( $command, $newArchiveDir);
 
function usage() {
	$line = "Usage: php " . basename(__FILE__) . " -c CCHOMEDIR -l LOGARCHIVEDEST\n";
	$line .= "-c, --ccdir=CCHOMEDIR		Cruisecontrol home directory (assumes logs are under CCHOMEDIR/logs).\n";
	$line .= "-l, --logArchive=LOGARCHIVEDEST	Destination directory for the archived logs.\n";
	$line .= "-h, --help		This help message\n";
	print $line;
}

function exitOnError( $errorMessage = '' , $showUsage = false ) {
	if ( strlen( $errorMessage ) > 0 ) {
		print "Error: " . $errorMessage . "\n";
	}
	if ( $showUsage ) {
		usage();
	}
	exit(1);
}

/*
 * Execute each command and move each file returned by $command into $archiveDir
 */
function findAndMove( $command, $archiveDir ) {
	$result = array();
	exec($command, $result );

	foreach ( $result as $logFile  ) {
		$name = basename( $logFile );
		rename( $logFile, $archiveDir . '/' . $name ); 
	}	
} 

