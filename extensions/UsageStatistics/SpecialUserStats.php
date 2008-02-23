<?php
if (!defined('MEDIAWIKI')) die();

$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'UserStats',
	'version'        => '1.4',
	'author'         => 'Paul Grinberg',
	'email'          => 'gri6507 at yahoo dot com',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Usage_Statistics',
	'description'    => 'Show individual user and overall wiki usage statistics',
	'descriptionmsg' => 'usagestatistics-desc',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['UserStats'] = $dir . 'SpecialUserStats.i18n.php';
$wgAutoloadClasses['SpecialUserStats'] = $dir . 'SpecialUserStats_body.php';
$wgSpecialPages['SpecialUserStats'] = 'SpecialUserStats';
