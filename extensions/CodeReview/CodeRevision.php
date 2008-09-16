<?php
if (!defined('MEDIAWIKI')) die();

class CodeRevision {
	static function newFromSvn( CodeRepository $repo, $data ) {
		$rev = new CodeRevision();
		$rev->mRepo = $repo->getId();
		$rev->mId = $data['rev'];
		$rev->mAuthor = $data['author'];
		$rev->mTimestamp = wfTimestamp( TS_MW, strtotime( $data['date'] ) );
		$rev->mMessage = rtrim( $data['msg'] );
		$rev->mPaths = $data['paths'];
		return $rev;
	}

	static function newFromRow( $row ) {
		$rev = new CodeRevision();
		$rev->mRepo = $row->cr_repo_id;
		$rev->mId = $row->cr_id;
		$rev->mAuthor = $row->cr_author;
		$rev->mTimestamp = wfTimestamp( TS_MW, $row->cr_timestamp );
		$rev->mMessage = $row->cr_message;
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

	function save() {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->insert( 'code_rev',
			array(
				'cr_repo_id' => $this->mRepo,
				'cr_id' => $this->mId,
				'cr_author' => $this->mAuthor,
				'cr_timestamp' => $dbw->timestamp( $this->mTimestamp ),
				'cr_message' => $this->mMessage ),
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

	function saveComment( $text, $review, $parent=null ) {
		global $wgUser;
		$ts = wfTimestamp( TS_MW );
		$sortkey = $this->threadedSortkey( $parent, $ts );

		$dbw = wfGetDB( DB_SLAVE );
		$dbw->insert( 'code_comment',
			array(
				'cc_repo_id' => $this->mRepo,
				'cc_rev_id' => $this->mId,
				'cc_text' => $text,
				'cc_parent' => $parent,
				'cc_user' => $wgUser->getId(),
				'cc_user_text' => $wgUser->getName(),
				'cc_timestamp' => $dbw->timestamp( $ts ),
				'cc_review' => $review,
				'cc_sortkey' => $sortkey ),
			__METHOD__ );
		
		return $dbw->insertId();
	}

	protected function threadedSortKey( $parent, $ts ) {
		if( $parent ) {
			// We construct a threaded sort key by concatenating the timestamps
			// of all our parent comments
			$dbw = wfGetDB( DB_SLAVE );
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
			$comments[] = new CodeComment( $this, $row );
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
		$result = $dbw->delete( 'code_tags',
			$this->tagData( $tags ),
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
		if( $title && $tag === $wgContLang->lc( $title->getPrefixedText() ) ) {
			return $lower;
		} else {
			return false;
		}
	}
	
	function isValidTag( $tag ) {
		return ($this->normalizeTag( $tag ) !== false );
	}
}
