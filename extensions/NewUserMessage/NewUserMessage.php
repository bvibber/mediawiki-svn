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

define('NEWUSERMESSAGE_VERSION','1.1, 2008-05-05');

$wgNewUserMessageTemplate = 'MediaWiki:NewUserMessage';

$wgExtensionMessagesFiles['NewUserMessage'] = dirname(__FILE__) . '/NewUserMessage.i18n.php';
$wgHooks['AddNewAccount'][] = 'wfCreateNewUserMessage';

$wgExtensionCredits['other'][] = array(
	'name'           => 'NewUserMessage',
	'version'        => NEWUSERMESSAGE_VERSION,
	'author'         => "[http://www.organicdesign.co.nz/User:Nad Nad]",
	'description'    => "Add a [[MediaWiki:NewUserMessage|message]] to newly created user's talk pages",
	'descriptionmsg' => 'newusermessage-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:NewUserMessage',
);

// Set the username of the user that makes the edit on user talk pages. If
// this user does not exist, the new user will show up as editing user.
$wgNewUserMessageEditor = 'Admin';

/*
 * Add the template message if the users talk page doesn't already exist
 */
function wfCreateNewUserMessage($user) {
	global $wgNewUserMessageTemplate;

	$name = $user->getName();
	$talk = $user->getTalkPage();

	if (!$talk->exists()) {
		global $wgUser, $wgNewUserMessageEditor;

		$article = new Article($talk);

		// Need to make the edit on the user talk page in another
		// user's context. Park the current user object and create
		// a user object for $wgNewUserMessageEditor. If that user
		// does not exist, make the edit with as the new user
		// anyway.
		$parkedWgUser = $wgUser;
		$wgUser = User::newFromName( $wgNewUserMessageEditor );
		if ( !$wgUser->idForName() ) {
			$wgUser = $parkedWgUser;
		}

		$article->doEdit('{'.'{'."$wgNewUserMessageTemplate|$name}}", false, EDIT_MINOR);
		$wgUser = $parkedWgUser;
	}

	return true;
}
