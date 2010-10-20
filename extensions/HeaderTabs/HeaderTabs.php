<?php
/**
 * Header Tabs extension
 *
 * @file
 * @ingroup Extensions
 *
 * @author Sergey Chernyshev
 * @author Yaron Koren
 */

$htScriptPath = $wgScriptPath . '/extensions/HeaderTabs';

$dir = dirname( __FILE__ );
// the file loaded depends on whether the ResourceLoader exists, which in
// turn depends on what version of MediaWiki this is - for MW 1.17+,
// HeaderTabs_body.jq.php will get loaded
if ( method_exists( 'OutputPage', 'addModules' ) ) {
	$wgAutoloadClasses['HeaderTabs'] = "$dir/HeaderTabs_body.jq.php";
} else {
	$wgAutoloadClasses['HeaderTabs'] = "$dir/HeaderTabs_body.yui.php";
}

$wgExtensionFunctions[] = 'htSetupExtension';
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'Header Tabs',
	'description' => 'Adds tabs to the page separating top-level sections.',
	'version' => '0.8',
	'author' => array( '[http://www.sergeychernyshev.com Sergey Chernyshev]', 'Yaron Koren' ),
	'url' => 'http://www.mediawiki.org/wiki/Extension:Header_Tabs'
);

$htUseHistory = true;

function htSetupExtension() {
	global $wgHooks;
	global $wgParser;

	$wgHooks['BeforePageDisplay'][] = 'HeaderTabs::addHTMLHeader';
	$wgHooks['ParserAfterTidy'][] = 'HeaderTabs::replaceFirstLevelHeaders';
	$wgParser->setHook( 'headertabs', array( 'HeaderTabs', 'tag' ) );

	return true;
}

# Parser function to insert a link changing a tab:
$wgExtensionFunctions[] = 'headerTabsParserFunctions';
$wgHooks['LanguageGetMagic'][] = 'headerTabsLanguageGetMagic';

function headerTabsParserFunctions() {
    global $wgParser;
    $wgParser->setFunctionHook( 'switchtablink', array( 'HeaderTabs', 'renderSwitchTabLink' ) );
}

function headerTabsLanguageGetMagic( &$magicWords, $langCode = "en" ) {
	switch ( $langCode ) {
	default:
		$magicWords['switchtablink']	= array ( 0, 'switchtablink' );
	}
	return true;
}
