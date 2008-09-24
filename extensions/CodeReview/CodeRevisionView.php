<?php

// Special:Code/MediaWiki/40696
class CodeRevisionView extends CodeView {

	function __construct( $repoName, $rev, $replyTarget=null ){
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
		$this->mRev = $this->mRepo ? $this->mRepo->getRevision( intval( $rev ) ) : null;
		$this->mReplyTarget = $replyTarget;
		$this->mPreviewText = false;
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
		$revText = $this->navigationLinks();
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
			'code-rev-status' => $this->statusForm(),
			'code-rev-message' => $this->formatMessage( $this->mRev->getMessage() ),
			'code-rev-paths' => $paths,
			'code-rev-tags' => $this->formatTags(),
		);
		$html = '<table class="mw-codereview-meta">';
		foreach( $fields as $label => $data ) {
			$html .= "<tr><td>" . wfMsgHtml( $label ) . "</td><td>$data</td></tr>\n";
		}
		$html .= '</table>';
		
		$diffHtml = $this->formatDiff();
		if( $diffHtml ) {
			$html .=
				"<h2>" . wfMsgHtml( 'code-rev-diff' ) . "</h2>" .
				"<div class='mw-codereview-diff'>" .
				$diffHtml .
				"</div>";
		}
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
	
	function navigationLinks() {
		$rev = $this->mRev->getId();
		$prev = $this->mRev->getPrevious();
		$next = $this->mRev->getNext();
		$repo = $this->mRepo->getName();
		
		$links = array();
		
		if( $prev ) {
			$prevTarget = SpecialPage::getTitleFor( 'Code', "$repo/$prev" );
			$links[] = '&lt;&nbsp;' . $this->mSkin->link( $prevTarget, "r$prev" );
		}
		
		$revText = "<b>r$rev</b>";
		$viewvc = $this->mRepo->getViewVcBase();
		if( $viewvc ){
			$url = htmlspecialchars( "$viewvc/?view=rev&revision=$rev" );
			$viewvcTxt = wfMsgHtml( 'code-rev-rev-viewvc' );
			$revText .= " (<a href=\"$url\" title=\"revision $rev\">$viewvcTxt</a>)";
		}
		$links[] = $revText;

		if( $next ) {
			$nextTarget = SpecialPage::getTitleFor( 'Code', "$repo/$next" );
			$links[] = $this->mSkin->link( $nextTarget, "r$next" ) . '&nbsp;&gt;';
		}

		return implode( ' | ', $links );
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
				// Save the text for reference on later comment display...
				$this->mPreviewText = $text;
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
		$diff = '';
		if( $viewvc ) {
			$rev = $this->mRev->getId();
			$prev = $rev - 1;
			$safePath = wfUrlEncode( $path );
			$link = $this->mSkin->makeExternalLink( "$viewvc$safePath?view=markup&pathrev=$rev", $encPath );
			if( $action !== 'A' ) {
				$diff = ' (' .
					$this->mSkin->makeExternalLink( "$viewvc$safePath?&pathrev=$rev&r1=$prev&r2=$rev", 
						wfMsgHtml('code-rev-diff-link') ) .
					')';
			}
		} else {
			$link = $encPath;
		}
		return "<li>$link ($desc)$diff</li>\n";
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
	
	function statusForm() {
		global $wgUser;
		if( $wgUser->isAllowed( 'codereview-set-status' ) ) {
			$repo = $this->mRepo->getName();
			$rev = $this->mRev->getId();
			$special = SpecialPage::getTitleFor( 'Code', "$repo/$rev/set/status" );
			return
				Xml::openElement( 'form',
					array(
						'action' => $special->getLocalUrl(),
						'method' => 'post' ) ) .
				Xml::openElement( 'select',
					array( 'name' => 'wpStatus' ) ) .
				$this->buildStatusList() .
				'</select>' .
				Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
				'&nbsp;' .
				Xml::submitButton( wfMsg( 'code-rev-status-set' ) ) .
				'</form>';
		} else {
			return htmlspecialchars( $this->statusDesc( $this->mRev->getStatus() ) );
		}
	}
	
	function buildStatusList() {
		$states = $this->mRev->getPossibleStates();
		$out = '';
		foreach( $states as $state ) {
			$list[$state] = $this->statusDesc( $state );
			$out .= Xml::option(
				$this->statusDesc( $state ),
				$state,
				$this->mRev->getStatus() == $state );
		}
		return $out;
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
		if( !$diff ) {
			return false;
		}
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
	
	function previewComment( $text, $review=0 ) {
		$comment = $this->mRev->previewComment( $text, $review );
		return $this->formatComment( $comment );
	}
	
	function formatComment( $comment, $replyForm='' ) {
		global $wgOut, $wgLang;
		$linker = new CodeCommentLinkerWiki( $this->mRepo );
		
		if( $comment->id === null ) {
			$linkId = 'cpreview';
			$permaLink = "<b>Preview:</b> ";
		} else {
			$linkId = 'c' . intval( $comment->id );
			$permaLink = $this->mSkin->link( $this->commentLink( $comment->id ), "#" );
		}
		
		return Xml::openElement( 'div',
			array(
				'class' => 'mw-codereview-comment',
				'id' => $linkId,
				'style' => $this->commentStyle( $comment ) ) ) .
			'<div class="mw-codereview-comment-meta">' .
			$permaLink .
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
		if( $this->mPreviewText != false && $parent === $this->mReplyTarget ) {
			$preview = $this->previewComment( $this->mPreviewText );
			$text = htmlspecialchars( $this->mPreviewText );
		} else {
			$preview = '';
			$text = '';
		}
		$repo = $this->mRepo->getName();
		$rev = $this->mRev->getId();
		if( $parent ) {
			$special = SpecialPage::getTitleFor( 'Code', "$repo/$rev/reply/$parent" );
		} else {
			$special = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );
		}
		return '<div class="mw-codereview-post-comment">' .
			$preview .
			Xml::openElement( 'form',
				array(
					'action' => $special->getLocalUrl(),
					'method' => 'post' ) ) .
			Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
			($parent ? Xml::hidden( 'wpParent', $parent ) : '') .
			'<div>' .
			Xml::openElement( 'textarea', array(
				'name' => 'wpReply',
				'id' => "wpReplyTo{$parent}",
				'cols' => 40,
				'rows' => 5 ) ) .
			$text .
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
