<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Add a new log to Special:Log that displays account creations in reverse
 * chronological order using the AddNewAccount hook
 *
 * @addtogroup Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfNewuserlog';
$wgExtensionCredits['other'][] = array(
	'name' => 'Newuserlog',
	'version'     => '1.1',
	'description' => 'adds a [[Special:Log/newusers|log of account creations]] to [[Special:Log]]',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Newuserlog',
	'author' => 'Ævar Arnfjörð Bjarmason',
);

# Internationalisation file
require_once( 'Newuserlog.i18n.php' );

function wfNewuserlog() {
	# Add messages
	global $wgMessageCache, $wgNewuserlogMessages;
	foreach( $wgNewuserlogMessages as $key => $value ) {
		$wgMessageCache->addMessages( $wgNewuserlogMessages[$key], $key );
	}

	# Add a new log type
	global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
	$wgLogTypes[]                      = 'newusers';
	$wgLogNames['newusers']            = 'newuserlogpage';
	$wgLogHeaders['newusers']          = 'newuserlogpagetext';
	$wgLogActions['newusers/newusers'] = 'newuserlogentry';
	$wgLogActions['newusers/create']   = 'newuserlog-create-entry';
	$wgLogActions['newusers/create2']  = 'newuserlog-create2-entry';

	# Run this hook on new account creation
	global $wgHooks;
	$wgHooks['AddNewAccount'][] = 'wfNewuserlogHook';
}

function wfNewuserlogHook( $user = null ) {
	global $wgUser, $wgContLang, $wgVersion;

	if( is_null( $user ) ) {
		// Compatibility with old versions which didn't pass the parameter
		$user = $wgUser;
	}

	$talk = $wgContLang->getFormattedNsText( NS_TALK );
	$contribs = wfMsgForContent( 'contribslink' );
	$block = wfMsgForContent( 'blocklink' );

	if( $user->getName() == $wgUser->getName() ) {
		$message = '';
		$action = 'create';
	} else {
		// Links not necessary for self-creations, they will appear already in
		// recentchanges and special:log view for the creating user.
		// For compatability: From 1.10alpha the 'user tools' are used at special:log
		// see bug 4756: Long usernames break block link in new user log entries

		$action = 'create2';
		if ( version_compare( $wgVersion, '1.10alpha', '>=' ) ) {
			$message = '';
		} else {
			$message = wfMsgForContent( 'newuserlog-create-text',
				$user->getName(), $talk, $contribs, $block );
		}
	}

	$log = new LogPage( 'newusers' );
	$log->addEntry( $action, $user->getUserPage(), $message, array( $user->getId() ) );

	return true;
}
