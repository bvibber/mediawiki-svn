<?php
/********************************************************
*                                                       *
*   Copyright (C) Microsoft. All rights reserved.       *
*                                                       *
********************************************************/

/*
Copyright (c) 2010, Microsoft 
All rights reserved.
*/

// WikiBhasha launch Extention script.
// Description: this script eases the procedure to launch WikiBhasha by instrumenting
// wikipedia pages with links and options to launch the application. It executes the
// same routine as the bookmarklet and is portable across Wikipedia installations.
//
// The current options to launch WikiBhasha are:
// 1. Looks for "action=edit" in the URL and check if toolbar exists. if present adds a
//    icon to the toolbar for launching WikiBhasha.
// 2. inserts a "WikiBhasha" option in the left side toolbox menu.
// 3. Looks for "wbAutoLaunch=true" in the URL and launch WikiBhasha.

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'WikiBhasha',
	'author' => 'Microsoft Researh',
	'url' => 'http://www.mediawiki.org/wiki/Extension:WikiBhasha',
	'description' => 'Default description message',
	'descriptionmsg' => 'wikibhasha-desc',
	'version' => '1.0',
);

//static Paths
$dir = dirname(__FILE__) . '/';
$ExtDir = str_replace( "\\", "/", $dir );
$top_dir = explode( '/', $ExtDir );
echo $top_dir = array_pop( $top_dir );
$ScriptPath = $wgScriptPath . '/extensions' . ( ( $top_dir == 'extensions' ) ? '' : '/' . $top_dir ); 
$jsPath = "extensions/WikiBhasha/src/";

//add a special page 
$wgSpecialPages['wikiBhasha'] = 'WikiBhasha';
$wgSpecialPageGroups['wikiBhasha'] = 'wiki';

// Autoloadable classes
$wgAutoloadClasses['wikiBhashaExt'] = $dir . 'wikiBhashaExtClass.php'; 
$wgAutoloadClasses['wikiBhasha'] = $dir . 'wikiBhashaSpecial.php';

//initilize wikiBhasha launch class
$wbExtClass = new wikiBhashaExt();

$wgAutoloadClasses['wikibhasha'] = $dir . 'wikibhasha_body.php'; # Location of the wikibhasha class (Tell MediaWiki to load this file)
$wgExtensionMessagesFiles['wikibhasha'] = $dir . 'wikibhasha.i18n.php'; # Location of a messages file (Tell MediaWiki to load this file)
$wgHooks['MonoBookTemplateToolboxEnd'][] = array( $wbExtClass, 'wikiBhashaToolbox' ); 
$wgHooks['BeforePageDisplay'][] = array( $wbExtClass, 'wbToolbarIcon' );

?>