<?php
/**
 * Initialization file for SemanticInternalObjects
 *
 * @file
 * @ingroup SemanticInternalObjects
 * @author Yaron Koren
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

define( 'SIO_VERSION', '0.6' );

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name'	=> 'Semantic Internal Objects',
	'version'	=> SIO_VERSION,
	'author'	=> 'Yaron Koren',
	'url'	=> 'http://www.mediawiki.org/wiki/Extension:Semantic_Internal_Objects',
	'descriptionmsg' => 'semanticinternalobjects-desc',
);

$wgHooks['ParserFirstCallInit'][] = 'siofRegisterParserFunctions';
$wgHooks['LanguageGetMagic'][] = 'siofLanguageGetMagic';
$wgHooks['ParserClearState'][] = 'SIOHandler::clearState';
$wgHooks['smwDeleteSemanticData'][] = 'SIOHandler::updateData';
$wgHooks['smwUpdatePropertySubjects'][] = 'SIOHandler::handleUpdatingOfInternalObjects';
$wgHooks['smwRefreshDataJobs'][] = 'SIOHandler::handleRefreshingOfInternalObjects';
$wgHooks['smwAddToRDFExport'][] = 'SIOSQLStore::createRDF';

$siogIP = dirname( __FILE__ );
$wgExtensionMessagesFiles['SemanticInternalObjects'] = $siogIP . '/SemanticInternalObjects.i18n.php';
$wgAutoloadClasses['SIOHandler'] = $siogIP . '/SemanticInternalObjects_body.php';
$wgAutoloadClasses['SIOSQLStore'] = $siogIP . '/SemanticInternalObjects_body.php';

function siofRegisterParserFunctions( &$parser ) {
	$parser->setFunctionHook( 'set_internal', array( 'SIOHandler', 'doSetInternal' ) );
	return true; // always return true, in order not to stop MW's hook processing!
}

function siofLanguageGetMagic( &$magicWords, $langCode = "en" ) {
	switch ( $langCode ) {
	default:
		$magicWords['set_internal'] = array ( 0, 'set_internal' );
	}
	return true;
}
