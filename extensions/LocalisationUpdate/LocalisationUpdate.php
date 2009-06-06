<?php
/*
KNOWN ISSUES:
- Only works with SVN revision 50605 or later of the
  Mediawiki core
*/

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
$wgHooks['MessageNotInMwNs'][] = "LocalisationUpdate::FindUpdatedMessage";

$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['LocalisationUpdate'] = $dir . 'LocalisationUpdate.i18n.php';
$wgAutoloadClasses['LocalisationUpdate'] = $dir . 'LocalisationUpdate.class.php';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'LocalisationUpdate::schemaUpdates';

$wgLocalisationUpdateRetryAttempts = 5;