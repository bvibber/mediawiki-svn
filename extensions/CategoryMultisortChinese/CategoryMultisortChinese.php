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

$wgAutoloadClasses['CategoryMultisortChineseHooks'] = dirname( __FILE__ ) . '/CategoryMultisortChinese.class.php';
$wgAutoloadClasses['CategoryMultisortChineseData'] = dirname( __FILE__ ) . '/CategoryMultisortChinese.data.php';

$wgExtensionMessagesFiles['CategoryMultisortChinese'] = dirname( __FILE__ ) . '/CategoryMultisortChinese.i18n.php';

$wgExtensionFunctions[] = 'efCategoryMultisortChineseInit';

function efCategoryMultisortChineseInit() {
	wfLoadExtensionMessages( 'CategoryMultisortChinese' );
}

new CategoryMultisortChineseHooks();

$wgCategoryMultisortSortkeyNames['mandarin'] = array( 'firstChar' => true );
$wgCategoryMultisortSortkeyNames['stroke'] = array( 'firstChar' => false );
$wgCategoryMultisortSortkeyNames['radical'] = array( 'firstChar' => true );
