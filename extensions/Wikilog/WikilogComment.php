<?php
/**
 * MediaWiki Wikilog extension
 * Copyright © 2008, 2009 Juliano F. Ravasi
 * http://www.mediawiki.org/wiki/Extension:Wikilog
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

/**
 * @addtogroup Extensions
 * @author Juliano F. Ravasi < dev juliano info >
 */

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * Wikilog article comment database entry.
 */
class WikilogComment
{
	/**
	 * Comment statuses.
	 */
	const S_OK				= 'OK';			///< Comment is published.
	const S_PENDING			= 'PENDING';	///< Comment is pending moderation.
	const S_DELETED			= 'DELETED';	///< Comment was removed.

	/**
	 * Mapping of comment statuses to readable messages. System messages are
	 * "wikilog-comment-{$statusMap[$status]}", except when false (for S_OK).
	 */
	public static $statusMap = array(
		self::S_OK				=> false,
		self::S_PENDING			=> 'pending',
		self::S_DELETED			=> 'deleted',
	);

	/**
	 * Wikilog article item this comment is associated to.
	 */
	public  $mItem			= NULL;

	/**
	 * General data about the comment.
	 */
	public  $mID			= NULL;		///< Comment ID.
	public  $mParent		= NULL;		///< Parent comment ID.
	public  $mThread		= NULL;		///< Comment thread.
	public  $mUserID		= NULL;		///< Comment author user id.
	public  $mUserText		= NULL;		///< Comment author user name.
	public  $mAnonName		= NULL;		///< Comment anonymous author name.
	public  $mStatus		= NULL;		///< Comment status.
	public  $mTimestamp		= NULL;		///< Date the comment was published.
	public  $mUpdated		= NULL;		///< Date the comment was last updated.
	public  $mCommentPage	= NULL;		///< Comment page id.
	public  $mCommentTitle  = NULL;		///< Comment page title.
	public  $mCommentRev	= NULL;		///< Comment revision id.
	public  $mText			= NULL;		///< Comment text.

	/**
	 * Whether the text was changed, and thus a database update is required.
	 */
	private $mTextChanged	= false;

	/**
	 * Constructor.
	 */
	public function __construct( WikilogItem &$item ) {
		$this->mItem = $item;
	}

	/**
	 * Returns the wikilog comment id.
	 */
	public function getID() {
		return $this->mID;
	}

	/**
	 * Set the author of the comment to the given (authenticated) user.
	 *
	 * This function can also be used when $user->getId() == 0
	 * (i.e. anonymous). In this case, a call to $this->setAnon() should
	 * follow, in order to set the anonymous name.
	 */
	public function setUser( $user ) {
		$this->mUserID = $user->getId();
		$this->mUserText = $user->getName();
		$this->mAnonName = NULL;
	}

	/**
	 * Set the anonymous (i.e. not logged in) author name.
	 */
	public function setAnon( $name ) {
		$this->mAnonName = $name;
	}

	/**
	 * Returns the wikitext of the comment.
	 */
	public function getText() {
		return $this->mText;
	}

	/**
	 * Changes the wikitext of the comment.
	 */
	public function setText( $text ) {
		$this->mText = $text;
		$this->mTextChanged = true;
	}

	/**
	 * Returns whether the comment is visible (not pending or deleted).
	 */
	public function isVisible() {
		return $this->mStatus == self::S_OK;
	}

	/**
	 * Returns whether the comment text is changed (DB update required).
	 */
	public function isTextChanged() {
		return $this->mTextChanged;
	}

	/**
	 * Load current revision of comment wikitext.
	 */
	public function loadText() {
		$dbr = wfGetDB( DB_SLAVE );
		$rev = Revision::loadFromId( $dbr, $this->mCommentRev );
		$this->mText = $rev->getText();
		$this->mTextChanged = false;
	}

	/**
	 * Saves comment data in the database.
	 */
	public function saveComment() {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();

		$data = array(
			'wlc_parent'    => $this->mParent,
			'wlc_post'      => $this->mItem->getID(),
			'wlc_user'      => $this->mUserID,
			'wlc_user_text' => $this->mUserText,
			'wlc_anon_name' => $this->mAnonName,
			'wlc_status'    => $this->mStatus,
			'wlc_timestamp' => $dbw->timestamp( $this->mTimestamp ),
			'wlc_updated'   => $dbw->timestamp( $this->mUpdated )
		);

		$delayed = array();

		# Main update.
		if ( $this->mID ) {
			$dbw->update( 'wikilog_comments', $data,
				array( 'wlc_id' => $this->mID ), __METHOD__ );
		} else {
			$cid = $dbw->nextSequenceValue( 'wikilog_comments_wlc_id' );
			$data = array( 'wlc_id' => $cid ) + $data;
			$dbw->insert( 'wikilog_comments', $data, __METHOD__ );
			$this->mID = $dbw->insertId();

			# Now that we have an ID, we can generate the thread.
			$this->mThread = self::getThreadHistory( $this->mID, $this->mParent );
			$delayed['wlc_thread'] = implode( '/', $this->mThread );
		}

		# Save article with comment text.
		if ( $this->mTextChanged ) {
			$this->mCommentTitle = $this->getCommentArticleTitle();
			$art = new Article( $this->mCommentTitle );
			$art->doEdit( $this->mText, $this->getAutoSummary() );
			$this->mTextChanged = false;

			$this->mCommentPage = $art->getID();
			$delayed['wlc_comment_page'] = $this->mCommentPage;
		}

		# Delayed updates.
		if ( !empty( $delayed ) ) {
			$dbw->update( 'wikilog_comments', $delayed,
				array( 'wlc_id' => $this->mID ), __METHOD__ );
		}

		# Update number of comments
		$this->mItem->updateNumComments( true );

		# Commit
		$dbw->commit();

		# Invalidate some caches.
		$this->mCommentTitle->invalidateCache();
		$this->mItem->mTitle->invalidateCache();
		$this->mItem->mTitle->getTalkPage()->invalidateCache();
		$this->mItem->mParentTitle->invalidateCache();
	}

	/**
	 * Deletes comment data from the database.
	 */
	public function deleteComment() {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();

		$dbw->delete( 'wikilog_comments', array( 'wlc_id' => $this->mID ), __METHOD__ );
		$this->mItem->updateNumComments( true );

		$dbw->commit();

		$this->mItem->mTitle->invalidateCache();
		$this->mItem->mTitle->getTalkPage()->invalidateCache();
		$this->mItem->mParentTitle->invalidateCache();
		$this->mID = NULL;
	}

	/**
	 * Returns comment article title.
	 */
	public function getCommentArticleTitle() {
		if ( $this->mCommentTitle ) {
			return $this->mCommentTitle;
		} else if ( $this->mCommentPage ) {
			return Title::newFromID( $this->mCommentPage, GAID_FOR_UPDATE );
		} else {
			$it = $this->mItem->mTitle;
			return Title::makeTitle(
				MWNamespace::getTalk( $it->getNamespace() ),
				$it->getText() . '/c' . self::padID( $this->mID )
			);
		}
	}

	/**
	 * Returns automatic summary (for recent changes) for the posted comment.
	 */
	public function getAutoSummary() {
		global $wgContLang;
		$user = $this->mUserID ? $this->mUserText : $this->mAnonName;
		$summ = $wgContLang->truncate( str_replace( "\n", ' ', $this->mText ),
			max( 0, 200 - strlen( wfMsgForContent( 'wikilog-comment-autosumm' ) ) ),
			'...' );
		return wfMsgForContent( 'wikilog-comment-autosumm', $user, $summ );
	}

	/**
	 * Returns the discussion history for a given comment. This is used to
	 * populate the $comment->mThread of a new comment whose id is @a $id
	 * and parent is @a $parent.
	 *
	 * @param $id Comment id of the new comment.
	 * @param $parent Comment id of its parent.
	 * @return Array of ids from the history since the first comment until
	 *   the given one.
	 */
	public static function getThreadHistory( $id, $parent ) {
		$thread = array();

		if ( $parent ) {
			$dbr = wfGetDB( DB_SLAVE );
			$thread = $dbr->selectField(
				'wikilog_comments',
				'wlc_thread',
				array( 'wlc_id' => intval( $parent ) ),
				__METHOD__
			);
			if ( $thread !== false ) {
				$thread = explode( '/', $thread );
			} else {
				throw new MWException( 'Invalid parent history.' );
			}
		}

		$thread[] = self::padID( $id );
		return $thread;
	}

	/**
	 * Formats the id of a comment as a string, padding it with zeros if
	 * necessary.
	 */
	public static function padID( $id ) {
		return str_pad( intval( $id ), 6, '0', STR_PAD_LEFT );
	}

	/**
	 * Creates a new comment object from a database row.
	 * @param $row Row from database.
	 * @return New WikilogComment object.
	 */
	public static function newFromRow( &$item, $row ) {
		$comment = new WikilogComment( $item );
		$comment->mID           = intval( $row->wlc_id );
		$comment->mParent       = intval( $row->wlc_parent );
		$comment->mThread       = explode( '/', $row->wlc_thread );
		$comment->mUserID       = intval( $row->wlc_user );
		$comment->mUserText     = strval( $row->wlc_user_text );
		$comment->mAnonName     = strval( $row->wlc_anon_name );
		$comment->mStatus       = strval( $row->wlc_status );
		$comment->mTimestamp    = wfTimestamp( TS_MW, $row->wlc_timestamp );
		$comment->mUpdated      = wfTimestamp( TS_MW, $row->wlc_updated );
		$comment->mCommentPage  = $row->wlc_comment_page;

		# This information may not be available for deleted comments.
		if ( $row->page_title && $row->page_latest ) {
			$comment->mCommentTitle = Title::makeTitle( $row->page_namespace, $row->page_title );
			$comment->mCommentRev = $row->page_latest;
		}
		return $comment;
	}

	/**
	 * Creates a new comment object for a new comment, given the text and
	 * the parent comment.
	 * @param $item Wikilog article object this is a comment for.
	 * @param $text Comment wikitext as a string.
	 * @param $parent Parent comment id.
	 * @return New WikilogComment object.
	 */
	public static function newFromText( &$item, $text, $parent = NULL ) {
		$ts = wfTimestamp( TS_MW );
		$comment = new WikilogComment( $item );
		$comment->mParent    = $parent;
		$comment->mStatus    = self::S_OK;
		$comment->mTimestamp = $ts;
		$comment->mUpdated   = $ts;
		$comment->setText( $text );
		return $comment;
	}

	/**
	 * Creates a new comment object from an existing comment id.
	 * Data is fetched from the database.
	 * @param $item Wikilog article item.
	 * @param $id Comment id.
	 * @return New WikilogComment object, or NULL if comment doesn't exist.
	 */
	public static function newFromID( &$item, $id ) {
		$dbr = wfGetDB( DB_SLAVE );
		$row = self::loadFromID( $dbr, $id );
		if ( $row ) {
			return self::newFromRow( $item, $row );
		}
		return NULL;
	}

	/**
	 * Creates a new comment object from an existing comment page id.
	 * Data is fetched from the database.
	 * @param $item Wikilog article item.
	 * @param $pageid Comment page id.
	 * @return New WikilogComment object, or NULL if comment doesn't exist.
	 */
	public static function newFromPageID( &$item, $pageid ) {
		$dbr = wfGetDB( DB_SLAVE );
		$row = self::loadFromPageID( $dbr, $pageid );
		if ( $row && $row->wlc_post == $item->getID() ) {
			return self::newFromRow( $item, $row );
		}
		return NULL;
	}

	/**
	 * Load information about a comment from the database given a set of
	 * conditions.
	 * @param $dbr Database connection object.
	 * @param $conds Conditions.
	 * @return Database row, or false.
	 */
	private static function loadFromConds( $dbr, $conds ) {
		extract( self::selectInfo( $dbr ) );	// $tables, $fields
		$row = $dbr->selectRow(
			$tables,
			$fields,
			$conds,
			__METHOD__,
			array( )
		);
		return $row;
	}

	/**
	 * Load information about a comment from the database given a set a
	 * comment id.
	 * @param $dbr Database connection object.
	 * @param $id Comment id.
	 * @return Database row, or false.
	 */
	private static function loadFromID( $dbr, $id ) {
		return self::loadFromConds( $dbr, array( 'wlc_id' => $id ) );
	}

	/**
	 * Load information about a comment from the database given a set of
	 * conditions.
	 * @param $dbr Database connection object.
	 * @param $pageid Comment page id.
	 * @return Database row, or false.
	 */
	private static function loadFromPageID( $dbr, $pageid ) {
		return self::loadFromConds( $dbr, array( 'wlc_comment_page' => $pageid ) );
	}

	/**
	 * Fetch all comments given a set of conditions.
	 * @param $dbr Database connection object.
	 * @param $conds Query conditions.
	 * @param $options Query options.
	 * @return Database query result object.
	 */
	private static function fetchFromConds( $dbr, $conds, $options = array() ) {
		extract( self::selectInfo( $dbr ) );	// $tables, $fields
		$result = $dbr->select(
			$tables,
			$fields,
			$conds,
			__METHOD__,
			$options
		);
		return $result;
	}

	/**
	 * Fetch all comments given a wikilog article item.
	 * @param $dbr Database connection object.
	 * @param $itemid Wikilog article item id.
	 * @return Database query result object.
	 */
	public static function fetchAllFromItem( $dbr, $itemid ) {
		return self::fetchFromConds( $dbr,
			array( 'wlc_post' => $itemid ),
			array( 'ORDER BY' => 'wlc_thread, wlc_id' )
		);
	}

	/**
	 * Fetch all comments given a wikilog article item and a thread.
	 * @param $dbr Database connection object.
	 * @param $itemid Wikilog article item id.
	 * @param $thread Thread description (array of comment ids).
	 * @return Database query result object.
	 */
	public static function fetchAllFromItemThread( $dbr, $itemid, $thread ) {
		if ( is_array( $thread ) ) {
			$thread = implode( '/', $thread );
		}
		$thread = $dbr->escapeLike( $thread );
		return self::fetchFromConds( $dbr,
			array( 'wlc_post' => $itemid, "wlc_thread LIKE '{$thread}/%'" ),
			array( 'ORDER BY' => 'wlc_thread, wlc_id' )
		);
	}

	/**
	 * Returns the tables and fields used for database queries for comment
	 * objects.
	 * @param $dbr Database connection object.
	 * @return Array(2) with the description of the tables and fields to be
	 *   used in database queries.
	 */
	private static function selectInfo( $dbr ) {
		extract( $dbr->tableNames( 'wikilog_comments', 'page' ) );
		return array(
			'tables' =>
				"{$wikilog_comments} " .
				"LEFT JOIN {$page} ON (page_id = wlc_comment_page)",
			'fields' => array(
				'wlc_id',
				'wlc_parent',
				'wlc_thread',
				'wlc_post',
				'wlc_user',
				'wlc_user_text',
				'wlc_anon_name',
				'wlc_status',
				'wlc_timestamp',
				'wlc_updated',
				'wlc_comment_page',
				'page_namespace',
				'page_title',
				'page_latest'
			)
		);
	}
}
