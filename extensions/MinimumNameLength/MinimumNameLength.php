<?php

/**
 * Extension enforces a minimum username length
 * during account registration
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efMinimumNameLengthSetup';
	$wgExtensionCredits['other'][] = array(
		'name' => 'Minimum Username Length',
		'version'     => '1.1',
		'author' => 'Rob Church',
		'url' => 'http://www.mediawiki.org/wiki/Extension:Minimum_Name_Length',
		'description' => 'Enforce a minimum username length during account registration',
	);

	/**
	 * Minimum username length to enforce
	 */
	$wgMinimumUsernameLength = 10;

	/**
	 * Extension setup function
	 */
	function efMinimumNameLengthSetup() {
		global $wgHooks, $wgMessageCache;
		$wgHooks['AbortNewAccount'][] = 'efMinimumNameLength';
		require_once( dirname( __FILE__ ) . '/MinimumNameLength.i18n.php' );
		foreach( efMinimumNameLengthMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
	}

	/**
	 * Hooks account creation and checks the
	 * username length, cancelling with an error
	 * if the username is too short
	 *
	 * @param User $user User object being created
	 * @param string $error Reference to error message to show
	 * @return bool
	 */
	function efMinimumNameLength( $user, &$error ) {
		global $wgMinimumUsernameLength;
		if( mb_strlen( $user->getName() ) < $wgMinimumUsernameLength ) {
			$error = wfMsgHtml( 'minnamelength-error', $wgMinimumUsernameLength );
			return false;
		} else {
			return true;
		}
	}

} else {
	echo( "This file is an extension to the MediaWiki software. It cannot be used standalone.\n" );
	exit( 1 );
}
