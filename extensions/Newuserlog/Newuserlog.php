<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Add a new log to Special:Log that displays account creations in reverse
 * chronological order using the AddNewAccount hook
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfNewuserlog';
$wgExtensionCredits['other'][] = array(
	'name' => 'Newuserlog',
	'description' => 'adds a [[Special:Log/newusers|log of account creations]] to [[Special:Log]]',
	'author' => 'Ævar Arnfjörð Bjarmason'
);

# Internationlisation file
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

function wfNewuserlogHook( $user=null ) {
	global $wgUser, $wgContLang;
	
	if( is_null( $user ) ) {
		// Compatibility with old versions which didn't pass the parameter
		$user = $wgUser;
	}
	
	$talk = $wgContLang->getFormattedNsText( NS_TALK );
	$contribs = wfMsgForContent( 'contribslink' );
	$block = wfMsgForContent( 'blocklink' );
	$message = wfMsgForContent( 'newuserlog-create-text',
		$user->getName(), $talk, $contribs, $block );
	
	if( $user->getName() == $wgUser->getName() ) {
		$action = 'create';
	} else {
		$action = 'create2';
	}
	
	$log = new LogPage( 'newusers' );
	$log->addEntry( $action, $user->getUserPage(), $message );
	
	return true;
}
?>
