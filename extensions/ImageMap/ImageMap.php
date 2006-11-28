<?php

$wgExtensionFunctions[] = 'wfSetupImageMap';
$wgAutoloadClasses['ImageMap'] = dirname(__FILE__).'/ImageMap_body.php';
$wgExtensionCredits['parserhook']['ImageMap'] = array(
	'name' => 'ImageMap',
	'author' => 'Tim Starling',
);

function wfSetupImageMap() {
	global $wgParser;
	$wgParser->setHook( 'imagemap', array( 'ImageMap', 'render' ) );
}

?>
