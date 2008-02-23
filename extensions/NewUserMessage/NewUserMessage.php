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

define('NEWUSERMESSAGE_VERSION','1.0.1, 2008-02-10');

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

/*
 * Add the template message if the users talk page doesn't already exist
 */
function wfCreateNewUserMessage($user) {
	global $wgNewUserMessageTemplate;

	$name = $user->getName();
	$talk = $user->getTalkPage();

	if (!$talk->exists()) {
		$article = new Article($talk);
		$article->insertNewArticle('{'.'{'."$wgNewUserMessageTemplate|$name}}",false,false,true,false);
	}

	return true;
}
