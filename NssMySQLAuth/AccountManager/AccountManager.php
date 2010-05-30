<?php
$dir = dirname( __FILE__ ) . '/';

$wgExtensionMessagesFiles['accountmanager'] = $dir . 'AccountManager.i18n.php';
$wgExtensionAliasesFiles['accountmanager'] = $dir . 'AccountManager.alias.php';

$wgAutoloadClasses['NssUser'] = $dir . 'NssUser.php';
$wgAutoloadClasses['NssGroup'] = $dir . 'NssGroup.php';
$wgAutoloadClasses['NssProperties'] = $dir . 'NssProperties.php';

$wgAutoloadClasses['SpecialAccountManager'] = $dir . 'SpecialAccountManager.php';
$wgAutoloadClasses['AmUserListView'] = $dir . 'AmUserListView.php';
$wgAutoloadClasses['AmUserView'] = $dir . 'AmUserView.php';
$wgAutoloadClasses['AmExport'] = $dir . 'AmExport.php';

$wgSpecialPages['AccountManager'] = 'SpecialAccountManager';

$wgUserProperties = array( 'address', 'city' );
