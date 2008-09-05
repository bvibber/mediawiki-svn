<?php

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the skin file directly.
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/WhoIsWatching/SpecialWhoIsWatching.php" );
EOT;
        exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
    'version'        => '0.7',
    'name'           => 'WhoIsWatching',
    'author'         => 'Paul Grinberg, Siebrand Mazeland',
    'email'          => 'gri6507 at yahoo dot com',
    'url'            => 'http://www.mediawiki.org/wiki/Extension:WhoIsWatching',
    'description'    => 'Provides a listing of usernames watching a wiki page',
    'descriptionmsg' => 'whoiswatching-desc',
);

$wgAutoloadClasses['WhoIsWatching'] = dirname(__FILE__) . '/SpecialWhoIsWatching_body.php';
$wgExtensionMessagesFiles['WhoIsWatching'] = $dir . 'WhoIsWatching.i18n.php';
$wgExtensionAliasesFiles['WhoIsWatching'] = $dir . 'SpecialWhoIsWatching.alias.php';
$wgSpecialPages['WhoIsWatching'] = 'WhoIsWatching';

# Set the following to either 'UserName' or 'RealName' to display the list of watching users as such.
$whoiswatching_nametype = 'RealName';

# Set the following to either True or False to optionally allow users to add others to watch a particular page
$whoiswatching_allowaddingpeople = true;
