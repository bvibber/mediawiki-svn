<?php

class CodeRevisionCommitter extends CodeRevisionView {

	function __construct( $repoName, $rev ){
		// Parent should set $this->mRepo, $this->mRev, $this->mReplyTarget
		parent::__construct( $repoName, $rev );
		
		global $wgRequest;
		$this->mAddTags = $this->splitTags( $wgRequest->getText( 'wpTag' ) );
		$this->mRemoveTags = $this->splitTags( $wgRequest->getText( 'wpRemoveTag' ) );
		$this->mStatus = $wgRequest->getText( 'wpStatus' );
		$this->text = $wgRequest->getText( "wpReply{$this->mReplyTarget}" );
	}

	function execute() {
		global $wgRequest, $wgOut;
		if( $this->validPost('codereview-add-tag') && count($this->mAddTags) ) {
			$this->mRev->addTags( $this->mAddTags );
		}
		if( $this->validPost('codereview-add-remove') && count($this->mRemoveTags) ) {
			$this->mRev->removeTags( $this->mRemoveTags );
		}
		if( $this->validPost('codereview-set-status') && $this->mRev->isValidStatus($this->mStatus) ) {
			$this->mRev->setStatus( $this->mStatus );
		}
		if( $this->validPost('codereview-post-comment') && strlen($this->text) ) {
			$parent = $wgRequest->getIntOrNull( 'wpParent' );
			$review = $wgRequest->getInt( 'wpReview' );
			$isPreview = $wgRequest->getCheck( 'wpPreview' );
			$id = $this->mRev->saveComment( $this->text, $review, $parent );
			// For comments, take us back to the rev page focused on the new comment
			$permaLink = $this->commentLink( $id );
			$wgOut->redirect( $permaLink->getFullUrl() );
			return;
		}
		// Return to rev page
		$permaLink = $this->revLink();
		$wgOut->redirect( $permaLink->getFullUrl() );
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
	
	public function validPost( $permission ) {
		global $wgUser, $wgRequest;
		return parent::validPost($permission) && $wgRequest->wasPosted()
			&& $wgUser->matchEditToken( $wgRequest->getVal('wpEditToken') );
	}
}
