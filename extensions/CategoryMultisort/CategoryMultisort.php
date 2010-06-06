<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}

$wgExtensionCredits['other'][] = array(
	'name' => 'CategoryMultisort',
	'author' => 'Liangent',
	'descriptionmsg' => 'categorymultisort-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:CategoryMultisort',
);

$wgAutoloadClasses['CategoryMultisortHooks'] = dirname( __FILE__ ) . '/CategoryMultisort.hooks.php';
$wgAutoloadClasses['CategoryMultisortViewer'] = dirname( __FILE__ ) . '/CategoryMultisort.class.php';

$wgExtensionMessagesFiles['CategoryMultisort'] = dirname( __FILE__ ) . '/CategoryMultisort.i18n.php';

$wgCategoryMultisortHooks = new CategoryMultisortHooks();

$wgDefaultUserOptions['categorymultisort-sortkey'] = '';

$wgCategoryMultisortSortkeySettings = array();

function efCategoryMultisortIntegrate( $integrate = true ) {
	global $wgParserConf, $wgCategoryMultisortHooks;
	
	static $defaultParser = null;
	if ( is_null( $defaultParser ) ) {
		$defaultParser = $wgParserConf['class'];
	}
	
	$wgParserConf['class'] = $integrate ? 'Parser_LinkHooks' : $defaultParser;
	$wgCategoryMultisortHooks->setIntegrate( $integrate );
}
