<?php
$dir = dirname( __FILE__ ) . '/';

$wgExtensionMessagesFiles['AccountManager'] = $dir . 'AccountManager.i18n.php';
$wgExtensionAliasesFiles['AccountManager'] = $dir . 'AccountManager.alias.php';

$wgAutoloadClasses['SpecialAccountManager'] = $dir . 'SpecialAccountManager.php';
$wgSpecialPages['AccountManager'] = 'SpecialAccountManager';

$wgUserProperties = array( 'address', 'city' );
$wgActivityModes = array( 'active', 'inactive' );
