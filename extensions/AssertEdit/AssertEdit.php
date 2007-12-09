<?php

if ( ! defined( 'MEDIAWIKI' ) )
	die();
/**#@+
 *
 * A bot interface extension that adds edit assertions, to help bots ensure
 * they stay logged in, and are working with the right wiki.
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:Assert_Edit
 *
 * @author Steve Sanbeg
 * @copyright Copyright © 2006-2007, Steve Sanbeg
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['other'][] = array(
	'name' => 'AssertEdit',
	'version' => '1.1',
	'author' => 'Steve Sanbeg',
	'description' => 'Adds edit assertions for use by bots',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Assert_Edit'
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['AssertEdit'] = $dir . 'AssertEdit.i18n.php';

class AssertEdit
{
	/**
	 * methods for core assertions
	 */
	static function assert_user() {
		global $wgUser;
		return $wgUser->isLoggedIn();
	}
	static function assert_bot() {
		global $wgUser;
		return $wgUser->isBot();
	}
	static function assert_exists() {
		global $wgTitle;
		return ( $wgTitle->getArticleID() != 0 );
	}

	/*
	 * List of assertions; can be modified with setAssert
	 */
	static private $msAssert = array(
		//simple constants, i.e. to test if the extension is installed.
		'true' => true,
		'false' => false,
		//useful variable tests, to ensure we stay logged in
		'user' => array( 'AssertEdit', 'assert_user' ),
		'bot' => array( 'AssertEdit', 'assert_bot' ),
		'exists' => array( 'AssertEdit', 'assert_exists' ),
		//override these in LocalSettings.php
		//'wikimedia' => false, //is this an offical wikimedia site?
		'test' => false      //Do we allow random tests?
	);

	static function setAssert( $key, $val ) {
		//Don't confuse things by changing core assertions.
		switch ( $key ) {
			case 'true':
			case 'false':
			case 'user':
			case 'bot':
			case 'exists':
				return false;
		}
		//make sure it's useable.
		if ( is_bool( $value ) or is_callable( $value ) ) {
			self::$msAssert[$key] = $value;
			return true;
		} else {
			return false;
		}
	}

	//call the specified assertion
	static function callAssert( $assertName, $negate ) {
		if ( isset( self::$msAssert[$assertName] ) ) {
			if ( is_bool( self::$msAssert[$assertName] ) ) {
				$pass = self::$msAssert[$assertName];
			} elseif ( is_callable( self::$msAssert[$assertName] ) ) {
				$pass = call_user_func( self::$msAssert[$assertName] );
			}

			if ( $negate and isset( $pass ) ) {
				$pass = !$pass;
			}
		} else {
			//unrecognized assert fails, regardless of negation.
			$pass = false;
		}
		return $pass;
	}

}

function wfAssertEditHook( &$editpage ) {
	global $wgOut, $wgRequest;

	$assertName = $wgRequest->getVal( 'assert' );
	$pass = true;

	if ( $assertName != '' ) {
		$pass = AssertEdit::callAssert( $assertName, false );
	}

	//check for negative assert
	if ( $pass ) {
		$assertName = $wgRequest->getVal( 'nassert' );
		if ( $assertName != '' ) {
			$pass = AssertEdit::callAssert( $assertName, true );
		}
	}

	if ( $pass ) {
		return true;
	} else {
		wfLoadExtensionMessages( 'AssertEdit' );

		//slightly modified from showErrorPage(), to return back here.
		$wgOut->setPageTitle( wfMsg( 'assert_edit_title' ) );
		$wgOut->setHTMLTitle( wfMsg( 'errorpagetitle' ) );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		$wgOut->enableClientCache( false );
		$wgOut->mRedirect = '';

		$wgOut->mBodytext = '';
		$wgOut->addWikiText( wfMsg( 'assert_edit_message', $assertName ) );

		$wgOut->returnToMain( false, $editpage->mTitle );
		return false;
	}
}

$wgHooks['AlternateEdit'][] = 'wfAssertEditHook';
