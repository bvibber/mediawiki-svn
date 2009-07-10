<?php
if ( ! defined( 'MEDIAWIKI' ) )
	die();
/**
 * SlippyMap extension
 *
 * @file
 * @ingroup Extension
 *
 * This file contains the main include file for the SlippyMap
 * extension of MediaWiki.
 *
 * Usage: Add the following line in LocalSettings.php:
 * require_once( "$IP/extensions/SlippyMap/SlippyMap.php" );
 *
 * See the SlippyMap documenation on mediawiki.org for further usage
 * information.
 *
 * @link http://www.mediawiki.org/wiki/Extension:SlippyMap Documentation
 *
 * Copyright 2008 Harry Wood, Jens Frank, Grant Slater, Raymond Spekking and others
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
*/

if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	$wgHooks['ParserFirstCallInit'][] = 'SlippyMapHooks::onParserFirstCallInit';
} else {
	$wgExtensionFunctions[] = 'SlippyMapHooks::onParserFirstCallInit';
}

$wgExtensionCredits['parserhook'][] = array(
	'path'				=> __FILE__,
	'name'				=> 'Slippy Map',
	'author'			=> array('[http://harrywood.co.uk Harry Wood]', 'Jens Frank', 'Aude', 'Ævar Arnfjörð Bjarmason'),
	'url'				=> 'http://www.mediawiki.org/wiki/Extension:SlippyMap',
	'description'		=> 'Adds a &lt;slippymap&gt; which allows for embedding of static & dynamic maps.Supports multiple map services including [http://openstreetmap.org OpenStreetMap] and NASA Worldwind',
	'descriptionmsg'	=> 'slippymap_desc',
);

/* Shortcut to this extension directory */
$dir = dirname( __FILE__ ) . '/';

/* i18n messages */
$wgExtensionMessagesFiles['SlippyMap']	= $dir . 'SlippyMap.i18n.php';

/* The classes which make up our extension*/
$wgAutoloadClasses['SlippyMapHooks']	= $dir . 'SlippyMap.hooks.php';
$wgAutoloadClasses['SlippyMap']			= $dir . 'SlippyMap.class.php';
$wgAutoloadClasses['WorldWind']			= $dir . 'SlippyMap.worldwind.php';

/* Parser tests */
$wgParserTestFiles[]                    = $dir . '/slippyMapParserTests.txt';

/*
 * Configuration variables
 */

/* Allowed mode= values on this server */
$wgMapModes = array( 'osm', 'satellite' );

/*
 * If true the a JS slippy map will be shown by default to supporting
 * clients, otherwise they'd have to click on the static image to
 * enable the slippy map.
 */
$wgAutoLoadMaps = false;
