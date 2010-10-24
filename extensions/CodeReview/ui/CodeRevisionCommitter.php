<?php

class CodeRevisionCommitter extends CodeRevisionView {

	function __construct( $repoName, $rev ) {
		// Parent should set $this->mRepo, $this->mRev, $this->mReplyTarget
		parent::__construct( $repoName, $rev );
	}

	function execute() {
		global $wgRequest, $wgOut, $wgUser;

		if ( !$wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) ) {
			$wgOut->addHTML( '<strong>' . wfMsg( 'sessionfailure' ) . '</strong>' );
			parent::execute();
			return;
		}
		if ( !$this->mRev ) {
			parent::execute();
			return;
		}

		$redirTarget = null;
		$dbw = wfGetDB( DB_MASTER );

		$dbw->begin();
		// Change the status if allowed
		$statusChanged = false;
		if ( $this->validPost( 'codereview-set-status' ) && $this->mRev->isValidStatus( $this->mStatus ) ) {
			$statusChanged = $this->mRev->setStatus( $this->mStatus, $wgUser );
		}
		$addTags = $removeTags = array();
		if ( $this->validPost( 'codereview-add-tag' ) && count( $this->mAddTags ) ) {
			$addTags = $this->mAddTags;
		}
		if ( $this->validPost( 'codereview-remove-tag' ) && count( $this->mRemoveTags ) ) {
			$removeTags = $this->mRemoveTags;
		}
		// If allowed to change any tags, then do so
		if ( count( $addTags ) || count( $removeTags ) ) {
			$this->mRev->changeTags( $addTags, $removeTags, $wgUser );
		}
		// Add any comments
		$commentAdded = false;
		if ( $this->validPost( 'codereview-post-comment' ) && strlen( $this->text ) ) {
			$parent = $wgRequest->getIntOrNull( 'wpParent' );
			$review = $wgRequest->getInt( 'wpReview' );
			// $isPreview = $wgRequest->getCheck( 'wpPreview' );
			$commentId = $this->mRev->saveComment( $this->text, $review, $parent );

		    $commentAdded = ($commentId !== 0);

			// For comments, take us back to the rev page focused on the new comment
			if ( !$this->jumpToNext ) {
				$redirTarget = $this->commentLink( $commentId );
			}
		}
		$dbw->commit();

	    if ( $statusChanged || $commentAdded ) {
		    if ( $statusChanged && $commentAdded ) {
			    $url = $this->mRev->getFullUrl( $commentId );
		        $this->mRev->emailNotifyUsersOfChanges( 'codereview-email-subj4', 'codereview-email-body4',
			        $wgUser->getName(), $this->mRev->getIdStringUnique(), $this->mRev->mOldStatus, $this->mRev->mStatus,
					$url, $this->text
		            );
		    } else if ( $statusChanged ) {
				$this->mRev->emailNotifyUsersOfChanges( 'codereview-email-subj3', 'codereview-email-body3',
					$wgUser->getName(), $this->mRev->getIdStringUnique(), $this->mRev->mOldStatus, $this->mRev->mStatus
					);
		    } else if ( $commentAdded ) {
			    $url = $this->mRev->getFullUrl( $commentId );
				$this->mRev->emailNotifyUsersOfChanges( 'codereview-email-subj', 'codereview-email-body',
					$wgUser->getName(), $url, $this->mRev->getIdStringUnique(), $this->text );
		    }
	    }

		// Return to rev page
		if ( !$redirTarget ) {
			// Was "next & unresolved" clicked?
			if ( $this->jumpToNext ) {
				$next = $this->mRev->getNextUnresolved( $this->mPath );
				if ( $next ) {
					$redirTarget = SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() . '/' . $next );
				} else {
					$redirTarget = SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() );
				}
			} else {
				# $redirTarget already set for comments
				$redirTarget = $this->revLink();
			}
		}
		$wgOut->redirect( $redirTarget->getFullUrl( array( 'path' => $this->mPath ) ) );
	}

	public function validPost( $permission ) {
		global $wgUser, $wgRequest;
		return parent::validPost( $permission ) && $wgRequest->wasPosted()
			&& $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) );
	}
}
