<?php

/**
 * DateDiff extension.
 *
 * On MediaWiki.org: 		http://www.mediawiki.org/wiki/Extension:DateDiff
 *
 * @file DateDiff.php
 *
 * @author David Raison
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define( 'Datediff_VERSION', '0.1' );

$wgExtensionMessagesFiles['DateDiff'] = dirname( __FILE__ ) . '/DateDiff.i18n.php';
$wgExtensionFunctions[] = "efDateDiff";
$wgHooks['LanguageGetMagic'][] = 'efDatesFunctionMagic';

// Extension credits that show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
    'name' => 'Datediff',
    'author' => 'David Raison',
    'url' => 'http://www.mediawiki.org/wiki/Extension:DateDiff',
	'path' => __FILE__,
    'descriptionmsg' => 'datediff_desc',
	'version' => Datediff_VERSION,
);

function efDateDiff() {
    global $wgParser;
    $wgParser->setFunctionHook( 'dates', 'calcdates' );
}

/**
 * Adds the magic words for the parser functions
 */
function efDatesFunctionMagic( &$magicWords, $langCode ) {
    $magicWords['dates'] = array( 0, 'dates' );
	return true;
}

function calcdates( &$parser ) {
	$params = func_get_args();
    array_shift( $params ); // We already know the $parser ...
    
	while ( empty( $params[0] ) ) {
		array_shift( $params );
	}

	$dates = array();
	
	foreach ( $params as $pair ) {
		// We currently ignore the label of the date.
		$dates[] = substr( $pair, strpos( $pair, '=' ) + 1 );
	}
		

	$time1 = strtotime( $dates[0] );
	$time2 = strtotime( $dates[1] );

	$a = ( $time2 > $time1 ) ? $time2 : $time1;       // higher
	$b = ( $a == $time1 ) ? $time2 : $time1;          // lower
	$datediff = $a - $b;

	$oneday = 86400;
	$days = array();
	
	for ( $i = 0; $i <= $datediff; $i += $oneday ) {
		$days[] = date( 'c', strtotime( $dates[0] ) + $i );
	}
	
	return implode( ',', $days );
}