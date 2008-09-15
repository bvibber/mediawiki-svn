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
		$repo->mId = $row->repo_id;
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

	function getLastStoredRev(){
		$dbr = wfGetDB( DB_SLAVE );
		$row = $dbr->selectField(
			'code_rev',
			'MAX(cr_id)',
			array(
				'cr_repo_id' => $this->getId(),
			),
			__METHOD__
		);
		return intval( $row );
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
			throw new MWException( 'barf' );
		return CodeRevision::newFromRow( $row );
	}
	
	function getDiff( $rev ) {
		$svn = SubversionAdaptor::newFromRepo( $this->mPath );
		return $svn->getDiff( '', $rev - 1, $rev );
	}
}
