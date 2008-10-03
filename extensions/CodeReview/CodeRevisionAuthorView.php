<?php

class CodeRevisionAuthorView extends CodeRevisionListView {
	function __construct( $repoName, $author ) {
		parent::__construct( $repoName );
		$this->mAuthor = $author;
	}
	
	function getPager() {
		return new SvnRevAuthorTablePager( $this, $this->mAuthor );
	}

	function execute() {
		global $wgOut, $wgUser, $wgRequest;

		$name = $wgRequest->getVal( 'user' );

		if ( $name ) {
			$this->linkTo( $name );
			return;
		}

		$user = $this->authorWikiUser( $this->mAuthor );
		if ( $user ) {
			$auth = wfMsgHtml( 'code-author-haslink',
				$this->mSkin->userLink( $user->getId(), $user->getName() ) .
				$this->mSkin->userToolLinks( $user->getId(), $user->getName() )
			);
			$wgOut->addHtml($auth);
		}

		parent::execute();

		if ( $wgUser->isAllowed( 'codereview-link-user' ) && !$user ) {
			$wgOut->addHtml(
				Xml::openElement( 'form', 
					array( 	'method' => 'get', 
							'action' => $this->getPager()->getTitle()->getLocalUrl(), 
							'name' => 'uluser', 
							'id' => 'mw-codeauthor-form1' 
					) 
				) .
				Xml::openElement( 'fieldset' ) .
				Xml::element( 'legend', array(), wfMsg( 'code-author-dolink' ) ) .
				Xml::inputLabel( wfMsg( 'code-author-name' ), 'user', 'username', 30, '') . ' ' .
				Xml::submitButton( wfMsg( 'ok' ) ) .
				Xml::closeElement( 'fieldset' ) .
				Xml::closeElement( 'form' ) . "\n"
			);
		}
	}
	
	/*
	 * Link the author to the wikiuser $name
	 */
	function linkTo ( $name ) {
		global $wgOut, $wgUser;

		if ( !$wgUser->isAllowed( 'codereview-link-user' ) ) {
			$wgOut->permissionRequired( 'codereview-link-user' );
			return;
		}

		if( $name == '' ) {
			$wgOut->addWikiMsg( 'nouserspecified' );
			return;
		}
		$user = User::newFromName( $name );

		if( !$user || $user->isAnon() ) {
			$wgOut->addWikiMsg( 'nosuchusershort', $name );
			return;
		}
		$dbw = wfGetDB( DB_MASTER );
		$dbw->insert(
			'code_authors',
			array(
				'ca_repo_id' => $this->mRepo->getId(),
				'ca_author' => $this->mAuthor,
				'ca_user_text' => $user->getName()
			),
			__METHOD__
		);
		$authorlink = $this->mSkin->link( $this->getPager()->getTitle(), $this->mAuthor);
		$userlink = $this->mSkin->userLink( $user->getId(), $user->getName() );
		$wgOut->addHtml(
			'<div class="successbox">' . 
			wfMsgHtml( 'code-author-success', $authorlink, $userlink) .
			'</div>'
		);
	}
}

class SvnRevAuthorTablePager extends SvnRevTablePager {
	function __construct( $view, $author ) {
		parent::__construct( $view );
		$this->mAuthor = $author;
	}
	
	function getQueryInfo() {
		$info = parent::getQueryInfo();
		$info['conds']['cr_author'] = $this->mAuthor; // fixme: normalize input?
		return $info;
	}

	function getTitle(){
		$repo = $this->mRepo->getName();
		return SpecialPage::getTitleFor( 'Code', "$repo/author/$this->mAuthor" );
	}
}
