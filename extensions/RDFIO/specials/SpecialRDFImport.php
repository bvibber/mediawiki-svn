<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
    echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/SMWRDFConnector/SpecialRDFImport.php" );
EOT;
    exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
    'name' => 'RDFImport',
    'author' => 'Samuel Lampa',
    'url' => 'http://www.mediawiki.org/wiki/Extension:SMWRDFConnector',
    'description' => 'RDF Import',
    'descriptionmsg' => 'rdfimport-desc',
    'version' => '0.0.0',
);

$dir = dirname(__FILE__) . '/';

$wgAutoloadClasses['RDFImport'] = $dir . 'SpecialRDFImport_body.php'; # Tell MediaWiki to load the extension body.
$wgExtensionMessagesFiles['RDFImport'] = $dir . 'SpecialRDFImport.i18n.php';
$wgExtensionAliasesFiles['RDFImport'] = $dir . 'SpecialRDFImport.alias.php';
$wgSpecialPages['RDFImport'] = 'RDFImport'; # Let MediaWiki know about your new special page.
