<?php
/**
 * SpecialLastUserLogin MediaWiki extension
 *
 * @file
 * @ingroup Extensions
 * @version 1.2.1
 * @author Justin G. Cramer
 * @author Danila Ulyanov
 * @author Thomas Klein
 * @link http://www.mediawiki.org/wiki/Extension:SpecialLastUserLoginEx Documentation
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */
 
if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}
 
// Extension credits that will show up on Special:Version
$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'LastUserLogin',
	'version' => '1.2.1',
	'author' => array( 'Justin G. Cramer', 'Danila Ulyanov', 'Thomas Klein' ),
	'url' => 'http://www.mediawiki.org/wiki/Extension:SpecialLastUserLoginEx',
	'description' => 'Displays the last time a user logged in',
	'descriptionmsg' => 'lastuserlogin-desc',
);
 
// New user right
$wgAvailableRights[] = 'lastlogin';
 
// Set up the new special page
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['LastUserLogin'] = $dir . 'LastUserLogin_body.php';
$wgExtensionMessagesFiles['LastUserLogin'] = $dir . 'LastUserLogin.i18n.php';
$wgSpecialPages['LastUserLogin'] = 'LastUserLogin';
 
// Function that updates the database when a user logs in
$wgExtensionFunctions[] = 'wfUpdateUserTouched';
 
function wfUpdateUserTouched() {
	global $wgOut, $wgCookiePrefix;
 
	if ( isset( $_COOKIE ) && isset( $_COOKIE["{$wgCookiePrefix}UserID"] ) ) {
		$dbw = wfGetDB( DB_MASTER );
		$query = "UPDATE " . $dbw->tableName( 'user' ) . " SET user_touched = '" . $dbw->timestamp() . "' WHERE user_id = " . intval( $_COOKIE["{$wgCookiePrefix}UserID"] );
		$dbw->doQuery( $query );
	}
}

