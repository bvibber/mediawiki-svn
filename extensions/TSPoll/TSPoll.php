<?php
/**
 * Toolserver Poll - Include the Toolserver-Poll-Skript(http://toolserver.org/~jan/poll/index.php)
 *
 * To activate this extension, add the following into your LocalSettings.php file:
 * require_once("$IP/extensions/TSPoll/TSPoll.php");
 *
 * @ingroup Extensions
 * @author Jan Luca <jan@toolserver.org>
 * @version 1.0 Dev
 * @link http://www.mediawiki.org/wiki/User:Jan_Luca/Extension:TSPoll Documentation
 * @license http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported or later
 */

/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */

// Abbrechen des Skriptes, wenn es nicht in Mediawiki eingebunden ist
if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
	die( - 1 );
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
	'name'          => 'TSPoll',
	'version'       => '1.0 Dev',
	'path'          => __FILE__,
	'author'        => 'Jan Luca',
	'url'           => 'http://www.mediawiki.org/wiki/User:Jan_Luca/Extension:TSPoll',
	'descriptionmsg' => 'tspoll-desc'
);

// Avoid unstubbing $wgParser on setHook() too early on modern (1.12+) MW versions, as per r35980
if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	$wgHooks['ParserFirstCallInit'][] = 'efTSPollSetup';
} else { // Otherwise do things the old fashioned way
	$wgExtensionFunctions[] = 'efTSPollSetupHook';
}

$wgExtensionMessagesFiles['TSPoll'] = dirname( __FILE__ ) . '/TSPoll.i18n.php';

function efTSPollSetup() {
	global $wgParser;
	$wgParser->setHook( 'TSPoll', 'efTSPollRender' );
	$wgParser->setHook( 'tspoll', 'efTSPollRender' );
	return true;
}

function efTSPollSetupHook( &$parser ) {
	$parser->setHook( 'TSPoll', 'efTSPollRender' );
	$parser->setHook( 'tspoll', 'efTSPollRender' );
	return true;
}

function efTSPollRender( $input, $args, $parser ) {

	if ( isset( $args['id'] ) && $args['id'] != "" ) {
		$id = wfUrlencode( $args['id'] );
	} else {
		wfLoadExtensionMessages( 'TSPoll' );
		return wfMsg( 'tspoll-id-error' );
	}

  if ( isset( $args['dev'] ) && $args['dev'] != "1" ) { // If the arrgument dev is given, use the TSPoll-Dev-Version
      $get_server = Http::get( 'http://toolserver.org/~jan/poll/dev/main.php?page=wiki_output&id='.$id );
  } else { // sonst die normale Version verwenden
      $get_server = Http::get( 'http://toolserver.org/~jan/poll/main.php?page=wiki_output&id='.$id );
  }

	if ( $get_server != '' ) {
		return $get_server;
	}
	else {
		wfLoadExtensionMessages( 'TSPoll' );
		return wfMsg( 'tspoll-fetch-error' );
	}
}
