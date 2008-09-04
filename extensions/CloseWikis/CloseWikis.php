<?php
/*
 * Copyright (C) 2008 Victor Vasiliev <vasilvv@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

if ( !defined( 'MEDIAWIKI' ) )
	die();

$wgExtensionCredits['other'][] = array(
	'name'           => 'CloseWikis',
	'author'         => 'Victor Vasiliev',
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'description'    => 'Allows to close wiki sites',
	'descriptionmsg' => 'closewikis-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:CloseWikis',
);

$dir = dirname( __FILE__ );
$wgExtensionMessagesFiles['CloseWikis'] =  "$dir/CloseWikis.i18n.php";
$wgExtensionAliasesFiles['CloseWikis'] = "$dir/CloseWikis.alias.php";
$wgHooks['getUserPermissionsErrors'][] = "CloseWikis::userCan";

$wgGroupPermissions['steward']['closewikis'] = true;
$wgAvailableRights[] = 'closewikis';
// To be promoted globally
$wgAvailableRights[] = 'editclosedwikis';

$wgAutoloadClasses['SpecialCloseWiki'] = "$dir/CloseWikis.page.php";
$wgSpecialPages['CloseWiki'] = 'SpecialCloseWiki';

$wgCloseWikisDatabase = 'closedwikis';

$wgLogTypes[]                     = 'closewiki';
$wgLogNames['closewiki']          = 'closewikis-log';
$wgLogHeaders['closewiki']        = 'closewikis-log-header';
$wgLogActions['closewiki/close']  = 'closewikis-log-close';
$wgLogActions['closewiki/reopen'] = 'closewikis-log-reopen';

class CloseWikis {
	static function getSlaveDB() {
		global $wgCloseWikisDatabase;
		return wfGetDB( DB_SLAVE, 'closewikis', $wgCloseWikisDatabase );
	}

	static function getMasterDB() {
		global $wgCloseWikisDatabase;
		return wfGetDB( DB_MASTER, 'closewikis', $wgCloseWikisDatabase );
	}

	static function getList() {
		global $wgMemc;
		$cached = $wgMemc->get( 'closedwikis' );
		if( is_array( $cached ) )
			return $cached;
		$list = array();
		$dbr = self::getSlaveDB();
		$result = $dbr->select( 'closedwikis', '*', false, __METHOD__ );
		foreach( $result as $row ) {
			$list[$row->cw_wiki] = $row->cw_reason;
		}
		$dbr->freeResult( $result );
		$wgMemc->set( 'closedwikis', $list );
		return $list;
	}

	static function getUnclosedList() {
		global $wgLocalDatabases;
		return array_diff( $wgLocalDatabases, array_keys( self::getList() ) );
	}

	static function userCan( &$title, &$user, $action, &$result ) {
		$list = self::getList();
		if( isset( $list[wfWikiID()] ) && !$user->isAllowed( 'editclosedwikis' ) ) {
			wfLoadExtensionMessages( 'CloseWikis' );
			$reason = $list[wfWikiID()];
			$result[] = $reason ?
				array( 'closewikis-closed', $reason ) :
				array( 'closewikis-closed-default' );
			return false;
		}
		return true;
	}
}
