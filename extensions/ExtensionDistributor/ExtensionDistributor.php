<?php

/**
 * This is an extension for distributing snapshot archives of extensions, 
 * to be run on mediawiki.org
 */


/********************
 * Configuration
 */

/** Directory to put tar files in */
$wgExtDistTarDir = false;

/** URL corresponding to $wgExtDistTarDir */
$wgExtDistTarUrl = false;

/** Subversion /mediawiki working copy */
$wgExtDistWorkingCopy = false;

/** Supported branches, the first one is the default */
$wgExtDistBranches = array(
	'trunk' => array(
		'tarLabel' => 'trunk',
		'msgName' => 'extdist-current-version',
	),
	'branches/REL1_12' => array(
		'tarLabel' => 'MW1.12',
		'name' => '1.12.x',
	),
	'branches/REL1_11' => array(
		'tarLabel' => 'MW1.11',
		'name' => '1.11.x',
	),
	'branches/REL1_10' => array(
		'tarLabel' => 'MW1.12',
		'name' => '1.10.x',
	),
);

/********************
 * Registration
 */
$wgSpecialPages['ExtensionDistributor'] = 'ExtensionDistributorPage';
$wgAutoloadClasses['ExtensionDistributorPage'] = dirname(__FILE__).'/ExtensionDistributor_body.php';
$wgExtensionMessagesFiles['ExtensionDistributor'] = dirname(__FILE__).'/ExtensionDistributor.i18n.php';

