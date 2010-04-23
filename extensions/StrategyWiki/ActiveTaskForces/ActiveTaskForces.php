<?php

/**
 * Period for edit counts, in seconds
 */
$wgActiveTaskForcesPeriod = 7 * 86400;


/* Registration */
$wgHooks['wgQueryPages'][] = 'wfActiveTaskForcesRegister';
$wgSpecialPages['ActiveTaskForces'] = 'ActiveTaskForcesSP';
$wgExtensionMessagesFiles['ActiveTaskForces'] = dirname( __FILE__ ) .'/ActiveTaskForces.i18n.php';
$wgAutoloadClasses['ActiveTaskForcesSP'] 
	= $wgAutoloadClasses['ActiveTaskForcesQP']
	= dirname( __FILE__ ) .'/ActiveTaskForces_body.php';

function wfActiveTaskForcesRegister( &$pages ) {
	$pages[] = array( 'ActiveTaskForcesQP', 'ActiveTaskForces' );
	return true;
}
