<?php
/**
* SharedUserrights -- adds a special page to manage rights in a shared database
*
* @ingroup Extensions
*
* @author Charles Melbye <charlie@yourwiki.net>
* @version 0.9
* @copyright Copyright (C) 2008 YourWiki, Inc.
* @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
*
*/

if( !defined('MEDIAWIKI') ){
	echo('THIS IS NOT A VALID ENTRY POINT.');
	exit(1);
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'SharedUserrights',
	'url' => 'http://www.mediawiki.org/wiki/Extension:SharedUserrights',
	'version' => '0.9',
	'author' => 'Charles Melbye',
	'description' => 'Easy global user rights administration'
);

$dir = dirname(__FILE__) . '/';

$wgAutoloadClasses['SharedUserrights'] = $dir . 'SharedUserrights_body.php';
$wgExtensionMessagesFiles['SharedUserrights'] = $dir . 'SharedUserrights.i18n.php';
$wgSpecialPages['SharedUserrights'] = 'SharedUserrights';
$wgSpecialPageGroups['SharedUserrights'] = 'users';
$wgHooks['LanguageGetSpecialPageAliases'][] = 'suLocalizedPageName';

$wgLogTypes[]                     = 'gblrights';
$wgLogNames['gblrights']          = 'gblrights-logpage';
$wgLogHeaders['gblrights']        = 'gblrights-pagetext';
$wgLogActions['gblrights/rights'] = 'gblrights-rights-entry';


function suLocalizedPageName( &$specialPageArray, $code ) {
	wfLoadExtensionMessages('SharedUserrights');
	$text = wfMsg('shareduserrights');

	$title = Title::newFromText($text);
	$specialPageArray['SharedUserrights'][] = $title->getDBKey();
	
	return true;
}

// Hooked functions
$wgHooks['UserEffectiveGroups'][] = 'efAddSharedUserRights';

function efAddSharedUserRights( $user, $groups ) {
	global $wgSharedDB, $wgDBname;	

	$dbr = wfGetDB( DB_SLAVE );

	if( $dbr->selectDB($wgSharedDB) ) {
		$res = $dbr->select( 'shared_user_groups',
			'sug_group',
			array ('sug_user' => $user->mId));
		while ( $row = $dbr->fetchObject( $res ) ) {
			$groups[] = $row->sug_group;
		}
		$dbr->freeResult( $res );
		$dbr->selectDB( $wgDBname ); # to prevent Listusers from breaking
	}

	return $groups;
}
