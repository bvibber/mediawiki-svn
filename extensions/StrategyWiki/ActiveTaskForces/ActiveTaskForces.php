<?php

/**
 * Period for edit counts, in seconds
 */
$wgActiveTaskForcesPeriod = 7 * 86400;


/* Registration */
$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'ActiveTaskForces',
	'author'         => 'Tim Starling',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:ActiveTaskForces',
	'descriptionmsg' => 'active-task-forces-desc',
);

$dir = dirname( __FILE__ ) . '/';
$wgHooks['wgQueryPages'][] = 'wfActiveTaskForcesRegister';
$wgSpecialPages['ActiveTaskForces'] = 'ActiveTaskForcesSP';
$wgSpecialPageGroups['ActiveTaskForces'] = 'wiki';
$wgExtensionMessagesFiles['ActiveTaskForces'] = $dir . 'ActiveTaskForces.i18n.php';
$wgAutoloadClasses['ActiveTaskForcesSP'] 
	= $wgAutoloadClasses['ActiveTaskForcesQP']
	= $dir . 'ActiveTaskForces_body.php';

function wfActiveTaskForcesRegister( &$pages ) {
	$pages[] = array( 'ActiveTaskForcesQP', 'ActiveTaskForces' );
	return true;
}
