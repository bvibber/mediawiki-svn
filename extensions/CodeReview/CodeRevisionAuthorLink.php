<?php

// Special:Code/MediaWiki/author/johndoe/link

class CodeRevisionAuthorLink extends CodeRevisionAuthorView {
	function getTitle() {
			$repo = $this->mRepo->getName();
			$auth = $this->mAuthor;
			return SpecialPage::getTitleFor( 'Code', "$repo/author/$auth/link");
	}

	function execute() {
		global $wgOut, $wgRequest, $wgUser;

		if ( !$wgUser->isAllowed( 'codereview-link-user' ) ) {
			$wgOut->permissionRequired( 'codereview-link-user' );
			return;
		}

		$target = $wgRequest->getVal( 'linktouser' );

		if ( $target && $wgRequest->getVal( 'newname' ) ) {
			$this->linkTo( $target );
			return;
		} else if ( $wgRequest->getVal( 'unlink' ) ) {
			$this->unlink();
			return;
		}
		

		$form = Xml::openElement( 'form', 
					array( 	'method' => 'get', 
							'action' => $this->getTitle()->getLocalUrl(), 
							'name' => 'uluser', 
							'id' => 'mw-codeauthor-form1' 
					) 
				) . Xml::openElement( 'fieldset' );
			
		$additional = '';
		if ( !$this->mUser )
			$form .= Xml::element( 'legend', array(), wfMsg( 'code-author-dolink' ) );
		else {
			$form .= Xml::element( 'legend', array(), wfMsg( 'code-author-alterlink' ) );
			$additional = Xml::openElement( 'fieldset' ) .
				Xml::element( 'legend', array(), wfMsg( 'code-author-orunlink' ) ) .
				Xml::submitButton( wfMsg( 'code-author-unlink' ), array( 'name' => 'unlink' ) ) .
				Xml::closeElement( 'fieldset' );
		}

		$form .= Xml::inputLabel( wfMsg( 'code-author-name' ), 'linktouser', 'username', 30, '') . ' ' .
				Xml::submitButton( wfMsg( 'ok' ), array( 'name' => 'newname') ) .
				Xml::closeElement( 'fieldset' ) .
				$additional .
				Xml::closeElement( 'form' ) . "\n";

		$wgOut->addHtml( $this->linkStatus() . $form );
	}

	/*
	 * Link the author to the wikiuser $name
	 */
	function linkTo ( $name ) {
		global $wgOut, $wgUser;

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
		if ( !$this->mUser ) 
			$dbw->insert(
				'code_authors',
				array(
					'ca_repo_id' => $this->mRepo->getId(),
					'ca_author' => $this->mAuthor,
					'ca_user_text' => $user->getName()
				),
				__METHOD__
			);
		else
			$dbw->update(
				'code_authors',
				array( 'ca_user_text' => $user->getName()),
				array(
					'ca_repo_id' => $this->mRepo->getId(),
					'ca_author' => $this->mAuthor,
				),
				__METHOD__
			);	
		
		$repo = $this->mRepo->getName();
		$author = SpecialPage::getTitleFor( 'Code', "$repo/author/$auth");
		$authorlink = $this->mSkin->link( $author, $this->mAuthor);
		$userlink = $this->mSkin->userLink( $user->getId(), $user->getName() );
		
		parent::$userLinks[$this->mAuthor] = $user;

		$wgOut->addHtml(
			'<div class="successbox">' . 
			wfMsgHtml( 'code-author-success', $authorlink, $userlink) .
			'</div>'
		);
	}

	function unlink() {
		global $wgOut;
		if ( !$this->mUser ) {
			$wgOut->addHtml( wfMsg( 'code-author-orphan' ) );
			return;
		}
		$dbw = wfGetDB( DB_MASTER );
		$dbw->delete(
			'code_authors',
			array(
				'ca_repo_id' => $this->mRepo->getId(),
				'ca_author' => $this->mAuthor,
			),
			__METHOD__
		);	
		
		parent::$userLinks[$this->mAuthor] = false;

		$wgOut->addHtml(
			'<div class="successbox">' . 
			wfMsgHtml( 'code-author-unlinksuccess' ) .
			'</div>'
		);
	}
}
