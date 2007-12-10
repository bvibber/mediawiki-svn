<?php

$wgExtensionFunctions[] = 'wfSetupImageMap';
$wgAutoloadClasses['ImageMap'] = dirname(__FILE__).'/ImageMap_body.php';
$wgExtensionCredits['parserhook']['ImageMap'] = array(
	'name' => 'ImageMap',
	'version' => '1.1',
	'author' => 'Tim Starling',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ImageMap',
	'description' => 'Allows client-side clickable image maps using <nowiki><imagemap></nowiki> tag',
);

function wfSetupImageMap() {
	global $wgParser;
	$wgParser->setHook( 'imagemap', array( 'ImageMap', 'render' ) );
}
