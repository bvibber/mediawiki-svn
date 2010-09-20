<?php
/**
 * Vector extension
 * 
 * @file
 * @ingroup Extensions
 * 
 * @author Trevor Parscal <trevor@wikimedia.org>
 * @author Roan Kattouw <roan.kattouw@gmail.com>
 * @author Nimish Gautam <nimish@wikimedia.org>
 * @author Adam Miller <amiller@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.3.0
 */

/* Configuration */

// Each module may be configured individually to be globally on/off or user preference based
$wgVectorFeatures = array(
	'collapsiblenav' => array( 'global' => true, 'user' => true ),
	'collapsibletabs' => array( 'global' => true, 'user' => false ),
	'editwarning' => array( 'global' => false, 'user' => true ),
	'expandablesearch' => array( 'global' => false, 'user' => true ),
	'footercleanup' => array( 'global' => false, 'user' => false ),
	'simplesearch' => array( 'global' => false, 'user' => true ),
);

// The Vector skin has a basic version of simple search, which is a prerequisite for the enhanced one
$wgDefaultUserOptions['vector-simplesearch'] = 1;

// Enable bucket testing for new version of collapsible nav
$wgCollapsibleNavBucketTest = false;
// Force the new version
$wgCollapsibleNavForceNewVersion = false;

/* Setup */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Vector',
	'author' => array( 'Trevor Parscal', 'Roan Kattouw', 'Nimish Gautam', 'Adam Miller' ),
	'version' => '0.3.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Vector',
	'descriptionmsg' => 'vector-desc',
);
$wgAutoloadClasses['VectorHooks'] = dirname( __FILE__ ) . '/Vector.hooks.php';
$wgExtensionMessagesFiles['Vector'] = dirname( __FILE__ ) . '/Vector.i18n.php';
$wgHooks['BeforePageDisplay'][] = 'VectorHooks::beforePageDisplay';
$wgHooks['GetPreferences'][] = 'VectorHooks::getPreferences';
$wgHooks['MakeGlobalVariablesScript'][] = 'VectorHooks::makeGlobalVariablesScript';
$wgHooks['ResourceLoaderRegisterModules'][] = 'VectorHooks::resourceLoaderRegisterModules';
