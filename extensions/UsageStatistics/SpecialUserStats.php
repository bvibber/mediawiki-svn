<?php
if (!defined('MEDIAWIKI')) die();
 
$wgExtensionCredits['specialpage'][] = array(
        'name'        => 'UserStats',
        'version'     => 'v1.2',
        'author'      => 'Paul Grinberg',
        'email'       => 'gri6507 at yahoo dot com',
        'url'         => 'http://www.mediawiki.org/wiki/Extension:Usage_Statistics',
        'description' => 'Show individual user and overall wiki usage statistics'
);
 
$wgAutoloadClasses['SpecialUserStats'] = dirname(__FILE__) . '/SpecialUserStats_body.php';
$wgSpecialPages['SpecialUserStats'] = 'SpecialUserStats';
$wgHooks['LoadAllMessages'][] = 'SpecialUserStats::loadMessages';

