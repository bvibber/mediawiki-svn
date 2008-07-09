<?php
/**
* GroupPermissions Manager extension by Ryan Schmidt
* Allows privelaged users to modify group permissions via a special page
* See http://www.mediawiki.org/wiki/Extension:GroupPermissions_Manager for more info
*/

if(!defined('MEDIAWIKI')) {
	echo("This file is an extension to the MediaWiki software and is not a valid access point");
	die(1);
}

$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'GroupPermissions Manager',
	'author'         => 'Ryan Schmidt',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:GroupPermissions_Manager',
	'version'        => '3.2',
	'description'    => 'Manage group permissions via a special page',
	'descriptionmsg' => 'grouppermissions-desc',
);
$wgAutoloadClasses['GroupPermissions'] = dirname(__FILE__) . '/GroupPermissionsManager_body.php';
$wgAutoloadClasses['RemoveUnusedGroups'] = dirname(__FILE__) . '/RemoveUnusedGroups.php';
$wgAutoloadClasses['SortPermissions'] = dirname(__FILE__) . '/SortPermissions.php';
$wgSpecialPages['GroupPermissions'] = 'GroupPermissions';
$wgSpecialPages['RemoveUnusedGroups'] = 'RemoveUnusedGroups';
$wgSpecialPages['SortPermissions'] = 'SortPermissions';
$wgExtensionMessagesFiles['GroupPermissions'] = dirname(__FILE__) . '/GroupPermissionsManager.i18n.php';

$wgLogTypes[] = 'gpmanager';
$wgLogActions['gpmanager/add'] = 'grouppermissions-log-add';
$wgLogActions['gpmanager/change'] = 'grouppermissions-log-change';
$wgLogActions['gpmanager/delete'] = 'grouppermissions-log-delete';
$wgLogActions['gpmanager/gpmanager'] = 'grouppermissions-log-entry';
$wgLogHeaders['gpmanager'] = 'grouppermissions-log-header';
$wgLogNames['gpmanager'] = 'grouppermissions-log-name';
$wgSpecialPageGroups['GroupPermissions'] = 'wiki';
$wgSpecialPageGroups['RemoveUnusedGroups'] = 'users';
$wgSpecialPageGroups['SortPermissions'] = 'wiki';

##Permission required to use the 'GroupPermissions' and 'SortPermissions' special page
##By default all bureaucrats can
$wgGroupPermissions['bureaucrat']['grouppermissions'] = true;
##Uncomment this if you want to make a separate group that can access the page as well
#$wgGroupPermissions['grouppermissions']['grouppermissions'] = true;
##'RemoveUnusedGroups' requires the 'userrights' permission, also given to bureaucrats by default

/**
* Permissions++ sub-extension
* This requires the GroupPermissions manager extension to function to its utmost ability, so don't insall this separate from it (or vice versa)
*/

$wgExtensionCredits['other'][] = array(
	'name'           => 'Permissions++',
	'author'         => 'Ryan Schmidt',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:GroupPermissions_Manager',
	'version'        => '1.0',
	'description'    => 'Extended permissions system',
	'descriptionmsg' => 'grouppermissions-desc2',
);

$wgHooks['UserGetRights'][] = 'efGPManagerRevokeRights';
$wgHooks['userCan'][] = 'efGPManagerExtendedPermissionsGrant';
$wgHooks['getUserPermissionsErrors'][] = 'efGPManagerExtendedPermissionsRevoke';
$wgHooks['NormalizeMessageKey'][] = 'efGPManagerReplaceEditMessage';
$wgGPManagerNeverGrant = array();
$wgGPManagerSort = array();
$wgGPManagerSortTypes = array( 'read', 'edit', 'manage', 'admin', 'tech', 'misc' );

##Default permissions for the ones added by Permissions++ extension
###Reading-related permissions
$wgGroupPermissions['*']['viewsource'] = true; //allows viewing of wiki source when one cannot edit the page
$wgGroupPermissions['*']['history'] = true; //allows viewing of page histories
$wgGroupPermissions['*']['raw'] = true; //allows use of action=raw
$wgGroupPermissions['*']['render'] = true; //allows use of action=render
$wgGroupPermissions['*']['info'] = true; //allows use of action=info if the option is enabled
$wgGroupPermissions['*']['credits'] = true; //allows use of action=credits
$wgGroupPermissions['*']['search'] = true; //allows access to Special:Search
$wgGroupPermissions['*']['recentchanges'] = true; //allows access to Special:RecentChanges
$wgGroupPermissions['*']['contributions'] = true; //allows viewing Special:Contributions pages, including own
###Editing-related permissions
###Note that 'edit' is reduced to only allowing editing of non-talk pages now, it is NOT a global toggle anymore
###In addition, 'createpage', and 'createtalk' no longer require the 'edit' right, this can be useful if you want to allow people to make pages, but not edit existing ones
$wgGroupPermissions['*']['edittalk'] = true; //can edit talk pages, including use of section=new

##Grouping of permissions for the GPManager
$wgGPManagerSort['read'] = array( 'read', 'viewsource', 'history', 'raw', 'render', 'info',
'credits', 'search', 'recentchanges', 'contributions' );
$wgGPManagerSort['edit'] = array( 'edit', 'createpage', 'createtalk', 'move', 'move-subpages',
'createaccount', 'upload', 'reupload', 'reupload-shared', 'upload_by_url',
'editprotected', 'edittalk', 'writeapi' );
$wgGPManagerSort['manage'] = array( 'delete', 'bigdelete', 'deletedhistory', 'undelete', 'mergehistory',
'protect', 'block', 'blockemail', 'hideuser', 'userrights', 'userrights-interwiki', 'rollback', 'markbotedits',
'patrol', 'editinterface', 'editusercssjs', 'hiderevision', 'deleterevision', 'browsearchive', 'suppressrevision',
'suppressionlog', 'suppress' );
$wgGPManagerSort['admin'] = array( 'siteadmin', 'import', 'importupload', 'trackback', 'unwatchedpages',
'grouppermissions' );
$wgGPManagerSort['tech'] = array( 'bot', 'purge', 'minoredit', 'nominornewtalk', 'ipblock-exempt',
'proxyunbannable', 'autopatrol', 'apihighlimits', 'suppressredirect', 'autoconfirmed',
'emailconfirmed', 'noratelimit' );
$wgGPManagerSort['misc'] = array(); //all rights added by extensions that don't have a sort clause get put here

##Load the config files, if they exist. This must be the last thing to run in the startup part
if(file_exists(dirname(__FILE__) . "/config/GroupPermissions.php") ) {
	require_once(dirname(__FILE__) . "/config/GroupPermissions.php");
}
if(file_exists(dirname(__FILE__) . "/config/SortPermissions.php")) {
	require_once(dirname(__FILE__) . "/config/SortPermissions.php");
}

//Revoke the rights that are set to "never"
function efGPManagerRevokeRights(&$user, &$rights) {
	global $wgGPManagerNeverGrant;
	$groups = $user->getEffectiveGroups();
	$never = array();
	$rights = array_unique($rights);
	foreach( $groups as $group ) {
		if( array_key_exists( $group, $wgGPManagerNeverGrant ) ) {
			foreach( $wgGPManagerNeverGrant[$group] as $right ) {
				$never[] = $right;
			}
		}
	}
	$never = array_unique( $never );
	foreach( $never as $revoke ) {
		$offset = array_search( $revoke, $rights );
		if( $offset !== false ) {
			array_splice( $rights, $offset, 1 );
		}
	}
	return true;
}

//Extend the permissions system for finer-grained control without requiring hacks
//For allowing actions that the normal permissions system would prevent
function efGPManagerExtendedPermissionsGrant($title, $user, $action, &$result) {
	global $wgRequest;
	$result = false;
	if( $action == 'edit' && ($wgRequest->getVal('action') == 'edit' || $wgRequest->getVal('action') == 'submit') ) {
		if( !$title->exists() ) {
			$protection = getTitleProtection($title);
			if($protection) {
				if( !$user->isAllowed($protection['pt_create_perm']) ) {
					//pass it on to the normal permissions system to handle
					$result = null;
					return true;
				}
			}
			//otherwise don't pass it on to the normal permission system, because the edit right would then be checked
			if( $title->isTalkPage() && $user->isAllowed('createtalk') ) {
				$result = true;
				return false;
			} elseif( !$title->isTalkPage() && $user->isAllowed('createpage') ) {
				$result = true;
				return false;
			}
		} else {
			$protection = $title->getRestrictions('edit');
			if($protection) {
				foreach($protection as $right) {
					if(!$user->isAllowed($right)) {
						//pass it on to the normal permissions system
						$result = null;
						return true;
					}
				}
			}
			if( $title->isTalkPage() && $user->isAllowed('edittalk') ) {
				$result = true;
				return false;
			} elseif( !$title->isTalkPage() && $user->isAllowed('edit') ) {
				$result = true;
				return false;
			}
		}
	}
	//hack for the UserCanRead method
	$res = efGPManagerExtendedPermissionsRevoke($title, $user, $action, $result);
	if(!$res) {
		$result = false;
		//yay epic hacking! If I can't choose to make it return badaccess-group0... I'll simply force it to
		global $wgGroupPermissions;
		foreach($wgGroupPermissions as $group => $rights) {
			$wgGroupPermissions[$group]['read'] = false;
		}
		return false;
	}
	$result = null;
	return true; //otherwise we don't care
}

//for preventing actions the normal permissions system would allow
function efGPManagerExtendedPermissionsRevoke($title, $user, $action, &$result) {
	global $wgRequest;
	$result = null;
	$err = array('badaccess-group0');
	if( $action == 'read' ) {
		if( $title->isSpecial('Recentchanges') && !$user->isAllowed('recentchanges') ) {
			$result = $err;
			return false;
		}
		if( $title->isSpecial('Search') && !$user->isAllowed('search') ) {
			$result = $err;
			return false;
		}
		if( $title->isSpecial('Contributions') && !$user->isAllowed('contributions') ) {
			$result = $err;
			return false;
		}
		if( $wgRequest->getVal('action') == 'edit' || $wgRequest->getVal('action') == 'submit' ) {
			if( !$title->userCan('edit') && !$user->isAllowed('viewsource') ) {
				$result = $err;
				return false;
			}
		}
		if( $wgRequest->getVal('action') == 'history' && !$user->isAllowed('history') ) {
			$result = $err;
			return false;
		}
		if( $wgRequest->getVal('action') == 'raw' && !$user->isAllowed('raw') ) {
			$result = $err;
			return false;
		}
		if( $wgRequest->getVal('action') == 'render' && !$user->isAllowed('render') ) {
			$result = $err;
			return false;
		}
		if( $wgRequest->getVal('action') == 'credits' && !$user->isAllowed('credits') ) {
			$result = $err;
			return false;
		}
		if( $wgRequest->getVal('action') == 'info' && !$user->isAllowed('info') ) {
			$result = $err;
			return false;
		}
	}
	if( $action == 'edit' ) {
		if($title->exists() && ($wgRequest->getVal('action') == 'edit' || $wgRequest->getVal('action') == 'submit')) {
			if( $title->isTalkPage() && !$user->isAllowed('edittalk') ) {
				$result = $err;
				return false;
			} elseif( !$title->isTalkPage() && !$user->isAllowed('edit') ) {
				$result = $err;
				return false;
			}
		}
	}
	return true; //otherwise we don't care
}

//replace right-edit messages with right-edit-new wherever applicable
function efGPManagerReplaceEditMessage(&$key, &$useDB, &$langCode, &$transform) {
	if($key == 'right-edit') {
		$key = 'right-edit-new';
		return false; //so it doesn't change load times TOO much
	}
	return true;
}

//Since the one in Title.php is private...
function getTitleProtection($title) {
	// Can't protect pages in special namespaces
	if ( $title->getNamespace() < 0 ) {
		return false;
	}

	$dbr = wfGetDB( DB_SLAVE );
	$res = $dbr->select( 'protected_titles', '*',
		array ('pt_namespace' => $title->getNamespace(), 'pt_title' => $title->getDBkey()) );

	if ($row = $dbr->fetchRow( $res )) {
		return $row;
	} else {
		return false;
	}
}

//was added in 1.13, so supporting for downwards compatibility with 1.12
function addScriptFile( $file ) {
		global $wgStylePath, $wgStyleVersion, $wgJsMimeType, $wgOut;
		if( substr( $file, 0, 1 ) == '/' ) {
			$path = $file;
		} else {
			$path =  "{$wgStylePath}/common/{$file}";
		}
		$encPath = htmlspecialchars( $path );
		$wgOut->addScript( "<script type=\"{$wgJsMimeType}\" src=\"$path?$wgStyleVersion\"></script>\n" );
}
