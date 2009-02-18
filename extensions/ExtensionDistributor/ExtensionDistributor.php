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

/** 
 * Supported branches, the first one is the default.
 * To add a branch, first check out the new branch in $wgExtDistWorkingCopy, and 
 * then add it here. Do not add a branch here without first checking it out.
 */
$wgExtDistBranches = array(
	'trunk' => array(
		'tarLabel' => 'trunk',
		'msgName' => 'extdist-current-version',
	),
	'branches/REL1_14' => array(
		'tarLabel' => 'MW1.14',
		'name' => '1.14.x',
	),
	'branches/REL1_13' => array(
		'tarLabel' => 'MW1.13',
		'name' => '1.13.x',
	),
	'branches/REL1_12' => array(
		'tarLabel' => 'MW1.12',
		'name' => '1.12.x',
	),
	'branches/REL1_11' => array(
		'tarLabel' => 'MW1.11',
		'name' => '1.11.x',
	),
);

/** Remote socket for svn-invoker.php (optional) */
$wgExtDistRemoteClient = false;

/********************
 * Registration
 */
$dir = dirname(__FILE__) . '/';
$wgSpecialPages['ExtensionDistributor'] = 'ExtensionDistributorPage';
$wgSpecialPageGroups['ExtensionDistributor'] = 'developer';
$wgAutoloadClasses['ExtensionDistributorPage'] = $dir . 'ExtensionDistributor_body.php';
$wgExtensionMessagesFiles['ExtensionDistributor'] = $dir . 'ExtensionDistributor.i18n.php';

$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'Extension Distributor',
	'author'         => 'Tim Starling',
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:ExtensionDistributor',
	'description'    => 'This is an extension for distributing snapshot archives of extensions',
	'descriptionmsg' => 'extdist-desc',
);
