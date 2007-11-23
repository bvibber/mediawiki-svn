<?php

$wgExtensionCredits['specialpage'][] = array(
	'version'     => '0.2',
	'name'        => 'WhoIsWatching',
	'author'      => 'Paul Grinberg, Siebrand Mazeland',
	'email'       => 'gri6507 at yahoo dot com',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:WhoIsWatching',
	'description' => 'Provides a listing of usernames watching a wiki page'
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['SpecialWhoIsWatching'] = $dir . 'SpecialWhoIsWatching.i18n.php';
$wgAutoloadClasses['SpecialWhoIsWatching'] = $dir . 'SpecialWhoIsWatching_body.php';
$wgSpecialPages['SpecialWhoIsWatching'] = 'SpecialWhoIsWatching';
