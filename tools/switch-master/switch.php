<?php

$confFile = dirname(__FILE__).'/SwitchSettings.php';

if ( !file_exists( $confFile ) ) {
	copy( "$confFile.sample", $confFile );
}

$switchConf = array();
$broken = false;
$IP = getenv( 'MW_INSTALL_PATH' );
require( $confFile ); # Sets $switchConf

if ( $broken ) {
	echo "Please customise SwitchSettings.php\n";
	exit( 1 );
}

if ( $IP === false ) {
	echo "Unable to find MediaWiki installation. " .
		"Set the MW_INSTALL_PATH environment variable, or set \$IP in SwitchSettings.php.\n";
	exit( 1 );
}

$optionsWithArgs = array( 'slave-load', 'master-load' );
$wgNoDBParam = true;
require( "$IP/maintenance/commandLine.inc" );
$wgAutoloadClasses['ConfEditor'] = dirname(__FILE__).'/ConfEditor.php';
$wgAutoloadClasses['MasterSwitcher'] = dirname(__FILE__).'/MasterSwitcher.php';

if ( count( $args ) < 2 ) {
	echo <<<EOT
Usage: switch.php [options] <old-master> <new-master>

Options: 
   --slave-load=<load-spec>
   --master-load=<load-spec>

where <load-spec> is a list of loads per section:

   <section>=<load>[,<section>=<load> [, ...] ]

Any unspecified loads will be set to 100 for (ex-master) slaves,
and 0 for new masters.

EOT;

	exit( 1 );
}

$switchOptions = array();
if ( isset( $options['slave-load'] ) ) {
	$switchOptions['newLoad'] = parseLoad( $options['slave-load'] );
}
if ( isset( $options['master-load'] ) ) {
	$switchOptions['masterLoad'] = parseLoad( $options['master-load'] );
}

$switcher = new MasterSwitcher( $switchConf );

$result = $switcher->switchMaster( $args[0], $args[1], $switchOptions );
if ( !$result ) {
	exit( 1 );
}
exit( 0 );


function parseLoad( $s ) {
	global $wgLBFactoryConf;
	$loads = array();
	$sectionStrings = array_map( 'trim', explode( ',', $s ) );
	foreach( $sectionStrings as $sectionString ) {
		$parts = array_map( 'trim', explode( '=', $sectionString ) );
		if ( count( $parts ) != 2 ) {
			echo "Invalid load specification \"$sectionString\"\n";
			exit( 1 );
		}
		if ( !preg_match( '/^\d+$/', $parts[1] ) ) {
			echo "Invalid load \"{$parts[1]}\"\n";
			exit( 1 );
		}
		if ( !isset( $wgLBFactoryConf['sectionLoads'][$parts[0]] ) ) {
			echo "Invalid section \"{$parts[0]}\"\n";
			exit( 1 );
		}
		$loads[$parts[0]] = intval( $parts[1] );
	}
	return $loads;
}
