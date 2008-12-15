<?php

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation, version 2
of the License.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * A file for the WhiteList extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Paul Grinberg <gri6507@yahoo.com>
 * @author Mike Sullivan <ms-mediawiki@umich.edu>
 * @copyright Copyright © 2008, Paul Grinberg, Mike Sullivan
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
 
function wfCheckWhitelist(&$title, &$wgUser, $action, &$result) {
	global $wgWhiteListRestrictedRight;
	global $wgWhitelistOverride;

        $hideMe = false;
	$pageOverride = false;

	$useWhitelist = in_array( $wgWhiteListRestrictedRight, $wgUser->getRights() );

	/* If page isn't blacklisted, and user is a contractor,
	 * only allow access IF user is available.
	 */
	if( $useWhitelist ) {
		
		if( !isset($wgWhitelistOverride['always']['read']) )
			$wgWhitelistOverride['always']['read'] = array();

		if( !isset($wgWhitelistOverride['always']['edit']) )
			$wgWhitelistOverride['always']['edit'] = array($wgUser->getUserPage()->getPrefixedText(), $wgUser->getTalkPage()->getPrefixedText());
		
		if( !isset($wgWhitelistOverride['never']['read']) )
			$wgWhitelistOverride['never']['read'] = array();
		
		if( !isset($wgWhitelistOverride['never']['edit']) )
			$wgWhitelistOverride['never']['edit'] = array();
		
		if (!$title)
                        return $hideMe;
                        
		/* Check if never allowed - check both read and write to be extra safe */
		if( ('edit' == $action) && (in_array( $title->getPrefixedText(), $wgWhitelistOverride['never']['edit'])) )
		{
			$hideMe |= true;
			$pageOverride = true;
			
		}
		if( in_array($title->getPrefixedText(), $wgWhitelistOverride['never']['read']) )
		{
			/* Block edits if read is blacklisted */
			$hideMe |= true;
			$pageOverride = true;
		}

		/* Check if always allowed (note that 'never' takes precedence).
		 * If 'edit' permission, 'read' is assumed.  */
		if( ('read' == $action) && (in_array( $title->getPrefixedText(), $wgWhitelistOverride['always']['read'])) )
		{
			$pageOverride = true;
			
		}
		else if( in_array($title->getPrefixedText(), $wgWhitelistOverride['always']['edit']) )
		{
			$pageOverride = true;
		}

		if( !$pageOverride )
		{
			/* Get database row for user/title combination.
			 * Assumes that either zero or one row is defined for a particular
			 * user and title combination. This is enforced by the front end.
			 * Behavior is undefined if more than one row is returned.
			 */
			$dbr = wfGetDB( DB_SLAVE );
			$result = $dbr->selectRow('whitelist',
				array( 'wl_allow_edit',
				       'wl_expires_on', ),
			      	array( 'wl_user_id' => $wgUser->getId(),
			      	       'wl_page_title' => $title->getPrefixedText() ),
				__METHOD__
			);
	
			/* Deny access if no entry returned. */
			$hideMe |= ( $result == false );
	
			/* Deny access if entry has expired. */
			if( $result ) {
				if( NULL != $result->wl_expires_on ) {
					$permissionHasExpired = ( strtotime($result->wl_expires_on) < strtotime(date("Y-m-d H:i:s")) );
					$hideMe |= $permissionHasExpired;
				}
		
				/* Deny access if attempting edit without permission. */
				$hideMe |= ( ($action=='edit') && ($result->wl_allow_edit == '0') );
			}
		}
	}
	
        if($hideMe)
                $result = !$hideMe;
 
        return !$hideMe;
}


