<?php

$wgExtensionCredits['specialpage'][] = array(
        'version'     => '0.1',
        'name'        => 'WhoIsWatching',
        'author'      => 'Paul Grinberg',
        'email'       => 'gri6507 at yahoo dot com',
        'url'         => 'http://www.mediawiki.org/wiki/Extension:WhoIsWatching',
        'description' => 'Provides a listing of usernames watching a wiki page'
);

$wgAutoloadClasses['SpecialWhoIsWatching'] = dirname(__FILE__) . '/SpecialWhoIsWatching_body.php';
$wgSpecialPages['SpecialWhoIsWatching'] = 'SpecialWhoIsWatching';
$wgHooks['LoadAllMessages'][] = 'SpecialWhoIsWatching::loadMessages';
