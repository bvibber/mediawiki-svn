<?php
/**
 * Poll - Create a specialpage for useing polls in MediaWiki
 *
 * To activate this extension, add the following into your LocalSettings.php file:
 * require_once("$IP/extensions/Poll/Poll.php");
 *
 * @ingroup Extensions
 * @author Jan Luca <jan@toolserver.org>
 * @version 0.0.1
 * @link http://www.mediawiki.org/wiki/User:Jan_Luca/Extension:Poll2 Documentation
 * @license http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported or later
 */

/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */

// Die the extension, if not MediaWiki is used
if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
	die( -1 );
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'Poll',
	'version'        => '0.0.1',
	'path'           => __FILE__,
	'author'         => 'Jan Luca',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Poll2',
	'descriptionmsg' => 'poll-desc'
);

// New right: poll-admin
$wgGroupPermissions['sysop']['poll-admin'] = true;
$wgGroupPermissions['*']['poll-admin'] = false;
$wgAvailableRights[] = 'poll-admin';

// New right: poll-create
$wgGroupPermissions['autoconfirmed']['poll-create'] = true;
$wgGroupPermissions['*']['poll-create'] = false;
$wgAvailableRights[] = 'poll-create';

// New right: poll-create
$wgGroupPermissions['autoconfirmed']['poll-vote'] = true;
$wgGroupPermissions['*']['poll-vote'] = false;
$wgAvailableRights[] = 'poll-vote';

$dir = dirname( __FILE__ ) . '/';

$wgAutoloadClasses['Poll'] = $dir . 'Poll_body.php'; # Tell MediaWiki to load the extension body.
$wgExtensionMessagesFiles['Poll'] = $dir . 'Poll.i18n.php';
$wgExtensionAliasesFiles['Poll'] = $dir . 'Poll.alias.php';
$wgSpecialPages['Poll'] = 'Poll'; # Let MediaWiki know about your new special page.
$wgSpecialPageGroups['Poll'] = 'other';

# Schema changes
$wgHooks['LoadExtensionSchemaUpdates'][] = 'efPollSchemaUpdates';

function efPollSchemaUpdates() {
	global $wgDBtype, $wgExtNewFields, $wgExtPGNewFields, $wgExtNewIndexes, $wgExtNewTables;
	$base = dirname(__FILE__);
	if( $wgDBtype == 'mysql' ) {
		$wgExtNewTables[] = array( 'poll', "$base/Poll.sql" ); // Initial install tables
    }
	return true;
}
