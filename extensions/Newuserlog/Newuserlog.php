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
	'description' => 'adds a [[Special:Log/newusers|log of recent account creations]] to [[Special:Log]]',
	'author' => 'Ævar Arnfjörð Bjarmason'
);

function wfNewuserlog() {
	global $wgMessageCache, $wgHooks, $wgContLang;
	
	$wgMessageCache->addMessages(
		array(
			'newuserlogpage' => 'User creation log',
			'newuserlogpagetext' => 'This is a log of recent user creations',
			'newuserlogentry' => '',
			'newuserloglog' => "Created the user [[User:$1|$1]] ([[User talk:$1|$2]] | [[Special:Contributions/$1|$3]])"
		)
	);

	# Add a new log type
	$wgHooks['LogPageValidTypes'][] = 'wfNewuserlogAddLogType';
	$wgHooks['LogPageLogName'][] = 'wfNewuserlogAddLogName';
	$wgHooks['LogPageLogHeader'][] = 'wfNewuserlogAddLogHeader';
	$wgHooks['LogPageActionText'][] = 'wfNewuserlogAddActionText';
	
	# Run this hook on new account creation
	$wgHooks['AddNewAccount'][] = 'wfNewuserlogHook';
}

function wfNewuserlogHook() {
	global $wgUser, $wgTitle, $wgContLang;

	$talk = $wgContLang->getFormattedNsText( NS_TALK );
	$contribs = wfMsgForContent( 'contribslink' );
	
	$log = new LogPage( 'newusers' );
	$log->addEntry( 'newusers', $wgTitle, wfMsgForContent( 'newuserloglog', $wgUser->getName(), $talk, $contribs ) );
	
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

function wfNewuserlogAddActionText( &$actions ) {
	$actions['newusers/newusers'] = 'newuserlogentry';
	return true;
}
?>
