<?php

/**
 * To find settings that aren't configurable by the extension.
 * Based on findhooks.php
 *
 * @addtogroup Extensions
 * @author Alexandre Emsenhuber
 * @license GPLv2 or higher
 */

$dir = dirname( __FILE__ );
$IP = "$dir/../..";
@include( "$dir/../CorePath.php" ); // Allow override
require_once( "$IP/maintenance/commandLine.inc" );

/**
 * Nicely output the array
 * @param $msg A message to show before the value
 * @param $arr An array
 * @param $sort Boolean : wheter to sort the array (Default: true)
 */
function printArray( $msg, $arr, $sort = true ) {
	if($sort) asort($arr); 
	foreach($arr as $v) echo "$msg: $v\n";
}

// Get our settings defs
$allSettings = array_keys( SpecialConfigure::getAllSettings() );

// Now we'll need to open DefaultSettings.php
$m = array();
$defaultSettings = file_get_contents( "$IP/includes/DefaultSettings.php" );
preg_match_all( '/\$(wg[A-Za-z0-9]+)\s*\=/', $defaultSettings, $m );
$definedSettings = array_unique( $m[1] );

$missing = array_diff( $definedSettings, $allSettings );
$remain = array_diff( $allSettings, $definedSettings );
$obsolete = array();
foreach( $remain as $setting ){
	if( SpecialConfigure::isSettingAvailable( $setting ) )
		$obsolete[] = $setting;
}

// let's show the results:
printArray('missing', $missing );
printArray('obsolete', $obsolete );
 
if( count( $missing ) == 0 && count( $obsolete ) == 0 ) 
	echo "Looks good!\n";