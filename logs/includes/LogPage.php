<?php
#
# Copyright (C) 2002, 2004 Brion Vibber <brion@pobox.com>
# http://www.mediawiki.org/
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
# http://www.gnu.org/copyleft/gpl.html

/**
 * Contain log classes
 *
 */

/**
 * Class to simplify the use of log pages.
 * The logs are now kept in a table which is easier to manage and trim
 * than ever-growing wiki pages.
 *
 */
class LogPage {

	/**
	 * Log type
	 */
	private $type = '';

	/**
	 * Should we update recent changes when adding a
	 * new log item?
	 */
	private $updateRecentChanges = true;

	/**
	  * Constructor
	  *
	  * @param string $type Log type
	  * @param bool $rc Update recent changes?
	  */
	public function __construct( $type, $rc = true ) {
		$this->type = $type;
		$this->updateRecentChanges = $rc;
	}

	/**
	 * @static
	 */
	public static function validTypes() {
		global $wgLogTypes;
		return $wgLogTypes;
	}

	/**
	 * @static
	 */
	public static function isLogType( $type ) {
		return in_array( $type, self::validTypes() );
	}

	/**
	 * @static
	 */
	public static function logName( $type ) {
		global $wgLogNames;

		if( isset( $wgLogNames[$type] ) ) {
			return str_replace( '_', ' ', wfMsg( $wgLogNames[$type] ) );
		} else {
			// Bogus log types? Perhaps an extension was removed.
			return $type;
		}
	}

	/**
	 * @todo handle missing log types
	 * @static
	 */
	static function logHeader( $type ) {
		global $wgLogHeaders;
		return wfMsg( $wgLogHeaders[$type] );
	}


	/**
	static function actionText( $type, $action, $title = NULL, $skin = NULL, $params = array(), $filterWikilinks=false, $translate=false ) {
		global $wgLang, $wgContLang, $wgLogActions;

		$key = "$type/$action";
		
		if( $key == 'patrol/patrol' )
			return PatrolLog::makeActionText( $title, $params, $skin );
		
		if( isset( $wgLogActions[$key] ) ) {
			if( is_null( $title ) ) {
				$rv=wfMsg( $wgLogActions[$key] );
			} else {
				if( $skin ) {

					switch( $type ) {
						case 'move':
							$titleLink = $skin->makeLinkObj( $title, $title->getPrefixedText(), 'redirect=no' );
							$params[0] = $skin->makeLinkObj( Title::newFromText( $params[0] ), $params[0] );
							break;
						case 'rights':
							$text = $wgContLang->ucfirst( $title->getText() );
							$titleLink = $skin->makeLinkObj( Title::makeTitle( NS_USER, $text ) );
							break;
						default:
							$titleLink = $skin->makeLinkObj( $title );
					}

				} else {
					$titleLink = $title->getPrefixedText();
				}
				if( $key == 'rights/rights' ) {
					if ($skin) {
						$rightsnone = wfMsg( 'rightsnone' );
					} else {
						$rightsnone = wfMsgForContent( 'rightsnone' );
					}
					if( !isset( $params[0] ) || trim( $params[0] ) == '' )
						$params[0] = $rightsnone;
					if( !isset( $params[1] ) || trim( $params[1] ) == '' )
						$params[1] = $rightsnone;
				}
				if( count( $params ) == 0 ) {
					if ( $skin ) {
						$rv = wfMsg( $wgLogActions[$key], $titleLink );
					} else {
						$rv = wfMsgForContent( $wgLogActions[$key], $titleLink );
					}
				} else {
					array_unshift( $params, $titleLink );
					$rv = wfMsgReal( $wgLogActions[$key], $params, true, !$skin );
				}
			}
		} else {
			wfDebug( "LogPage::actionText - unknown action $key\n" );
			$rv = "$action";
		}
		if( $filterWikilinks ) {
			$rv = str_replace( "[[", "", $rv );
			$rv = str_replace( "]]", "", $rv );
		}
		return $rv;
	}*/

	/**
	 * Insert a new log row, updating recent changes if
	 * we've been asked to do so
	 *
	 * @param string $action Log action
	 * @param Title $target Associated title object
	 * @param string $comment Log comment/rationale
	 * @param array $params Log parameters
	 */
	public function addEntry( $action, $target, $comment, $params = array() ) {
		global $wgUser;
		wfProfileIn( __METHOD__ );
		
		if( wfReadOnly() ) {
			wfProfileOut( __METHOD__ );
			return false;
		}

		if( !is_array( $params ) )
			$params = array( $params );

		$dbw = wfGetDB( DB_MASTER );
		$now = wfTimestampNow();
		$log_id = $dbw->nextSequenceValue( 'log_log_id_seq' );

		$data = array(
			'log_type' => $this->type,
			'log_action' => $action,
			'log_timestamp' => $dbw->timestamp( $now ),
			'log_user' => $wgUser->getId(),
			'log_namespace' => $target->getNamespace(),
			'log_title' => $target->getDBkey(),
			'log_comment' => $comment,
			'log_params' => self::makeParamBlob( $params ),
		);

		# log_id doesn't exist on Wikimedia servers yet, and it's a tricky 
		# schema update to do. Hack it for now to ignore the field on MySQL.
		if ( !is_null( $log_id ) ) {
			$data['log_id'] = $log_id;
		}
		$dbw->insert( 'logging', $data, __METHOD__ );

		# Update recent changes if required
		if( $this->updateRecentChanges ) {
			RecentChange::notifyLog(
				$now,
				$target,
				$wgUser,
				$comment,
				'',
				$this->type,
				$action,
				$params
			);
		}

		wfProfileOut( __METHOD__ );
		return true;
	}

	/**
	 * Encode parameters into a BLOB-safe value
	 *
	 * @param array $params Parameters
	 * @return string
	 */
	public static function makeParamBlob( $params ) {
		return implode( "\n", $params );
	}

	/**
	 * Decode parameters from a BLOB value
	 *
	 * @param string $blob Parameter blob
	 * @return array
	 */
	public static function extractParams( $blob ) {
		return $blob === ''
			? array()
			: explode( "\n", $blob );
	}
	
}

?>