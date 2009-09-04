<?php
/**
 * WikiTextLoggedInOut extension
 * Defines two new parser hooks, <loggedin> and <loggedout>
 * that will display different output depending if the user
 * is logged in or not.
 *
 * @author Wikia, Inc.
 * @version 1.0
 * @link http://www.mediawiki.org/wiki/Extension:WikiTextLoggedInOut
 */

$wgHooks['ParserFirstCallInit'][] = 'wfWikiTextLoggedIn';
$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['WikiTextLoginInOut'] = $dir . 'WikiTextLoggedInOut.i18n.php';

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'WikiTextLoggedInOut',
	'version' => '1.0',
	'author' => 'Wikia New York Team',
	'description' => 'Two parser hooks, <tt>&lt;loggedin&gt;</tt> and <tt>&lt;loggedout&gt;</tt> to show different text depending on the users\' login state',
	'url' => 'http://www.mediawiki.org/wiki/Extension:WikiTextLoggedInOut',
	'descriptionmsg' => 'wikitextloggedinout-desc'
);

function wfWikiTextLoggedIn( &$parser ) {
	$parser->setHook( 'loggedin', 'OutputLoggedInText' );
	return true;
}

function OutputLoggedInText( $input, $args, &$parser ) {
	global $wgUser, $wgOut;

	if( $wgUser->isLoggedIn() ){
		return $parser->recursiveTagParse($input);
	}

	return "";
}

$wgHooks['ParserFirstCallInit'][] = 'wfWikiTextLoggedOut';

function wfWikiTextLoggedOut( &$parser ) {
	$parser->setHook( 'loggedout', 'OutputLoggedOutText' );
	return true;
}

function OutputLoggedOutText( $input, $args, &$parser ) {
	global $wgUser, $wgOut;

	if( !$wgUser->isLoggedIn() ){
		return $parser->recursiveTagParse($input);
	}

	return "";
}
