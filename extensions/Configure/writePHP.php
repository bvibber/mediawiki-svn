<?php

/**
 * Maintenance script that helps to do maintenance with configuration files.
 *
 * @file
 * @ingroup Extensions
 * @author Alexandre Emsenhuber
 * @license GPLv2 or higher
 */

$optionsWithArgs = array( 'wiki', 'version', 'file' );

$dir = dirname(__FILE__) . '/';
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false )
	$IP = $dir . '../..';

require_once( "$IP/maintenance/commandLine.inc" );

require_once( $dir . 'writePHP.inc' );

$obj = new ConfigurationWriter( $options );
$obj->run();
