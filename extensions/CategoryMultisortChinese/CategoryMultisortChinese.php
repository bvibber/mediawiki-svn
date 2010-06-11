<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'CategoryMultisortChinese',
	'author' => 'Liangent',
	'descriptionmsg' => 'categorymultisortchinese-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:CategoryMultisortChinese',
);

$dir = dirname(__FILE__) . '/';
$wgAutoloadClasses['CategoryMultisortChineseHooks'] = $dir . 'CategoryMultisortChinese.hooks.php';
$wgAutoloadClasses['CategoryMultisortChineseData'] = $dir . 'CategoryMultisortChinese.data.php';

$wgExtensionMessagesFiles['CategoryMultisortChinese'] = $dir . 'CategoryMultisortChinese.i18n.php';

$wgExtensionFunctions[] = 'efCategoryMultisortChineseInit';

function efCategoryMultisortChineseInit() {
	global $wgContLang, $wgCategoryMultisortSortkeySettings;
	
	wfLoadExtensionMessages( 'CategoryMultisortChinese' );
	
	if ( in_array( 'zh-hans', $wgContLang->getVariants() ) ) {
		$wgCategoryMultisortSortkeySettings['stroke-s'] = $wgCategoryMultisortSortkeySettings['stroke'];
		$wgCategoryMultisortSortkeySettings['radical-s'] = $wgCategoryMultisortSortkeySettings['radical'];
	}
	
	if ( in_array( 'zh-hant', $wgContLang->getVariants() ) ) {
		$wgCategoryMultisortSortkeySettings['stroke-t'] = $wgCategoryMultisortSortkeySettings['stroke'];
		$wgCategoryMultisortSortkeySettings['radical-t'] = $wgCategoryMultisortSortkeySettings['radical'];
	}
}

new CategoryMultisortChineseHooks();

$wgCategoryMultisortSortkeySettings['mandarin'] = array();
$wgCategoryMultisortSortkeySettings['cantonese'] = array();
$wgCategoryMultisortSortkeySettings['stroke'] = array( 'first' => 3, 'type' => 'int' );
$wgCategoryMultisortSortkeySettings['radical'] = array();
