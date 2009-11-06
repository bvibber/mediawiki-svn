<?php
/**
 * Usability Initiative SimpleSearch extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the SimpleSearch portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * This extension requires $wgVectorUseSimpleSearch = true; (false by default)
 * and $wgEnableOpenSearchSuggest = true; (default)
 * and recommends $wgEnableMWSuggest = false; (default)
 * 
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/SimpleSearch/SimpleSearch.php" );
 *
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Bump the version number every time you change any of the .css/.js files
$wgSimpleSearchStyleVersion = 3;

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'SimpleSearch',
	'author' => 'Trevor Parscal',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:SimpleSearch',
	'descriptionmsg' => 'simplesearch-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$wgAutoloadClasses['SimpleSearchHooks'] =
	dirname( __FILE__ ) . '/SimpleSearch.hooks.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['SimpleSearch'] =
	dirname( __FILE__ ) . '/SimpleSearch.i18n.php';

// Registers Hooks
$wgHooks['AjaxAddScript'][] = 'SimpleSearchHooks::initialize';