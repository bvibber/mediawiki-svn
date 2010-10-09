<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
    echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/RDFIO/specials/SpecialARC2Admin.php" );
EOT;
    exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'SpecialARC2Admin',
	'author' => 'Samuel Lampa',
	'url' => 'http://www.mediawiki.org/wiki/Extension:SMWRDFConnector',
	'descriptionmsg' => 'rdfio-arc2admin-desc',
	'version' => '0.0.0',
);

$dir = dirname( __FILE__ ) . '/';

$wgAutoloadClasses['SpecialARC2Admin'] = $dir . 'SpecialARC2Admin_body.php'; # Tell MediaWiki to load the extension body.
$wgExtensionMessagesFiles['SpecialARC2Admin'] = $dir . 'SpecialARC2Admin.i18n.php';
$wgExtensionAliasesFiles['SpecialARC2Admin'] = $dir . 'SpecialARC2Admin.alias.php';
$wgSpecialPages['SpecialARC2Admin'] = 'SpecialARC2Admin'; # Let MediaWiki know about your new special page.
