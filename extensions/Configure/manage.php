<?php

/**
 * Maintenance script that helps to do maintenance with configuration files.
 *
 * @file
 * @ingroup Extensions
 * @author Alexandre Emsenhuber
 * @license GPLv2 or higher
 */

$optionsWithArgs = array( 'revert', 'delete' );
define( 'EXT_CONFIGURE_NO_EXTRACT', true );

$dir = dirname( __FILE__ );
$IP = "$dir/../..";
@include( "$dir/../CorePath.php" ); // Allow override
require_once( "$IP/maintenance/commandLine.inc" );

require_once( dirname( __FILE__ ) . "/manage.inc" );

$obj = new ConfigurationManager( $options );
$obj->run();
