<?php
if (!defined('MEDIAWIKI')) die();

class CodeRepository {
	public static function newFromName( $name ) {
		$dbw = wfGetDB( DB_MASTER );
		$row = $dbw->selectRow(
			'code_repo',
			array(
				'repo_id',
				'repo_name',
				'repo_path',
				'repo_viewvc',
				'repo_bugzilla' ),
			array( 'repo_name' => $name ),
			__METHOD__ );

		if( $row ) {
			return self::newFromRow( $row );
		} else {
			return null;
		}
	}

	static function newFromRow( $row ) {
		$repo = new CodeRepository();
		$repo->mId = intval($row->repo_id);
		$repo->mName = $row->repo_name;
		$repo->mPath = $row->repo_path;
		$repo->mViewVc = $row->repo_viewvc;
		$repo->mBugzilla = $row->repo_bugzilla;
		return $repo;
	}

	static function getRepoList(){
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'code_repo', '*', array(), __METHOD__ );
		$repos = array();
		foreach( $res as $row ){
			$repos[] = self::newFromRow( $row );
		}
		return $repos;
	}

	function getId() {
		return intval( $this->mId );
	}

	function getName() {
		return $this->mName;
	}

	function getPath(){
		return $this->mPath;
	}

	function getViewVcBase(){
		return $this->mViewVc;
	}

	/**
	 * Return a bug URL or false.
	 */
	function getBugPath( $bugId ) {
		if( $this->mBugzilla ) {
			return str_replace( '$1',
				urlencode( $bugId ), $this->mBugzilla );
		}
		return false;
	}

	function getLastStoredRev() {
		$dbr = wfGetDB( DB_SLAVE );
		$row = $dbr->selectField(
			'code_rev',
			'MAX(cr_id)',
			array( 'cr_repo_id' => $this->getId() ),
			__METHOD__
		);
		return intval( $row );
	}
	
	function getAuthorList() {
		global $wgMemc;
		$key = wfMemcKey( 'codereview', 'authors', $this->getId() );
		$authors = $wgMemc->get( $key );
		if( is_array($authors) ) {
			return $authors;
		}
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 
			'code_rev',
			array( 'cr_author', 'MAX(cr_timestamp) AS time' ),
			array( 'cr_repo_id' => $this->getId() ),
			__METHOD__,
			array( 'GROUP BY' => 'cr_author', 
				'ORDER BY' => 'time DESC', 'LIMIT' => 500 )
		);
		$authors = array();
		while( $row = $dbr->fetchObject( $res ) ) {
			$authors[] = $row->cr_author;
		}
		$wgMemc->set( $key, $authors, 3600*24*3 );
		return $authors;
	}
	
	function getTagList() {
		global $wgMemc;
		$key = wfMemcKey( 'codereview', 'tags', $this->getId() );
		$tags = $wgMemc->get( $key );
		if( is_array($tags) ) {
			return $tags;
		}
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 
			'code_tags',
			array( 'ct_tag', 'COUNT(*) AS revs' ),
			array( 'ct_repo_id' => $this->getId() ),
			__METHOD__,
			array( 'GROUP BY' => 'ct_tag', 
				'ORDER BY' => 'revs DESC', 'LIMIT' => 500 )
		);
		$tags = array();
		while( $row = $dbr->fetchObject( $res ) ) {
			$tags[] = $row->ct_tag;
		}
		$wgMemc->set( $key, $tags, 3600*24*3 );
		return $tags;
	}

	/**
	 * Load a particular revision out of the DB
	 */
	function getRevision( $id ) {
		$dbr = wfGetDB( DB_SLAVE );
		$row = $dbr->selectRow(
			'code_rev',
			'*',
			array(
				'cr_id' => $id,
				'cr_repo_id' => $this->getId(),
			),
			__METHOD__
		);
		if( !$row )
			throw new MWException( 'Failed to load expected revision data' );
		return CodeRevision::newFromRow( $row );
	}

	function getDiff( $rev, $skipCache = '' ) {
		global $wgMemc;

		$rev1 = $rev - 1;
		$rev2 = $rev;
		
		$revision = $this->getRevision( $rev );
		if( !$revision->isDiffable() ) {
			return false;
		}

		$key = wfMemcKey( 'svn', md5( $this->mPath ), 'diff', $rev1, $rev2 );
		if( $skipCache === 'skipcache' ) {
			$data = NULL;
		} else {
			$data = $wgMemc->get( $key );
		}

		if( !$data ) {
			$svn = SubversionAdaptor::newFromRepo( $this->mPath );
			$data = $svn->getDiff( '', $rev1, $rev2 );
			$wgMemc->set( $key, $data, 3600*24*3 );
		}

		return $data;
	}
}
