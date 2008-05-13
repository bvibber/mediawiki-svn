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

define('NEWUSERMESSAGE_VERSION','1.1.2, 2008-05-13');

// Specify a template to wrap the new user message within
$wgNewUserMessageTemplate = 'MediaWiki:NewUserMessage';

// Set the username of the user that makes the edit on user talk pages. If
// this user does not exist, the new user will show up as editing user.
$wgNewUserMessageEditor = 'Admin';

// Edit summary for the recent changes entry of a new users message
$wgNewUserEditSummary = "Adding [[$wgNewUserMessageTemplate|welcome message]] to new user's talk page";

// Specify whether or not the new user message creation should show up in recent changes
$wgNewUserSupressRC = false;

// Should the new user message creation be a minor edit?
$wgNewUserMinorEdit = true;



$wgExtensionMessagesFiles['NewUserMessage'] = dirname(__FILE__) . '/NewUserMessage.i18n.php';
$wgHooks['AddNewAccount'][] = 'wfCreateNewUserMessage';

$wgExtensionCredits['other'][] = array(
	'name'           => 'NewUserMessage',
	'version'        => NEWUSERMESSAGE_VERSION,
	'author'         => "[http://www.organicdesign.co.nz/User:Nad Nad]",
	'description'    => "Add a [[$wgNewUserMessageTemplate|message]] to newly created user's talk pages",
	'descriptionmsg' => 'newusermessage-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:NewUserMessage',
);

/*
 * Add the template message if the users talk page doesn't already exist
 */
function wfCreateNewUserMessage($user) {
	global $wgNewUserMessageTemplate, $wgNewUserMinorEdit, $wgNewUserSupressRC, $wgNewUserEditSummary;

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

		$flags = 0;
		if ($wgNewUserMinorEdit) $flags = $flags | EDIT_MINOR;
		if ($wgNewUserSupressRC) $flags = $flags | EDIT_SUPPRESS_RC;

		$article->doEdit('{'.'{'."$wgNewUserMessageTemplate|$name}}", $wgNewUserEditSummary, $flags);
		$wgUser = $parkedWgUser;
	}

	return true;
}
