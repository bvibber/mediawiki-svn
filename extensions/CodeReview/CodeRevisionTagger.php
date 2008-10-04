<?php

class CodeRevisionTagger extends CodeRevisionView {

	function __construct( $repoName, $rev ){
		parent::__construct( $repoName, $rev );
		
		global $wgRequest;
		$this->mAddTags = $this->splitTags( $wgRequest->getText( 'wpTag' ) );
		$this->mRemoveTags = $this->splitTags( $wgRequest->getText( 'wpRemoveTag' ) );
	}

	function execute() {
		global $wgOut;
		
		if( $this->validPost( 'codereview-add-tag' ) ) {
			if( count($this->mAddTags) )
				$this->mRev->addTags( $this->mAddTags );
			if( count($this->mRemoveTags) )
				$this->mRev->removeTags( $this->mRemoveTags );
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
			( !empty( $this->mAddTags ) || !empty( $this->mRemoveTags ) );
	}
}
