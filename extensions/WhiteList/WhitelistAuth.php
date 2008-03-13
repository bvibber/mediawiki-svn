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

define("WHITELIST_GRANT", 1);
define("WHITELIST_DENY", -1);
define("WHITELIST_NOACTION", 0);

class WhitelistExec
{

	/* $result value:
	 *   true=Access Granted
	 *   false=Access Denied
	 *   null=Don't know/don't care (not 'allowed' or 'denied')
	 * Return value:
	 *   true=Later functions can override.
	 *   false=Later functions not consulted.
	 */
	static function CheckWhitelist(&$title, &$wgUser, $action, &$result) {

		global $wgWhiteListRestrictedRight;

		$override = WHITELIST_NOACTION;

		/* Bail if the user isn't restricted.... */
		if( !in_array($wgWhiteListRestrictedRight, $wgUser->getRights()) ) {
			$result = null; /* don't care */
			return true; /* Later functions can override */
		}

		/* Sanity Check */
		if (!$title)
			return $hideMe;

		/* Check global allow/deny lists */
		$override = self::GetOverride($title, $action);

		/* Check if user page */
		if( WHITELIST_NOACTION == $override )
			$override = self::IsUserPage( $title->GetPrefixedText(), $wgUser );

		/* Handle special pages */
		if( WHITELIST_NOACTION == $override )
			$override = self::IsAllowedSpecialPage( $title, $wgUser );

		/* Check if page is on whitelist */
		if( WHITELIST_NOACTION == $override )
			$override = self::IsAllowed( $title, $wgUser, $action );

		switch( $override )
		{
			case WHITELIST_GRANT:
				$result = true; /* Allow other checks to be run */
				return true; /* Later functions can override */
				break;
			case WHITELIST_DENY:
			case WHITELIST_NOACTION:
			default: /* Invalid - shouldn't be possible... */
				$result = false; /* Access Denied */
				return false; /* Later functions not consulted */
		}
	}

	/* Check for global page overrides (allow or deny)
	 */
	static function GetOverride($title, $action )
	{
		global $wgWhitelistOverride;

                $allowView = $allowEdit = $denyView = $denyEdit = false;
 
                foreach( $wgWhitelistOverride['always']['read'] as $value )
                {
                        if( self::RegexCompare($title, $value) )
                        {
                                $allowView = true;
                        }
                }
 
                foreach( $wgWhitelistOverride['always']['edit'] as $value )
                {
                        if( self::RegexCompare($title, $value) )
                        {
                                $allowEdit = true;
                        }
                }

		$override = undef;

		foreach( $wgWhitelistOverride['never']['read'] as $value )
                {
                        if( self::RegexCompare($title, $value) )
                        {
                                $denyView = true;
                        }
                }
 
                foreach( $wgWhitelistOverride['never']['edit'] as $value )
                {
                        if( self::RegexCompare($title, $value) )
                        {
                                $denyEdit = true;
                        }
                }

		if( $action == 'edit' )
		{
			if( $denyEdit || $denyView )
				$override = WHITELIST_DENY;
			else if( $allowEdit )
				$override = WHITELIST_GRANT;
			else
				$override = WHITELIST_NOACTION;
		}
		else
		{
			if( $denyView )
				$override = WHITELIST_DENY;
			else if( $allowView || $allowEdit )
				$override = WHITELIST_GRANT;
			else
				$override = WHITELIST_NOACTION;
		}

		return $override;
	}

	/* Allow access to user pages (unless disabled)
	 */
	static function IsUserPage( $title_text, &$wgUser )
	{
		global $wgWhitelistAllowUserPages;

		$userPage = $wgUser->getUserPage()->getPrefixedText();
		$userTalkPage = $wgUser->getTalkPage()->getPrefixedText();

		if( ($wgWhitelistAllowUserPages == true) &&
			($title_text == $userPage) || ($title_text == $userTalkPage) )
			return WHITELIST_GRANT;
		else
			return WHITELIST_NOACTION;
	}

	/* Special page wildcard notes:
	 *  - Special: namespace entries can either be the exact name of
	 *    a page, or Special:*. Other entries will be ignored.
	 *    Action is ignored for these pages.
	 */
	static function IsAllowedSpecialPage( &$title, &$wgUser )
	{
		global $wgContLanguageCode;

		$dbr = wfGetDB( DB_SLAVE );

		if( NS_SPECIAL == $title->getNamespace() )
			{
			/* Get localized Special: namespace text */
			$lang = Language::Factory($wgContLanguageCode);
			$special_ns_text = $lang->getNsText( $title->getNamespace() );

			/* Check for wildcard (Special:%) */
			$db_result = $dbr->selectRow('whitelist',
				array( 'wl_allow_edit',
				'wl_expires_on', ),
				array( 'wl_user_id' => $wgUser->getId(),
				'wl_page_title' => $special_ns_text . ":%" ),
				__METHOD__
			);
			if( false != $db_result )
				return 1; /* Allow */

			/* Check for exact page name */
			$db_result = $dbr->selectRow('whitelist',
				array( 'wl_allow_edit',
				'wl_expires_on', ),
				array( 'wl_user_id' => $wgUser->getId(),
				'wl_page_title' => $title->getPrefixedText() ),
				__METHOD__
			);
			if( false != $db_result )
				return WHITELIST_GRANT;
		}

		/* No hits */
		return WHITELIST_NOACTION;
	}

	/* Check whether the page is whitelisted.
	 * returns true if page is on whitelist, false if it is not.
	 */
	static function IsAllowed( &$title, &$wgUser, $action )
	{
		if( NS_MAIN <= $title->getNamespace() )
		{

			/* Get all valid database rows for the user.
			 * Throw out any results which do not give sufficient
			 * privilege for the current action.
			 */
			$dbr = wfGetDB( DB_SLAVE );

			/* Query Parameters */
			$db_return_cols = array( 'wl_id',
								     'wl_page_title',
									 'wl_expires_on' );
			$db_conditions = array( 'wl_user_id' => $wgUser->getId() );

			/* If editing, only get entries with edit privileges */
			if( $action == 'edit' )
				array_push( $db_conditions, 'wl_allow_edit', '1' );

			/* Do the query */
			$db_results = $dbr->select('whitelist', $db_return_cols,
				$db_conditions, __METHOD__ );

			/* Loop through each result returned and
			 * check for matches.
			 */
			while( $db_result = $dbr->fetchObject($db_results) )
			{
				/* Check for expired privilege */
				$expired = strtotime($db_result->wl_expires_on) > strtotime(date("Y-m-d H:i:s"));

				/* Check page title against regex */
				if( ! $expired )
				{
					if( self::RegexCompare($title, $db_result->wl_page_title) )
					{
						$dbr->freeResult($db_results);
						return WHITELIST_GRANT;
					}
				}
			}
			$dbr->freeResult($db_results);
		}
		return WHITELIST_NOACTION;
	}

	/* Returns true if hit, false otherwise */
	static function RegexCompare(&$title, $sql_regex)
	{
		if( $title->exists() )
		{
			$matches = WhitelistEdit::ExpandWildCardWhiteList( $sql_regex );
			foreach( $matches as $match )
			{
				$match_title = Title::newFromId($match);
				if( $match_title->getPrefixedText() == $title->getPrefixedText() )
				return true;
			}
		}
		else
		{
			/* Convert regex to PHP format */
			$php_regex = str_replace('%', '*', $sql_regex);

			/* Generate regex; use | as delimiter as it is an illegal title character. */
			$php_regex_full = $wgWhitelistWildCardInsensitive ?
				'|' . $php_regex . '|i' : '|' . $php_regex . '|';


			if (self::preg_test($php_regex_full))
			if( preg_match( $php_regex_full, $title->getPrefixedText() ) )
			return true;
		}
	}

	# test to see if a regular expression is valid
	function preg_test($regex)
	{
		if (sprintf("%s",@preg_match($regex,'')) == '')
		{
			$error = error_get_last();
			return false;
		}
		else
		return true;
	}
} /* End class */
