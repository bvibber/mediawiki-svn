<?php

define( 'MW_NO_DB', 1 );
define( 'MW_CONFIG_CALLBACK', 'wfInstallerConfig' );

function wfInstallerConfig() {
	$GLOBALS['wgUseDatabaseMessages'] = false;
	$GLOBALS['wgLBFactoryConf'] = array( 'class' => 'LBFactory_InstallerFake' );
	$GLOBALS['wgShowExceptionDetails'] = true;
}

chdir( ".." );
require( './includes/WebStart.php' );

$installer = new WebInstaller( $wgRequest );

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

$session = $installer->execute( $session );
$_SESSION['installData'] = $session;

