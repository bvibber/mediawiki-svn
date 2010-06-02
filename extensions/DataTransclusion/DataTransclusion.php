<?php
/**
 * DataTransclusion extension - shows recent changes on a wiki page.
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Daniel Kinzler for Wikimedia Deutschland
 * @copyright © 2010 Wikimedia Deutschland (Author: Daniel Kinzler)
 * @licence GNU General Public Licence 2.0 or later
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'DataTransclusion',
	'author' => 'Daniel Kinzler for Wikimedia Deutschland',
	'url' => 'http://mediawiki.org/wiki/Extension:DataTransclusion',
	'descriptionmsg' => 'datatransclusion-desc',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['DataTransclusion'] = $dir . 'DataTransclusion.i18n.php';
$wgExtensionMessagesFiles['DataTransclusionMagic'] = $dir . 'DataTransclusion.i18n.magic.php';

$wgAutoloadClasses['DataTransclusionRenderer'] = $dir. 'DataTransclusionRenderer.php';
$wgAutoloadClasses['DataTransclusionHandler'] = $dir. 'DataTransclusionHandler.php';
$wgAutoloadClasses['DataTransclusionSource'] = $dir. 'DataTransclusionSource.php';
$wgAutoloadClasses['CachingDataTransclusionSource'] = $dir. 'DataTransclusionSource.php';
$wgAutoloadClasses['FakeDataTransclusionSource'] = $dir. 'DataTransclusionSource.php';
$wgAutoloadClasses['DBDataTransclusionSource'] = $dir. 'DBDataTransclusionSource.php';
#$wgAutoloadClasses['WAPIDataTransclusionSource'] = $dir. 'WAPIDataTransclusionSource.php';

$wgHooks['ParserFirstCallInit'][] = 'efDataTransclusionSetHooks';

$wgDataTransclusionSources = array();

function efDataTransclusionSetHooks( $parser ) {
	$parser->setHook( 'record' , 'DataTransclusionHandler::handleRecordTag' );
	$parser->setFunctionHook( 'record' , 'DataTransclusionHandler::handleRecordFunction' ); 
	return true;
}
