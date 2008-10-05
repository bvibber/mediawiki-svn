<?php
if (!defined('MEDIAWIKI')) die();

class CodeRevision {
	static function newFromSvn( CodeRepository $repo, $data ) {
		$rev = new CodeRevision();
		$rev->mRepo = $repo->getId();
		$rev->mId = intval($data['rev']);
		$rev->mAuthor = $data['author'];
		$rev->mTimestamp = wfTimestamp( TS_MW, strtotime( $data['date'] ) );
		$rev->mMessage = rtrim( $data['msg'] );
		$rev->mPaths = $data['paths'];
		$rev->mStatus = 'new';
		return $rev;
	}

	static function newFromRow( $row ) {
		$rev = new CodeRevision();
		$rev->mRepo = intval($row->cr_repo_id);
		$rev->mId = intval($row->cr_id);
		$rev->mAuthor = $row->cr_author;
		$rev->mTimestamp = wfTimestamp( TS_MW, $row->cr_timestamp );
		$rev->mMessage = $row->cr_message;
		$rev->mStatus = $row->cr_status;
		return $rev;
	}

	function getId() {
		return intval( $this->mId );
	}

	function getAuthor() {
		return $this->mAuthor;
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
	
	static function getPossibleStates() {
		return array( 'new', 'fixme', 'resolved', 'ok' );
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
				'cr_repo_id' => $this->mRepo,
				'cr_id' => $this->mId ),
			__METHOD__ );
	}

	function save() {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();
		
		$dbw->insert( 'code_rev',
			array(
				'cr_repo_id' => $this->mRepo,
				'cr_id' => $this->mId,
				'cr_author' => $this->mAuthor,
				'cr_timestamp' => $dbw->timestamp( $this->mTimestamp ),
				'cr_message' => $this->mMessage,
				'cr_status' => $this->mStatus ),
			__METHOD__,
			array( 'IGNORE' ) );

		if( $this->mPaths ) {
			$data = array();
			foreach( $this->mPaths as $path ) {
				$data[] = array(
					'cp_repo_id' => $this->mRepo,
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
			array( 'cp_repo_id' => $this->mRepo, 'cp_rev_id' => $this->mId ),
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
		if( !strlen($text) ) {
			return 0;
		}
		$dbw = wfGetDB( DB_MASTER );
		$data = $this->commentData( $text, $review, $parent );
		$data['cc_id'] = $dbw->nextSequenceValue( 'code_comment_cc_id' );
		$dbw->insert( 'code_comment',
			$data,
			__METHOD__ );
		
		return $dbw->insertId();
	}
	
	protected function commentData( $text, $review, $parent=null ) {
		global $wgUser;
		$dbw = wfGetDB( DB_MASTER );
		$ts = wfTimestamp( TS_MW );
		$sortkey = $this->threadedSortkey( $parent, $ts );
		return array(
			'cc_repo_id' => $this->mRepo,
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
				'cc_repo_id' => $this->mRepo,
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
	
	function getTags() {
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->select( 'code_tags',
			array( 'ct_tag' ),
			array(
				'ct_repo_id' => $this->mRepo,
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
				'ct_repo_id' => $this->mRepo,
				'ct_rev_id' => $this->mId,
				'ct_tag' => $tagsNormal ),
			__METHOD__ );
	}
	
	protected function tagData( $tags ) {
		$data = array();
		foreach( $tags as $tag ) {
			$data[] = array(
				'ct_repo_id' => $this->mRepo,
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
				'cr_repo_id' => $this->mRepo,
				"cr_id>$encId" ),
			__METHOD__,
			array(
				'ORDER BY' => 'cr_repo_id, cr_id',
				'LIMIT' => 1 ) );
		
		if( $row ) {
			return $row->cr_id;
		} else {
			return false;
		}
	}
}
