<?php
$dir = dirname( __FILE__ ) . '/';

$wgExtensionMessagesFiles['AccountManager'] = $dir . 'AccountManager.i18n.php';
$wgExtensionAliasesFiles['AccountManager'] = $dir . 'AccountManager.alias.php';

$wgAutoloadClasses['NssUser'] = $dir . 'NssUser.php';
$wgAutoloadClasses['NssGroup'] = $dir . 'NssGroup.php';
$wgAutoloadClasses['NssProperties'] = $dir . 'NssProperties.php';

$wgAutoloadClasses['SpecialAccountManager'] = $dir . 'SpecialAccountManager.php';
$wgAutoloadClasses['AmUserListView'] = $dir . 'AmUserListView';
$wgAutoloadClasses['AmUserView'] = $dir . 'AmUserView';

$wgSpecialPages['AccountManager'] = 'SpecialAccountManager';

$wgUserProperties = array( 'address', 'city' );

