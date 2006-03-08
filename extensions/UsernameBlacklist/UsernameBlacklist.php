<?php

/**
 * Extension to provide a global "bad username" list
 *
 * @author Rob Church <robchur@gmail.com>
 * @package MediaWiki
 * @subpackage Extensions
 * @copyright  2006 Rob Church
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0
 */

if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'UsernameBlacklist_Init';
	$wgExtensionCredits['other'][] = array(
		'name' => 'Username blacklist',
		'description' => 'provides a regex.-compatible blacklist of usernames',
		'author' => 'Rob Church'
		);

	/**
	 * Constructor
	 */	
	function UsernameBlacklist_Init() {
		global $wgMessageCache, $wgHooks;
		$wgHooks['AbortNewAccount'][] = 'UsernameBlacklist_Hook';
		$wgMessageCache->addMessage( 'blacklistedusername', 'Blacklisted username' );
		$wgMessageCache->addMessage( 'blacklistedusernametext', 'The username you have chosen matches the [[MediaWiki:Usernameblacklist|list of blacklisted usernames]].' );
	}

	/**
	 * Hooked function used to check the username against a blacklist.
	 * Bring an error page if there is any match.
	 *
	 * @return boolean false if username is blacklisted.
	 */
	function UsernameBlacklist_Hook( $user ) {
		global $wgOut;
		$username  = $user->getName();
		$blacklist = wfMsg( 'usernameblacklist' );
		if( $blacklist != '&lt;usernameblacklist&gt;' ) {
			$list = explode( "\n", $blacklist );
			foreach( $list as $item ) {
				$item = UsernameBlacklist_Trim( $item );
				if( $item ) {
					$regex = '/' . UsernameBlacklist_Trim( $item ) . '/';
					if( preg_match( $regex, $username ) > 0 ) {
						$rt_title = Title::makeTitle( NS_SPECIAL, 'Userlogin' );
						$wgOut->errorPage( 'blacklistedusername', 'blacklistedusernametext' );
						$wgOut->returnToMain( false, $rt_title->getPrefixedText() );
						return( false );
					}
				}
			}
			return( true );
		} else {
			return( true );
		}
	}

	/**
	 * Remove occurences of ' ' or '*' at the beginning of a string
	 * and check for commented lines
	 *
	 * @param string $text A text to trim.
	 * @return string The trimmed text.
	 */
	function UsernameBlacklist_Trim( $text ) {
		while( ( substr( $text, 0, 1 ) == '*' ) || ( substr( $text, 0, 1 ) == ' ' ) ) {
			$text = substr( $text, 1, strlen( $text ) - 1 );
		}
		return( substr( $text, 0, 1 ) == '#' ? false : $text );
	}	
	
} else {
	die( 'This file is an extension to the MediaWiki package, and cannot be executed separately.' );
}

?>
