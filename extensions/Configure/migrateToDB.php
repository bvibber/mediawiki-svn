<?php

/**
 * Maintenance script that migrate configuration from files to database.
 *
 * @file
 * @ingroup Extensions
 * @author Alexandre Emsenhuber
 * @license GPLv2 or higher
 */

$dir = dirname(__FILE__) . '/';
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false )
	$IP = $dir . '../..';

require_once( "$IP/maintenance/commandLine.inc" );

require_once( $dir . "migrateToDB.inc" );

$obj = new FilesToDB( $options );
$obj->run();
