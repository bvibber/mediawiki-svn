<?php
/**
 * Initialization file for SemanticInternalObjects
 *
 * @file
 * @ingroup SemanticInternalObjects
 * @author Yaron Koren
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

define( 'SIO_VERSION', '0.1' );

$wgExtensionCredits['parserhook'][] = array(
	'name'	=> 'Semantic Internal Objects',
	'version'	=> SIO_VERSION,
	'author'	=> 'Yaron Koren',
	'url'	=> 'http://www.mediawiki.org/wiki/Extension:Semantic_Internal_Objects',
	'description'	=>  'Setting of internal objects in Semantic MediaWiki',
	'descriptionmsg' => 'semanticinternalobjects-desc',
);

$wgExtensionFunctions[] = 'siofParserFunctions';
$wgHooks['LanguageGetMagic'][] = 'siofLanguageGetMagic';
$wgHooks['smwDeleteSemanticData'][] = 'SIOHandler::updateData';

$siogIP = $IP . '/extensions/SemanticInternalObjects';
$wgExtensionMessagesFiles['SemanticInternalObjects'] = $siogIP . '/SemanticInternalObjects.i18n.php';
$wgAutoloadClasses['SIOHandler'] = $siogIP . '/SemanticInternalObjects_body.php';

function siofParserFunctions() {
	global $wgHooks, $wgParser;
	if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
		$wgHooks['ParserFirstCallInit'][] = 'siofRegisterParserFunctions';
	} else {
		if ( class_exists( 'StubObject' ) && !StubObject::isRealObject( $wgParser ) ) {
			$wgParser->_unstub();
		}
		siofRegisterParserFunctions( $wgParser );
	}
}

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
