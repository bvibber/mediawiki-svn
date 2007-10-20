<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the extension file directly.
if (!defined('MEDIAWIKI')) {
		echo <<<EOT
To install the ChangeAuthor extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/ChangeAuthor/ChangeAuthor.setup.php" );
EOT;
		exit(1);
}

$wgExtensionCredits['specialpage'][] = array(
		'name' => 'ChangeAuthor',
		'author' => 'Roan Kattouw',
		'url' => 'http://www.mediawiki.org/wiki/Extension:ChangeAuthor',
		'version' => '1.0',
		'description' => 'Allows changing a revision\'s author afterwards'
);

$wgAutoloadClasses['ChangeAuthor'] = dirname(__FILE__) . '/ChangeAuthor.body.php';
$wgSpecialPages['ChangeAuthor'] = 'ChangeAuthor';
$wgHooks['LoadAllMessages'][] = 'ChangeAuthor::loadMessages';
$wgHooks['LanguageGetSpecialPageAliases'][] = 'ChangeAuthorLocalizedPageName';

$wgLogTypes[] = 'changeauth';
$wgLogNames['changeauth'] = 'changeauthor-logpagename';
$wgLogHeaders['changeauth'] = 'changeauthor-logpagetext';
$wgLogActions['changeauth/changeauth'] = 'changeauthor-logentry';

function ChangeAuthorLocalizedPageName(&$specialPageArray, $code)
{
		ChangeAuthor::loadMessages();
		$text = wfMsg('changeauthor-short');

		$title = Title::newFromText($text);
		$specialPageArray['ChangeAuthor'][] = $title->getDBKey();
		return true;
}
