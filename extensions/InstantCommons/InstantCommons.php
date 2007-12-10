<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
<html><body>
<p>This is the InstantCommons extension for MediaWiki. To enable it, put the following 
at the end of your LocalSettings.php:</p>
<pre>
require_once( "\$IP/extensions/InstantCommons/InstantCommons.php" );
</pre>
</body></html>
EOT;
	exit;
}

$wgExtensionCredits['other'][] = array(
	'name' => 'InstantCommons',
	'version' => '0.5',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InstantCommons',
	'description' => 'Enable use of Wikimedia Commons as media source',
	'author' => 'Suuch',
);

$wgAutoloadClasses['ApiInstantCommons'] = dirname( __FILE__ ) . '/InstantCommons_body.php';
$wgAPIModules['instantcommons'] = 'ApiInstantCommons';
