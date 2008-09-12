<?php

class SpecialRepoAdmin extends SpecialPage {
	function __construct() {
		wfLoadExtensionMessages( 'CodeReview' );
		parent::__construct( 'RepoAdmin', 'repoadmin' );
	}

	function execute( $subpage ) {
		global $wgRequest, $wgUser;
		$this->setHeaders();
/*
		if( !$this->userCanExecute( $wgUser ) ){
			$this->displayRestrictionError();
			return;	
		}
*/
		$repo = $wgRequest->getVal( 'repo', $subpage );
		if( $repo == '' ) {
			$view = new RepoAdminListView( $this );
		} else {
			$view = new RepoAdminRepoView( $this, $repo );
		}
		$view->execute();
	}
}

class RepoAdminListView {
	var $mPage;

	function __construct( $page ) {
		$this->mPage = $page;
	}

	function getForm() {
		global $wgScript;
		return Xml::fieldset( wfMsg( 'repoadmin-new-legend' ) ) .
			Xml::openElement( 'form', array( 'method' => 'get', 'action' => $wgScript ) ) .
			Xml::hidden( 'title', $this->mPage->getTitle()->getPrefixedDBKey() ) .
			Xml::inputLabel( wfMsg( 'repoadmin-new-label' ), 'repo', 'repo' ) .
			Xml::submitButton( wfMsg( 'repoadmin-new-button' ) ) .
			'</form></fieldset>';
	}

	function execute() {
		global $wgOut;
		$wgOut->addHtml( $this->getForm() );
		$repos = CodeRepository::getRepoList();
		if( !count( $repos ) ){
			return;
		}
		$text = '';
		foreach( $repos as $repo ){
			$name = $repo->getName();
			$text .= "* [[Special:RepoAdmin/$name|$name]]\n";
		}
		$wgOut->addWikiText( $text );
	}
}

class RepoAdminRepoView {
	var $mPage;

	function __construct( $page, $repo ) {
		$this->mPage = $page;
		$this->mRepoName = $repo;
		$this->mRepo = CodeRepository::newFromName( $repo );
	}

	function execute() {
		global $wgOut, $wgRequest, $wgUser;
		$repoExists = (bool)$this->mRepo;
		$repoPath = $wgRequest->getVal( 'wpRepoPath', $repoExists ? $this->mRepo->mPath : '' );
		$bugPath = $wgRequest->getVal( 'wpBugPath', $repoExists ? $this->mRepo->mBugzilla : '' );
		$viewPath = $wgRequest->getVal( 'wpViewPath', $repoExists ? $this->mRepo->mViewVc : '' );
		if( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ), $this->mRepoName ) ){
			// @todo log
			$dbw = wfGetDB( DB_MASTER );
			if( $repoExists ){
				$dbw->update(
					'code_repo',
					array(
						'repo_path' => $repoPath,
						'repo_viewvc' => $viewPath,
						'repo_bugzilla' => $bugPath
					),
					array( 'repo_id' => $this->mRepo->getId() ),
					__METHOD__
				);
			} else {
				$dbw->insert(
					'code_repo',
					array(
						'repo_name' => $this->mRepoName,
						'repo_path' => $repoPath,
						'repo_viewvc' => $viewPath,
						'repo_bugzilla' => $bugPath
					),
					__METHOD__
				);
			}
			$wgOut->wrapWikiMsg( '<div class="successbox">$1</div>', array( 'repoadmin-edit-sucess', $this->mRepoName ) );
			return;
		}
		$wgOut->addHtml(
			Xml::fieldset( wfMsg( 'repoadmin-edit-legend', $this->mRepoName ) ) .
			Xml::openElement( 'form', array( 'method' => 'post', 'action' => $this->mPage->getTitle( $this->mRepoName )->getLocalURL() ) ) .
			'<table>'.
			'<tr><td>' . Xml::label( wfMsg( 'repoadmin-edit-path' ), 'wpRepoPath' ) . '</td><td>' . Xml::input( 'wpRepoPath', false, $repoPath ) . '</td></tr>' .
			'<tr><td>' . Xml::label( wfMsg( 'repoadmin-edit-bug' ), 'wpBugPath' ) . '</td><td>' . Xml::input( 'wpBugPath', false, $bugPath ) . '</td></tr>' .
			'<tr><td>' . Xml::label( wfMsg( 'repoadmin-edit-view' ), 'wpViewPath' ) . '</td><td>' . Xml::input( 'wpViewPath', false, $viewPath ) . '</td></tr>' .
			'</table>' .
			Xml::hidden( 'wpEditToken', $wgUser->editToken( $this->mRepoName ) ) .
			Xml::submitButton( wfMsg( 'repoadmin-edit-button' ) ) .
			'</form></fieldset>'
		);
	}
}