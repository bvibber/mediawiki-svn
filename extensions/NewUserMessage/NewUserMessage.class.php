<?php
/** Extension:NewUserMessage
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author [http://www.organicdesign.co.nz/nad User:Nad]
 * @license LGPL (http://www.gnu.org/copyleft/lesser.html)
 * @copyright 2007-10-15 [http://www.organicdesign.co.nz/nad User:Nad]
 */

if (!defined('MEDIAWIKI'))
	die('Not an entry point.');

class NewUserMessage {
	/*
	 * Add the template message if the users talk page doesn't already exist
	 */
	static function createNewUserMessage($user) {
		$name = $user->getName();
		$talk = $user->getTalkPage();

		if (!$talk->exists()) {
			global $wgUser, $wgNewUserMinorEdit, $wgNewUserSupressRC;

			wfLoadExtensionMessages( 'NewUserMessage' );

			$article = new Article($talk);

			// Need to make the edit on the user talk page in another
			// user's context. Park the current user object and create
			// a user object for $editingUser. If that user does not
			// exist, make the edit as reserved user
			// "MediaWiki default".
			$parkedWgUser = $wgUser;
			$editingUser = wfMsg( 'newusermessage-editor' );
			if( User::idFromName( $editingUser ) ) {
				$wgUser = User::newFromName( $editingUser );
			}
			else
				$wgUser = User::newFromName( "MediaWiki default" );

			$flags = 0;
			$messageTemplate = wfMsg( 'newusermessage-template' );
			if ($wgNewUserMinorEdit) $flags = $flags | EDIT_MINOR;
			if ($wgNewUserSupressRC) $flags = $flags | EDIT_SUPPRESS_RC;

			$article->doEdit('{'.'{'."$messageTemplate|$name}}", wfMsg( 'newuseredit-summary' ), $flags);
			$wgUser = $parkedWgUser;
		}
		return true;
	}
}
