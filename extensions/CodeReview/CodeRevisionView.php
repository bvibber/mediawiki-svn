<?php

// Special:Code/MediaWiki/40696
class CodeRevisionView extends CodeView {

	function __construct( $repoName, $rev, $replyTarget=null ){
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
		$this->mRev = $this->mRepo ? $this->mRepo->getRevision( intval( $rev ) ) : null;
		$this->mReplyTarget = $replyTarget;
	}

	function execute(){
		global $wgOut, $wgUser;
		if( !$this->mRepo || !$this->mRev ) {
			$view = new CodeRepoListView();
			$view->execute();
			return;
		}

		$redirectOnPost = $this->checkPostings();
		if( $redirectOnPost ) {
			$wgOut->redirect( $redirectOnPost );
			return;
		}

		$repoLink = $wgUser->getSkin()->link( SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() ),
			htmlspecialchars( $this->mRepo->getName() ) );
		$rev = $this->mRev->getId();
		$revText = htmlspecialchars( $rev );
		$viewvc = $this->mRepo->getViewVcBase();
		if( $viewvc ){
			$url = htmlspecialchars( "$viewvc/?view=rev&revision=$rev" );
			$viewvcTxt = wfMsgHtml( 'code-rev-rev-viewvc' );
			$revText .= " (<a href=\"$url\" title=\"revision $rev\">$viewvcTxt</a>)";
		}
		$paths = '';
		$modifiedPaths = $this->mRev->getModifiedPaths();
		foreach( $modifiedPaths as $row ){
			$paths .= $this->formatPathLine( $row->cp_path, $row->cp_action );
		}
		if( $paths ){
			$paths = "<ul>\n$paths</ul>";
		}
		$fields = array(
			'code-rev-repo' => $repoLink,
			'code-rev-rev' => $revText,
			'code-rev-author' => $this->authorLink( $this->mRev->getAuthor() ),
			'code-rev-message' => $this->formatMessage( $this->mRev->getMessage() ),
			'code-rev-paths' => $paths,
			'code-rev-tags' => $this->formatTags(),
		);
		$html = '<table class="mw-codereview-meta">';
		foreach( $fields as $label => $data ) {
			$html .= "<tr><td>" . wfMsgHtml( $label ) . "</td><td>$data</td></tr>\n";
		}
		$html .= '</table>';
		
		$html .=
			"<h2>" . wfMsgHtml( 'code-rev-diff' ) . "</h2>" .
			"<div class='mw-codereview-diff'>" .
			$this->formatDiff() .
			"</div>";

		$html .=
			'<h2>'. wfMsgHtml( 'code-comments' ) .'</h2>' .
			$this->formatComments();
		
		if( $this->mReplyTarget ) {
			global $wgJsMimeType;
			$id = intval( $this->mReplyTarget );
			$html .= "<script type=\"$wgJsMimeType\">addOnloadHook(function(){" .
				"document.getElementById('wpReplyTo$id').focus();" .
				"});</script>";
		}

		$wgOut->addHtml( $html );
	}

	function checkPostings() {
		global $wgRequest, $wgUser;
		if( $wgRequest->wasPosted()
			&& $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) ) {
			// Look for a posting...
			$text = $wgRequest->getText( 'wpReply' );
			$parent = $wgRequest->getIntOrNull( 'wpParent' );
			$review = $wgRequest->getInt( 'wpReview' );
			$isPreview = $wgRequest->getCheck( 'wpPreview' );
			if( $isPreview ) {
				// NYI
			} else {
				$id = $this->mRev->saveComment( $text, $review, $parent );
				
				// Redirect to the just-saved comment; this avoids POST
				// horrors on forward/rewind. Hope we don't have slave issues?
				$permaLink = $this->commentLink( $id );
				return $permaLink->getFullUrl();
			}
		}
		return false;
	}

	function formatPathLine( $path, $action ) {
		$desc = wfMsgHtml( 'code-rev-modified-'.strtolower( $action ) );
		$encPath = htmlspecialchars( $path );
		$viewvc = $this->mRepo->getViewVcBase();
		if( $viewvc ) {
			$rev = $this->mRev->getId();
			$safePath = wfUrlEncode( $path );
			$link = $this->mSkin->makeExternalLink(
				"$viewvc$safePath?view=markup&pathrev=$rev",
				$encPath );
		} else {
			$link = $encPath;
		}
		return "<li>$link ($desc)</li>\n";
	}
	
	function formatTags() {
		global $wgUser;
		
		$tags = $this->mRev->getTags();
		$list = implode( ", ",
			array_map(
				array( $this, 'formatTag' ),
				$tags ) );
		
		if( $wgUser->isAllowed( 'codereview-add-tag' ) ) {
			$list .= $this->addTagForm();
		}
		
		return $list;
	}
	
	function addTagForm() {
		global $wgUser;
		$repo = $this->mRepo->getName();
		$rev = $this->mRev->getId();
		$special = SpecialPage::getTitleFor( 'Code', "$repo/$rev/add/tag" );
		return
			Xml::openElement( 'form',
				array(
					'action' => $special->getLocalUrl(),
					'method' => 'post' ) ) .
			Xml::input( 'wpTag', '' ) .
			Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
			'&nbsp;' .
			Xml::submitButton( wfMsg( 'code-rev-tag-add' ) ) .
			'</form>';
	}
	
	function formatTag( $tag ) {
		global $wgUser;
		
		$repo = $this->mRepo->getName();
		$special = SpecialPage::getTitleFor( 'Code', "$repo/tag/$tag" );
		
		return $this->mSkin->link( $special, htmlspecialchars( $tag ) );
	}

	function formatDiff() {
		$diff = $this->mRepo->getDiff( $this->mRev->getId() );
		return "<pre>" . htmlspecialchars( $diff ) . "</pre>";
	}

	function formatComments() {
		return "<div class='mw-codereview-comments'>" .
			implode( "\n",
				array_map(
					array( $this, 'formatCommentInline' ),
					$this->mRev->getComments() ) ) .
			$this->postCommentForm() .
			"</div>";
	}

	function formatCommentInline( $comment ) {
		if( $comment->id == $this->mReplyTarget ) {
			return $this->formatComment( $comment,
				$this->postCommentForm( $comment->id ) );
		} else {
			return $this->formatComment( $comment );
		}
	}
	
	function commentLink( $commentId ) {
		$repo = $this->mRepo->getName();
		$rev = $this->mRev->getId();
		$title = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );
		$title->setFragment( "#c{$commentId}" );
		return $title;
	}
	
	function formatComment( $comment, $replyForm='' ) {
		global $wgOut, $wgLang;
		$linker = new CodeCommentLinkerWiki( $this->mRepo );
		
		return Xml::openElement( 'div',
			array(
				'class' => 'mw-codereview-comment',
				'id' => 'c' . intval( $comment->id ),
				'style' => $this->commentStyle( $comment ) ) ) .
			'<div class="mw-codereview-comment-meta">' .
			$this->mSkin->link( $this->commentLink( $comment->id ), "#" ) .
			wfMsgHtml( 'code-rev-comment-by',
				$this->mSkin->userLink( $comment->user, $comment->userText ) .
				$this->mSkin->userToolLinks( $comment->user, $comment->userText ) ) .
			' &nbsp; ' .
			$wgLang->timeanddate( $comment->timestamp ) .
			' ' .
			$this->commentReplyLink( $comment->id ) .
			'</div>' .
			'<div class="mw-codereview-comment-text">' .
			$wgOut->parse( $linker->link( $comment->text ) ) .
			'</div>' .
			$replyForm .
			'</div>';
	}

	function commentStyle( $comment ) {
		$depth = $comment->threadDepth();
		$margin = ($depth - 1) * 48;
		return "margin-left: ${margin}px";
	}
	
	function commentReplyLink( $id ) {
		$repo = $this->mRepo->getName();
		$rev = $this->mRev->getId();
		$self = SpecialPage::getTitleFor( 'Code', "$repo/$rev/reply/$id" );
		$self->setFragment( "#c$id" );
		return '[' .
			$this->mSkin->link( $self, wfMsg( 'codereview-reply-link' ) ) .
			']';
	}
	
	function postCommentForm( $parent=null ) {
		global $wgUser;
		return '<div class="mw-codereview-post-comment">' .
			Xml::openElement( 'form',
				array(
					'action' => '', // fixme
					'method' => 'post' ) ) .
			Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
			($parent ? Xml::hidden( 'wpParent', $parent ) : '') .
			'<div>' .
			Xml::openElement( 'textarea', array(
				'name' => 'wpReply',
				'id' => "wpReplyTo{$parent}",
				'cols' => 40,
				'rows' => 5 ) ) .
			'</textarea>' .
			'</div>' .
			'<div>' .
			Xml::submitButton( wfMsg( 'code-rev-comment-submit' ), array( 'name' => 'wpSave' ) ) .
			' ' .
			Xml::submitButton( wfMsg( 'code-rev-comment-preview' ), array( 'name' => 'wpPreview' ) ) .
			'</div>' .
			'</form>' .
			'</div>';
	}
}
