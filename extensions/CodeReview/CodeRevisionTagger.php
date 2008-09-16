<?php

class CodeRevisionTagger extends CodeRevisionView {

	function __construct( $repoName, $rev ){
		parent::__construct( $repoName, $rev );
		
		global $wgRequest;
		$this->mTag = $wgRequest->getText( 'wpTag' );
	}

	function execute() {
		global $wgOut;
		
		if( $this->validPost( 'codereview-add-tag' ) ) {
			$this->mRev->addTags( array( $this->mTag ) );
			
			$repo = $this->mRepo->getName();
			$rev = $this->mRev->getId();
			$special = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );

			$wgOut->redirect( $special->getFullUrl() );
		} else {
			throw new MWException( 'barf' );
		}
	}
	
	function validPost( $permission ) {
		return parent::validPost( $permission ) &&
			$this->mRev->isValidTag( $this->mTag );
	}
}
