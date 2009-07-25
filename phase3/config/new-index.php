<?php

define( 'MW_NO_DB', 1 );
define( 'MW_CONFIG_CALLBACK', 'wfInstallerConfig' );

function wfInstallerConfig() {
	// Don't access the database
	$GLOBALS['wgUseDatabaseMessages'] = false;
	$GLOBALS['wgLBFactoryConf'] = array( 'class' => 'LBFactory_InstallerFake' );
	// Debug-friendly
	$GLOBALS['wgShowExceptionDetails'] = true;
	// Don't break forms
	$GLOBALS['wgExternalLinkTarget'] = '_blank';
}

chdir( ".." );
require( './includes/WebStart.php' );

// Disable the i18n cache
Language::getLocalisationCache()->disableBackend();

$installer = new WebInstaller( $wgRequest );
$wgParser->setHook( 'doclink', array( $installer, 'docLink' ) );

if ( !$installer->startSession() ) {
	$installer->finish();
	exit;
}

$session = isset( $_SESSION['installData'] ) ? $_SESSION['installData'] : array();

if ( isset( $session['settings']['_UserLang'] ) ) {
	$langCode = $session['settings']['_UserLang'];
} elseif ( !is_null( $wgRequest->getVal( 'UserLang' ) ) ) {
	$langCode = $wgRequest->getVal( 'UserLang' );
} else {
	$langCode = 'en';
}
$wgLang = Language::factory( $langCode );

$wgMetaNamspace = $wgCanonicalNamespaceNames[NS_PROJECT];

$session = $installer->execute( $session );

$_SESSION['installData'] = $session;

