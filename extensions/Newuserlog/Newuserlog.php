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
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfNewuserlog';
$wgExtensionCredits['other'][] = array(
	'name' => 'Newuserlog',
	'author' => 'Ævar Arnfjörð Bjarmason'
);

function wfNewuserlog() {
	global $wgMessageCache, $wgHooks, $wgContLang;
	
	$talk = $wgContLang->getFormattedNsText( NS_TALK );
	
	$wgMessageCache->addMessages(
		array(
			'newuserlogpage' => 'New user log',
			'newuserlogpagetext' => 'This is a log of recent user creations',
			'newuserloglog' => 'Created the user "[[User:$1|$1]]" ([[User talk:$1|' . $talk . ']])'
		)
	);

	# Add a new log type
	$wgHooks['LogPageValidTypes'][] = 'wfNewuserlogAddLogType';
	$wgHooks['LogPageLogName'][] = 'wfNewuserlogAddLogName';
	$wgHooks['LogPageLogHeader'][] = 'wfNewuserlogAddLogHeader';
	
	# Run this hook on new account creation
	$wgHooks['AddNewAccount'][] = 'wfNewuserlogHook';
}

function wfNewuserlogHook() {
	global $wgUser, $wgTitle;
	
	$log = new LogPage( 'newusers' );
	$log->addEntry( 'newusers', $wgTitle, wfMsg( 'newuserloglog', $wgUser->getName() ) );
	return true;
}

function wfNewuserlogAddLogType( &$types ) {
	if ( !in_array( 'newusers', $types ) )
		$types[] = 'newusers';
	return true;
}

function wfNewuserlogAddLogName( &$names ) {
	$names['newusers'] = 'newuserlogpage';
	return true;
}

function wfNewuserlogAddLogHeader( &$headers ) {
	$headers['newusers'] = 'newuserlogpagetext';
	return true;
}
?>
