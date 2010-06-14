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
		$wgCategoryMultisortSortkeySettings['stroke-simplified'] = $wgCategoryMultisortSortkeySettings['stroke'];
		$wgCategoryMultisortSortkeySettings['radical-simplified'] = $wgCategoryMultisortSortkeySettings['radical'];
	}
	
	if ( in_array( 'zh-hant', $wgContLang->getVariants() ) ) {
		$wgCategoryMultisortSortkeySettings['stroke-traditional'] = $wgCategoryMultisortSortkeySettings['stroke'];
		$wgCategoryMultisortSortkeySettings['radical-traditional'] = $wgCategoryMultisortSortkeySettings['radical'];
	}
}

new CategoryMultisortChineseHooks();

$wgCategoryMultisortSortkeySettings['mandarin-pinyin'] = array();
$wgCategoryMultisortSortkeySettings['mandarin-bopomofo'] = array();
$wgCategoryMultisortSortkeySettings['mandarin-wadegiles'] = array();
$wgCategoryMultisortSortkeySettings['mandarin-mps2'] = array();
$wgCategoryMultisortSortkeySettings['mandarin-tongyong'] = array();
$wgCategoryMultisortSortkeySettings['cantonese-jyutping'] = array();
$wgCategoryMultisortSortkeySettings['stroke'] = array( 'first' => 3, 'type' => 'int' );
$wgCategoryMultisortSortkeySettings['radical'] = array();
