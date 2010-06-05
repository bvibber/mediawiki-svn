<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}

$wgExtensionCredits['other'][] = array(
	'name' => 'CategoryMultisortChinese',
	'author' => 'Liangent',
	'descriptionmsg' => 'categorymultisortchinese-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:CategoryMultisortChinese',
);

$wgAutoloadClasses['CategoryMultisortChineseHooks'] = dirname( __FILE__ ) . '/CategoryMultisortChinese.hooks.php';
$wgAutoloadClasses['CategoryMultisortChineseData'] = dirname( __FILE__ ) . '/CategoryMultisortChinese.data.php';

$wgExtensionMessagesFiles['CategoryMultisortChinese'] = dirname( __FILE__ ) . '/CategoryMultisortChinese.i18n.php';

$wgExtensionFunctions[] = 'efCategoryMultisortChineseInit';

function efCategoryMultisortChineseInit() {
	global $wgContLang, $wgCategoryMultisortSortkeyNames;
	
	wfLoadExtensionMessages( 'CategoryMultisortChinese' );
	
	if ( in_array( 'zh-hans', $wgContLang->getVariants() ) ) {
		$wgCategoryMultisortSortkeyNames['stroke-s'] = $wgCategoryMultisortSortkeyNames['stroke'];
		$wgCategoryMultisortSortkeyNames['radical-s'] = $wgCategoryMultisortSortkeyNames['radical'];
	}
	
	if ( in_array( 'zh-hant', $wgContLang->getVariants() ) ) {
		$wgCategoryMultisortSortkeyNames['stroke-t'] = $wgCategoryMultisortSortkeyNames['stroke'];
		$wgCategoryMultisortSortkeyNames['radical-t'] = $wgCategoryMultisortSortkeyNames['radical'];
	}
}

new CategoryMultisortChineseHooks();

$wgCategoryMultisortSortkeyNames['mandarin'] = array( 'firstChar' => true );
$wgCategoryMultisortSortkeyNames['stroke'] = array( 'firstChar' => false );
$wgCategoryMultisortSortkeyNames['radical'] = array( 'firstChar' => true );
