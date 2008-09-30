<?php

class CodeRevisionTagger extends CodeRevisionView {

	function __construct( $repoName, $rev ){
		parent::__construct( $repoName, $rev );
		
		global $wgRequest;
		$this->mTags = $this->splitTags( $wgRequest->getText( 'wpTag' ) );
	}

	function execute() {
		global $wgOut;
		
		if( $this->validPost( 'codereview-add-tag' ) ) {
			$this->mRev->addTags( $this->mTags );
			
			$repo = $this->mRepo->getName();
			$rev = $this->mRev->getId();
			$special = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );

			$wgOut->redirect( $special->getFullUrl() );
		} else {
			throw new MWException( 'Attempted to add invalid tag (fixme UI)' );
		}
	}
	
	function splitTags( $input ) {
		$tags = array_map( 'trim', explode( ",", $input ) );
		foreach( $tags as $key => $tag ) {
			$normal = $this->mRev->normalizeTag( $tag );
			if( $normal === false ) {
				return null;
			}
			$tags[$key] = $normal;
		}
		return $tags;
	}
	
	function validPost( $permission ) {
		return parent::validPost( $permission ) &&
			!empty( $this->mTags );
	}
}
