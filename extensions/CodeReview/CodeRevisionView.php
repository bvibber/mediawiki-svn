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
		$html = '<table>
<tr><td valign="top">' . wfMsgHtml( 'code-rev-repo' ) . '</td><td valign="top">' . $repoLink . '</td></tr>
<tr><td valign="top">' . wfMsgHtml( 'code-rev-rev' ) . '</td><td valign="top">' . $revText . '</td></tr>
<tr><td valign="top">' . wfMsgHtml( 'code-rev-author' ) . '</td><td valign="top">' . $this->authorLink( $this->mRev->getAuthor() ) . '</td></tr>
<tr><td valign="top">' . wfMsgHtml( 'code-rev-message' ) . '</td><td valign="top">' . $this->formatMessage( $this->mRev->getMessage() ) . '</td></tr>
<tr><td valign="top">' . wfMsgHtml( 'code-rev-paths' ) . '</td><td valign="top">' . $paths . '</td></tr>
</table>';
		$html .=
			"<h2>" . wfMsgHtml( 'code-rev-diff' ) . "</h2>" .
			"<div class='mw-codereview-diff'>" .
			$this->formatDiff() .
			"</div>";

		$html .=
			"<h2>Comments</h2>" .
			$this->formatComments();
		
		if( $this->mReplyTarget ) {
			$id = intval( $this->mReplyTarget );
			$html .= "<script>addOnloadHook(function(){" .
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
			$isPreview = $wgRequest->getCheck( 'wpCommentPreview' );
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
			'</div>' .
			'</form>';
	}
}
