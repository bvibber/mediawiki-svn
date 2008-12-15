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

$wgExtensionCredits['other'][] = array(
	'name'           => 'Newuserlog',
	'version'        => '2008-02-08',
	'description'    => 'Ads a [[Special:Log/newusers|log of account creations]] to [[Special:Log]]',
	'descriptionmsg' => 'newuserlog-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Newuserlog',
	'author'         => 'Ævar Arnfjörð Bjarmason'
);

$wgExtensionFunctions[] = 'wfNewuserlog';
$wgExtensionMessagesFiles['Newuserlog'] = dirname(__FILE__) . '/Newuserlog.i18n.php';

function wfNewuserlog() {
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
	# Run this hook on Special:Log
	$wgHooks['LogLine'][] = 'wfNewuserlogLogLine';
}

function wfNewuserlogHook( $user = null, $byEmail = false ) {
	global $wgUser, $wgContLang, $wgVersion;
	wfLoadExtensionMessages( 'Newuserlog' );

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
		$message = '';
		if ( version_compare( $wgVersion, '1.10alpha', '>=' ) ) {
			if( $byEmail ) {
				$message = wfMsgForContent( 'newuserlog-byemail' );
			}
		} else {
			$message = wfMsgForContent( 'newuserlog-create-text',
				$user->getName(), $talk, $contribs, $block );
		}
	}

	$log = new LogPage( 'newusers' );
	$log->addEntry( $action, $user->getUserPage(), $message, array( $user->getId() ) );

	return true;
}

/**
 * Create user tool links for self created users
 * @param string $log_type
 * @param string $log_action
 * @param object $title
 * @param array $paramArray
 * @param string $comment
 * @param string $revert user tool links
 * @param string $time timestamp of the log entry
 * @return bool true
 */
function wfNewuserlogLogLine( $log_type = '', $log_action = '', $title = null, $paramArray = array(), &$comment = '', &$revert = '', $time = '' ) {
	if ( $log_action == 'create2' ) {
		global $wgUser;
		$skin = $wgUser->getSkin();
		if( isset( $paramArray[0] ) ) {
			$revert = $skin->userToolLinks( $paramArray[0], $title->getDBkey(), true );
		} else {
			# Fall back to a blue contributions link
			$revert = $skin->userToolLinks( 1, $title->getDBkey() );
		}
		if( $time < '20080129000000' ) {
			# Suppress $comment from old entries (before 2008-01-29), not needed and can contain incorrect links
			$comment = '';
		}
	}
	return true;
}
