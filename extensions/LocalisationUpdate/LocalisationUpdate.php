<?php
/*
KNOWN ISSUES:
- Only works with SVN revision 50605 or later of the
  Mediawiki core
*/

// Configuration

/**
 * This should point to either an HTTP-accessible Subversion repository containing
 * MediaWiki's 'phase3' and 'extensions' directory, *or* a local directory containing
 * checkouts of them:
 *
 * cd /path/to/mediawiki-trunk
 * svn co http://svn.wikimedia.org/svnroot/mediawiki/trunk/phase3
 * svn co http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions
 * $wgLocalisationUpdateSVNURL = '/path/to/mediawiki-trunk';
 */
$wgLocalisationUpdateSVNURL = "http://svn.wikimedia.org/svnroot/mediawiki/trunk";

$wgLocalisationUpdateRetryAttempts = 5;

// Info about me!
$wgExtensionCredits['other'][] = array(
	'path'           => __FILE__,
	'name'           => 'LocalisationUpdate',
	'author'         => array( 'Tom Maaswinkel', 'Niklas Laxström' ),
	'version'        => '0.2',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:LocalisationUpdate',
	'description'    => 'Keeps the localised messages as up to date as possible',
	'descriptionmsg' => 'localisationupdate-desc',
);

// Use the right hook
$wgHooks['LocalisationCacheRecache'][] = 'LocalisationUpdate::onRecache'; // MW 1.16+

$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['LocalisationUpdate'] = $dir . 'LocalisationUpdate.i18n.php';
$wgAutoloadClasses['LocalisationUpdate'] = $dir . 'LocalisationUpdate.class.php';
$wgAutoloadClasses['LUDependency'] = $dir . 'LocalisationUpdate.class.php';
$wgAutoloadClasses['QuickArrayReader'] = $dir . 'QuickArrayReader.php';

$wgHooks['LoadExtensionSchemaUpdates'][] = 'LocalisationUpdate::schemaUpdates';

