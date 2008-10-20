<?php
if (!defined('MEDIAWIKI')) die();

class CodeRevision {
	static function newFromSvn( CodeRepository $repo, $data ) {
		$rev = new CodeRevision();
		$rev->mRepoId = $repo->getId();
		$rev->mRepo = $repo;
		$rev->mId = intval($data['rev']);
		$rev->mAuthor = $data['author'];
		$rev->mTimestamp = wfTimestamp( TS_MW, strtotime( $data['date'] ) );
		$rev->mMessage = rtrim( $data['msg'] );
		$rev->mPaths = $data['paths'];
		$rev->mStatus = 'new';

		$common = null;
		if( $rev->mPaths ) {
			if (count($rev->mPaths) == 1)
				$common = $rev->mPaths[0]['path'];
			else {
				$first = array_shift( $rev->mPaths );

				$common = explode( '/', $first['path'] );

				foreach( $rev->mPaths as $path ) {
					$compare = explode( '/', $path['path'] );

					// make sure $common is the shortest path
					if ( count($compare) < count($common) )
						list( $compare, $common ) = array( $common, $compare );

					$tmp = array();
					foreach ( $common as $k => $v )
						if ( $v==$compare[$k] ) $tmp[]= $v;
						else break;
					$common = $tmp;
				}
				$common = implode( '/', $common);

				array_unshift( $rev->mPaths, $first );
			}
		}
		$rev->mCommonPath = $common;
		return $rev;
	}

	static function newFromRow( CodeRepository $repo, $row ) {
		$rev = new CodeRevision();
		$rev->mRepoId = intval($row->cr_repo_id);
		if( $rev->mRepoId != $repo->getId() ) {
			throw new MWException( "Invalid repo ID in " . __METHOD__ );
		}
		$rev->mRepo = $repo;
		$rev->mId = intval($row->cr_id);
		$rev->mAuthor = $row->cr_author;
		$rev->mTimestamp = wfTimestamp( TS_MW, $row->cr_timestamp );
		$rev->mMessage = $row->cr_message;
		$rev->mStatus = $row->cr_status;
		$rev->mCommonPath = $row->cr_path;
		return $rev;
	}

	function getId() {
		return intval( $this->mId );
	}
	
	function getRepoId() {
		return intval( $this->mRepoId );
	}

	function getAuthor() {
		return $this->mAuthor;
	}
	
	function getWikiUser() {
		return $this->mRepo->authorWikiUser( $this->getAuthor() );
	}

	function getTimestamp() {
		return $this->mTimestamp;
	}

	function getMessage() {
		return $this->mMessage;
	}
	
	function getStatus() {
		return $this->mStatus;
	}

	function getCommonPath() {
		return $this->mCommonPath;
	}
	
	static function getPossibleStates() {
		return array( 'new', 'fixme', 'resolved', 'ok', 'deferred' );
	}
	
	function isValidStatus( $status ) {
		return in_array( $status, self::getPossibleStates(), true );
	}
	
	function setStatus( $status ) {
		if( !$this->isValidStatus( $status ) ) {
			throw new MWException( "Tried to save invalid code revision status" );
		}
		
		$this->mStatus = $status;
		
		$dbw = wfGetDB( DB_MASTER );
		$dbw->update( 'code_rev',
			array( 'cr_status' => $status ),
			array(
				'cr_repo_id' => $this->mRepoId,
				'cr_id' => $this->mId ),
			__METHOD__ );
	}

	function save() {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();
		
		$dbw->insert( 'code_rev',
			array(
				'cr_repo_id' => $this->mRepoId,
				'cr_id' => $this->mId,
				'cr_author' => $this->mAuthor,
				'cr_timestamp' => $dbw->timestamp( $this->mTimestamp ),
				'cr_message' => $this->mMessage,
				'cr_status' => $this->mStatus,
				'cr_path' => $this->mCommonPath ),
			__METHOD__,
			array( 'IGNORE' ) );
		// Already exists? Update the row!
		if( !$dbw->affectedRows() ) {
			$dbw->update( 'code_rev',
				array(
					'cr_author' => $this->mAuthor,
					'cr_timestamp' => $dbw->timestamp( $this->mTimestamp ),
					'cr_message' => $this->mMessage,
					'cr_path' => $this->mCommonPath ), 
				array(
					'cr_repo_id' => $this->mRepoId,
					'cr_id' => $this->mId ),
				__METHOD__ );
		}
		// Update path tracking used for output and searching
		if( $this->mPaths ) {
			$data = array();
			foreach( $this->mPaths as $path ) {
				$data[] = array(
					'cp_repo_id' => $this->mRepoId,
					'cp_rev_id' => $this->mId,
					'cp_path' => $path['path'],
					'cp_action' => $path['action'] );
			}
			$dbw->insert( 'code_paths',
				$data,
				__METHOD__,
				array( 'IGNORE' ) );
		}

		$dbw->commit();
	}

	function getModifiedPaths(){
		$dbr = wfGetDB( DB_SLAVE );
		return $dbr->select(
			'code_paths',
			array( 'cp_path', 'cp_action' ),
			array( 'cp_repo_id' => $this->mRepoId, 'cp_rev_id' => $this->mId ),
			__METHOD__
		);
	}
	
	function isDiffable() {
		$paths = $this->getModifiedPaths();
		if( !$paths->numRows() || $paths->numRows() > 20 ) {
			return false; // things need to get done this year
		}
		return true;
	}

	function previewComment( $text, $review, $parent=null ) {
		$data = $this->commentData( $text, $review, $parent );
		$data['cc_id'] = null;
		return CodeComment::newFromData( $this, $data );
	}
	
	function saveComment( $text, $review, $parent=null ) {
		global $wgUser;
		if( !strlen($text) ) {
			return 0;
		}
		$dbw = wfGetDB( DB_MASTER );
		$data = $this->commentData( $text, $review, $parent );

		$dbw->begin();
		$data['cc_id'] = $dbw->nextSequenceValue( 'code_comment_cc_id' );
		$dbw->insert( 'code_comment', $data, __METHOD__ );
		$commentId = $dbw->insertId();
		$dbw->commit();

		// Give email notices to committer and commentors
		global $wgCodeReviewENotif, $wgEnableEmail;
		if( $wgCodeReviewENotif && $wgEnableEmail ) {
			// Make list of users to send emails to
			$users = $this->getCommentingUsers();
			if( $user = $this->getWikiUser() ) {
				$users[$user->getId()] = $user;
			}
			// Get repo and build comment title (for url)
			$title = SpecialPage::getTitleFor( 'Code', $this->mRepo->getName().'/'.$this->mId );
			$title->setFragment( "#c{$commentId}" );
			$url = $title->getFullUrl();
			foreach( $users as $userId => $user ) {
				// No sense in notifying this commentor
				if( $wgUser->getId() == $user->getId() ) {
					continue;
				}
				if( $user->canReceiveEmail() ) {
					$user->sendMail(
						wfMsg( 'codereview-email-subj', $this->mRepo->getName(), $this->mId ),
						wfMsg( 'codereview-email-body', $wgUser->getName(), $url, $this->mId, $text )
					);
				}
			}
		}
		
		return $commentId;
	}
	
	protected function commentData( $text, $review, $parent=null ) {
		global $wgUser;
		$dbw = wfGetDB( DB_MASTER );
		$ts = wfTimestamp( TS_MW );
		$sortkey = $this->threadedSortkey( $parent, $ts );
		return array(
			'cc_repo_id' => $this->mRepoId,
			'cc_rev_id' => $this->mId,
			'cc_text' => $text,
			'cc_parent' => $parent,
			'cc_user' => $wgUser->getId(),
			'cc_user_text' => $wgUser->getName(),
			'cc_timestamp' => $dbw->timestamp( $ts ),
			'cc_review' => $review,
			'cc_sortkey' => $sortkey );
	}

	protected function threadedSortKey( $parent, $ts ) {
		if( $parent ) {
			// We construct a threaded sort key by concatenating the timestamps
			// of all our parent comments
			$dbw = wfGetDB( DB_MASTER );
			$parentKey = $dbw->selectField( 'code_comment',
				'cc_sortkey',
				array( 'cc_id' => $parent ),
				__METHOD__ );
			if( $parentKey ) {
				return $parentKey . ',' . $ts;
			} else {
				// hmmmm
				throw new MWException( 'Invalid parent submission' );
			}
		} else {
			return $ts;
		}
	}

	function getComments() {
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->select( 'code_comment',
			array(
				'cc_id',
				'cc_text',
				'cc_parent',
				'cc_user',
				'cc_user_text',
				'cc_timestamp',
				'cc_review',
				'cc_sortkey' ),
			array(
				'cc_repo_id' => $this->mRepoId,
				'cc_rev_id' => $this->mId ),
			__METHOD__,
			array(
				'ORDER BY' => 'cc_sortkey' ) );

		$comments = array();
		foreach( $result as $row ) {
			$comments[] = CodeComment::newFromRow( $this, $row );
		}
		$result->free();
		return $comments;
	}
	
	function getCommentingUsers() {
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'code_comment',
			'DISTINCT(cc_user)',
			array(
				'cc_repo_id' => $this->mRepoId,
				'cc_rev_id' => $this->mId,
				'cc_user != 0' // users only
			),
			__METHOD__ 
		);
		$users = array();
		while( $row = $res->fetchObject() ) {
			$users[$row->cc_user] = User::newFromId( $row->cc_user );
		}
		return $users;
	}
	
	function getTags() {
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->select( 'code_tags',
			array( 'ct_tag' ),
			array(
				'ct_repo_id' => $this->mRepoId,
				'ct_rev_id' => $this->mId ),
			__METHOD__ );
		
		$tags = array();
		foreach( $result as $row ) {
			$tags[] = $row->ct_tag;
		}
		return $tags;
	}
	
	function addTags( $tags ) {
		$dbw = wfGetDB( DB_MASTER );
		$result = $dbw->insert( 'code_tags',
			$this->tagData( $tags ),
			__METHOD__,
			array( 'IGNORE' ) );
	}
	
	function removeTags( $tags ) {
		$dbw = wfGetDB( DB_MASTER );
		$tagsNormal = array();
		foreach( $tags as $tag ) {
			$tagsNormal[] = $this->normalizeTag( $tag );
		}
		$result = $dbw->delete( 'code_tags',
			array( 
				'ct_repo_id' => $this->mRepoId,
				'ct_rev_id' => $this->mId,
				'ct_tag' => $tagsNormal ),
			__METHOD__ );
	}
	
	protected function tagData( $tags ) {
		$data = array();
		foreach( $tags as $tag ) {
			$data[] = array(
				'ct_repo_id' => $this->mRepoId,
				'ct_rev_id' => $this->mId,
				'ct_tag' => $this->normalizeTag( $tag ) );
		}
		return $data;
	}
	
	function normalizeTag( $tag ) {
		global $wgContLang;
		$lower = $wgContLang->lc( $tag );
		
		$title = Title::newFromText( $tag );
		if( $title && $lower === $wgContLang->lc( $title->getPrefixedText() ) ) {
			return $lower;
		} else {
			return false;
		}
	}
	
	function isValidTag( $tag ) {
		return ($this->normalizeTag( $tag ) !== false );
	}
	
	function getPrevious() {
		// hack!
		if( $this->mId > 1 ) {
			return $this->mId - 1;
		} else {
			return false;
		}
	}
	
	function getNext() {
		$dbr = wfGetDB( DB_SLAVE );
		$encId = $dbr->addQuotes( $this->mId );
		$row = $dbr->selectRow( 'code_rev',
			'cr_id',
			array(
				'cr_repo_id' => $this->mRepoId,
				"cr_id > $encId" ),
			__METHOD__,
			array(
				'ORDER BY' => 'cr_repo_id, cr_id',
				'LIMIT' => 1 ) );
		
		if( $row ) {
			return intval($row->cr_id);
		} else {
			return false;
		}
	}
	
	function getNextUnresolved() {
		$dbr = wfGetDB( DB_SLAVE );
		$encId = $dbr->addQuotes( $this->mId );
		$row = $dbr->selectRow( 'code_rev',
			'cr_id',
			array(
				'cr_repo_id' => $this->mRepoId,
				"cr_id > $encId",
				'cr_status' => array('new','fixme') ),
			__METHOD__,
			array(
				'ORDER BY' => 'cr_repo_id, cr_id',
				'LIMIT' => 1 ) );
		
		if( $row ) {
			return intval($row->cr_id);
		} else {
			return false;
		}
	}
}
