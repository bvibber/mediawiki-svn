<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the OggHandler extension. Please see the README file for installation instructions.\n";
	exit( 1 );
}

$oggDir = dirname(__FILE__);
$wgAutoloadClasses['OggHandler'] = "$oggDir/OggHandler_body.php";

$wgMediaHandlers['application/ogg'] = 'OggHandler';
if ( !in_array( 'ogg', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'ogg';
}
if ( !in_array( 'ogv', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'ogv';
}
if ( !in_array( 'oga', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'oga';
}
ini_set( 'include_path',
	"$oggDir/PEAR/File_Ogg" .
	PATH_SEPARATOR .
	ini_get( 'include_path' ) );

// Bump this when updating OggPlayer.js to help update caches
$wgOggScriptVersion = '10';

$wgExtensionMessagesFiles['OggHandler'] = "$oggDir/OggHandler.i18n.php";
$wgExtensionMessagesFiles['OggHandlerMagic'] = "$oggDir/OggHandler.i18n.magic.php";
$wgParserOutputHooks['OggHandler'] = array( 'OggHandler', 'outputHook' );
$wgHooks['LanguageGetMagic'][] = 'OggHandler::registerMagicWords';


/**
 * Handle Adding of "timedText" NameSpace
 */
$wgTimedTextNS = null;

// Make sure $wgExtraNamespaces in an array (set to NULL by default) :
if ( !is_array( $wgExtraNamespaces ) ) {
	$wgExtraNamespaces = array();
}
// Check for "TimedText" NS
$maxNS = 101; // content pages need "even" namespaces
foreach($wgExtraNamespaces as $ns => $nsTitle ){
	if( $nsTitle == 'TimedText' ){
		$wgTimedTextNS = $ns;
	}
	if( $ns > $maxNS ){
		$maxNs = $ns;
	}
}
// If not found add Add a custom timedText NS
if( !$wgTimedTextNS ){
	$wgTimedTextNS = ( $maxNS + 1 );
	$wgExtraNamespaces[	$wgTimedTextNS ] = 'TimedText';
	$wgExtraNamespaces[ $wgTimedTextNS +1 ] =  'TimedText_talk';
}
define( "NS_TIMEDTEXT", $wgTimedTextNS);
// Assume $wgTimedTextNS +1 for talk
define( "NS_TIMEDTEXT_TALK", $wgTimedTextNS +1);


// end of handling timedText

//Setup a hook for iframe=true (will strip the interface and only output the player)
$wgHooks['ArticleFromTitle'][] = 'OggHandler::iframeOutputHook';

$wgExtensionCredits['media'][] = array(
	'path'           => __FILE__,
	'name'           => 'OggHandler',
	'author'         => 'Tim Starling',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:OggHandler',
	'description'    => 'Handler for Ogg Theora and Vorbis files, with JavaScript player.',
	'descriptionmsg' => 'ogg-desc',
);

/******************* CONFIGURATION STARTS HERE **********************/

//set the supported ogg codecs:
$wgOggVideoTypes = array( 'Theora' );
$wgOggAudioTypes = array( 'Vorbis', 'Speex', 'FLAC' );

//if wgPlayerStats collection is enabled or not
$wgPlayerStatsCollection=false;

//if $wgEnableJS2system = true  and the below variable is set to true
// then we can output the <video> tag and its re-written by mv_embed
$wgVideoTagOut = true;

//if we should enable iframe embedding of form ?title=File:name&iframe=true
$wgEnableIframeEmbed = true;

//Location of oggThumb binary (used over the ffmpeg version)
$wgOggThumbLocation = '/usr/bin/oggThumb';

//the location of ffmpeg2theora
$wgffmpeg2theoraPath = '/usr/bin/ffmpeg2theora';

// Location of the FFmpeg binary
$wgFFmpegLocation = '/usr/bin/ffmpeg';

/**
 * enable oggz_chop support
 * if enabled the mv_embed player will use temporal urls
 * for helping with seeking with some plugin types
 */
$wgEnableTemporalOggUrls = false;

// Filename or URL path to the Cortado Java player applet.
//
// If no path is included, the path to this extension's
// directory will be used by default -- this should work
// on most local installations.
//
// You may need to include a full URL here if $wgUploadPath
// specifies a host different from where the wiki pages are
// served -- the applet .jar file must come from the same host
// as the uploaded media files or Java security rules will
// prevent the applet from loading them.
//
$wgCortadoJarFile = "cortado-ovt-stripped-0.5.1.jar";
