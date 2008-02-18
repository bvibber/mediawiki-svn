<?php
/**
 * Extension for web-based editing of large numbers of Messages*.php files.
 *
 * This extension is insecure. Do not enable it on a public wiki. It provides 
 * unauthenticated write access to the PHP files in your MediaWiki installation. 
 */


$wgSpecialPages['EditMessages'] = 'EditMessagesPage';
$wgExtensionMessagesFiles['EditMessages'] = dirname(__FILE__) . '/EditMessages.i18n.php';
$wgAutoloadClasses['EditMessagesPage'] = dirname(__FILE__).'/EditMessages_body.php';
