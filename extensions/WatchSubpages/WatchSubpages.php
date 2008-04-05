<?php
# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/WatchSubpages/WatchSubpages.php" );
EOT;
        exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'author' => '[http://www.strategywiki.org/wiki/User:Prod User:Prod]',
	'name' => 'Watch Guide Subpages',
	'url' => 'http://www.strategywiki.org/wiki/User:Prod',
	'description' => 'Quickly add all subpages of a guide to the users watchlist'
);

$wgAutoloadClasses['WatchSubpages'] = dirname(__FILE__) . '/WatchSubpages_body.php';
$wgSpecialPages['WatchSubpages'] = 'WatchSubpages';