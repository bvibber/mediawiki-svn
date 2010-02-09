<?php
/* 
	The RefHelper extension is free software: you can redistribute it 
	and/or modify it under the terms of the GNU General Public License 
	as published by the Free Software Foundation, either version 3 of 
	the License, or (at your option) any later version.

    This program is distributed WITHOUT ANY WARRANTY. See 
	http://www.gnu.org/licenses/#GPL for more details.
*/

if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/RefHelper/RefHelper.php" );
EOT;
        exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'RefHelper',
	'author' => 'Jonathan Williford',
	'url' => 'http://neurov.is/on',
	'description' => 'This extension helps create pages for references.',
	'descriptionmsg' => 'This extension helps create pages for references.',
	'version' => '0.0.1',
);

global $wgHooks;

$wgHooks['SkinTemplateToolboxEnd'][] = 'RefHelperHooks::addRefHelperLink';
$wgHooks['BeforePageDisplay'][] = 'RefHelperHooks::addRefHelperJavascript';

 
 
$dir = dirname(__FILE__) . '/';
 
$wgAutoloadClasses['RefHelperHooks'] = $dir . 'RefHelper.hooks.php';
$wgAutoloadClasses['RefHelper'] = $dir . 'RefHelper.create.php';
$wgAutoloadClasses['RefSearch'] = $dir . 'RefHelper.search.php';
$wgExtensionMessagesFiles['RefHelper'] = $dir . 'RefHelper.i18n.php';
$wgExtensionAliasesFiles['RefHelper'] = $dir . 'RefHelper.alias.php';
$wgSpecialPages['RefHelper'] = 'RefHelper';
$wgSpecialPages['RefSearch'] = 'RefSearch';
