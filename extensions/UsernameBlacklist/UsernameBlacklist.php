<?php

/**
 * Extension to provide a global "bad username" list
 *
 * @author Rob Church <robchur@gmail.com>
 * @package MediaWiki
 * @subpackage Extensions
 * @copyright © 2006 Rob Church
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0
 */

if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efUsernameBlacklistSetup';
	$wgExtensionCredits['other'][] = array( 'name' => 'Username Blacklist', 'author' => 'Rob Church', 'url' => 'http://meta.wikimedia.org/wiki/Username_Blacklist' );

	$wgAvailableRights[] = 'uboverride';
	$wgGroupPermissions['sysop']['uboverride'] = true;

	/**
	 * Register the extension
	 */
	function efUsernameBlacklistSetup() {
		global $wgMessageCache, $wgHooks;
		$wgHooks['AbortNewAccount'][] = 'efUsernameBlacklist';
		$wgMessageCache->addMessage( 'blacklistedusername', 'Blacklisted username' );
		$wgMessageCache->addMessage( 'blacklistedusernametext', 'The username you have chosen matches the [[MediaWiki:Usernameblacklist|list of blacklisted usernames]]. Please choose another.' );
	}

	/**
	 * Perform the check
	 * @param $user User to be checked
	 * @return bool
	 */
	function efUsernameBlacklist( &$user ) {
		global $wgUser;
		$blackList =& UsernameBlacklist::fetch();
		if( $blackList->match( $user->getName() ) && !$wgUser->isAllowed( 'uboverride' ) ) {
			global $wgOut;
			$returnTitle = Title::makeTitle( NS_SPECIAL, 'Userlogin' );
			$wgOut->errorPage( 'blacklistedusername', 'blacklistedusernametext' );
			$wgOut->returnToMain( false, $returnTitle->getPrefixedText() );
			return false;
		} else {
			return true;
		}
	}
	
	class UsernameBlacklist {
		
		var $regex;		
		
		/**
		 * Trim leading spaces and asterisks from the text
		 * @param $text Text to trim
		 * @return string
		 */
		function transform( $text ) {
			return trim( $text, ' *' );
		}
		
		/**
		 * Is the supplied text a comment?
		 * @param $text Text to check
		 * @return bool
		 */
		function isComment( $text ) {
			return substr( $this->transform( $text ), 0, 1 ) == '#';
		}
		
		/**
		 * Return a regular expression representing the blacklist
		 * @return string
		 */
		function buildBlacklist() {
			$blacklist = wfMsg( 'usernameblacklist' );
			if( $blacklist != '&lt;usernameblacklist&gt;' ) {
				$lines = explode( "\n", $blacklist );
				foreach( $lines as $line ) {
					if( !$this->isComment( $line ) )
						$groups[] = $this->transform( $line );
				}
				return count( $groups ) ? '/(' . implode( '|', $groups ) . ')/' : false;
			} else {
				return false;
			}
		}
		
		/**
		 * Match a username against the blacklist
		 * @param $username Username to check
		 * @return bool
		 */
		function match( $username ) {
			return $this->regex ? preg_match( $this->regex, $username ) : false;
		}
		
		/**
		 * Constructor
		 * Prepare the regular expression
		 */
		function UsernameBlacklist() {
			$this->regex = $this->buildBlacklist();
		}

		/**
		 * Fetch an instance of the blacklist class
		 * @return UsernameBlacklist
		 */
		function fetch() {
			static $blackList = false;
			if( !$blackList )
				$blackList = new UsernameBlacklist();
			return $blackList;
		}
		
	}
	
} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

?>
