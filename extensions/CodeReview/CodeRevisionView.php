<?php

// Special:Code/MediaWiki/40696
class CodeRevisionView extends CodeView {

	function __construct( $repoName, $rev, $replyTarget=null ){
		global $wgRequest;
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
		$this->mRev = $this->mRepo ? $this->mRepo->getRevision( intval( $rev ) ) : null;
		$this->mPreviewText = false;
		$this->mReplyTarget = $replyTarget ? 
			(int)$replyTarget : $wgRequest->getIntOrNull( 'wpParent' );
		$this->mSkipCache = ($wgRequest->getVal( 'action' ) == 'purge');
	}

	function execute(){
		global $wgOut, $wgUser, $wgLang;
		if( !$this->mRepo ) {
			$view = new CodeRepoListView();
			$view->execute();
			return;
		}
		if( !$this->mRev ) {
			$view = new CodeRevisionListView( $this->mRepo->getName() );
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
			$paths = "<div class='mw-codereview-paths'><ul>\n$paths</ul></div>\n";
		}
		$fields = array(
			'code-rev-repo' => $repoLink,
			'code-rev-rev' => $revText,
			'code-rev-date' => $wgLang->timeanddate( $this->mRev->getTimestamp() ),
			'code-rev-author' => $this->authorLink( $this->mRev->getAuthor() ),
			'code-rev-status' => $this->statusForm(),
			'code-rev-tags' => $this->tagForm(),
			'code-rev-message' => $this->formatMessage( $this->mRev->getMessage() ),
			'code-rev-paths' => $paths,
		);
		$special = SpecialPage::getTitleFor( 'Code', $this->mRepo->getName().'/'.$this->mRev->getId() );

		$html = Xml::openElement( 'form', array( 'action' => $special->getLocalUrl(), 'method' => 'post' ) );
		$html .= '<div>' .
			Xml::submitButton( wfMsg( 'code-rev-submit' ), array( 'name' => 'wpSave' ) ) .
			' ' .
			Xml::submitButton( wfMsg( 'code-rev-submit-next' ), array( 'name' => 'wpSaveAndNext' ) ) .
			' ' .
			Xml::submitButton( wfMsg( 'code-rev-comment-preview' ), array( 'name' => 'wpPreview' ) ) .
			'</div>';
		
		$html .= $this->formatMetaData( $fields );

		if( $this->mRev->isDiffable() ) {
			$diffHtml = $this->formatDiff();
			$html .=
				"<h2>" . wfMsgHtml( 'code-rev-diff' ) .
				' <small>[' . $wgUser->getSkin()->makeLinkObj( $special,
					wfMsg('code-rev-purge-link'), 'action=purge' ) . ']</small></h2>' .
				"<div class='mw-codereview-diff' id='mw-codereview-diff'>" . $diffHtml . "</div>\n";
		}
		$comments = $this->formatComments();
		if( $comments ) {
			$html .= "<h2 id='code-comments'>". wfMsgHtml( 'code-comments' ) ."</h2>\n" . $comments;
		}
		
		if( $this->mReplyTarget ) {
			global $wgJsMimeType;
			$id = intval( $this->mReplyTarget );
			$html .= "<script type=\"$wgJsMimeType\">addOnloadHook(function(){" .
				"document.getElementById('wpReplyTo$id').focus();" .
				"});</script>\n";
		}
		$html .= '<div>' .
			Xml::submitButton( wfMsg( 'code-rev-submit' ), array( 'name' => 'wpSave' ) ) .
			' ' .
			Xml::submitButton( wfMsg( 'code-rev-submit-next' ), array( 'name' => 'wpSaveAndNext' ) ) .
			' ' .
			Xml::submitButton( wfMsg( 'code-rev-comment-preview' ), array( 'name' => 'wpPreview' ) ) .
			'</div>' . 
			'</form>';

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
		if( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getVal('wpEditToken') ) ) {
			// Look for a posting...
			$text = $wgRequest->getText( "wpReply{$this->mReplyTarget}" );
			$parent = $wgRequest->getIntOrNull( 'wpParent' );
			$review = $wgRequest->getInt( 'wpReview' );
			$isPreview = $wgRequest->getCheck( 'wpPreview' );
			if( $isPreview ) {
				// Save the text for reference on later comment display...
				$this->mPreviewText = $text;
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
	
	function tagForm() {
		global $wgUser;
		$tags = $this->mRev->getTags();
		$list = '';
		if( count($tags) ) {
			$list = implode( ", ",
				array_map(
					array( $this, 'formatTag' ),
					$tags ) 
			) . '&nbsp;';
		}
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
			return
				Xml::openElement( 'select', array( 'name' => 'wpStatus' ) ) .
				$this->buildStatusList() .
				'</select>';
		} else {
			return htmlspecialchars( $this->statusDesc( $this->mRev->getStatus() ) );
		}
	}
	
	function buildStatusList() {
		$states = CodeRevision::getPossibleStates();
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
		return '<div><table><tr><td>' .
			Xml::inputLabel( wfMsg('code-rev-tag-add'), 'wpTag', 'wpTag', '' ) .
			'</td><td>&nbsp;</td><td>' .
			Xml::inputLabel( wfMsg('code-rev-tag-remove'), 'wpRemoveTag', 'wpRemoveTag', '' ) .
			'</td></tr></table></div>';
	}
	
	function formatTag( $tag ) {
		global $wgUser;
		$repo = $this->mRepo->getName();
		$special = SpecialPage::getTitleFor( 'Code', "$repo/tag/$tag" );
		return $this->mSkin->link( $special, htmlspecialchars( $tag ) );
	}

	function formatDiff() {
		global $wgEnableAPI;
		
		// Asynchronous diff loads will require the API
		// And JS in the client, but tough shit eh? ;)
		$deferDiffs = $wgEnableAPI;
		
		if( $this->mSkipCache ) {
			// We're purging the cache on purpose, probably
			// because the cached data was corrupt.
			$cache = 'skipcache';
		} elseif( $deferDiffs ) {
			// If data is already cached, we'll take it now;
			// otherwise defer the load to an AJAX request.
			// This lets the page be manipulable even if the
			// SVN connection is slow or uncooperative.
			$cache = 'cached';
		} else {
			$cache = '';
		}
		$diff = $this->mRepo->getDiff( $this->mRev->getId(), $cache );
		if( !$diff && $deferDiffs ) {
			// We'll try loading it by AJAX...
			return $this->stubDiffLoader();
		}
		$hilite = new CodeDiffHighlighter();
		return $hilite->render( $diff );
	}
	
	function stubDiffLoader() {
		global $wgOut, $wgScriptPath;
		$encRepo = Xml::encodeJsVar( $this->mRepo->getName() );
		$encRev = Xml::encodeJsVar( $this->mRev->getId() );
		$wgOut->addScriptFile( "$wgScriptPath/extensions/CodeReview/codereview.js" );
		$wgOut->addInlineScript(
			"addOnloadHook(
				function() {
					CodeReview.loadDiff($encRepo,$encRev);
				}
			);" );
		return "Loading diff...";
	}

	function formatComments() {
		$comments = implode( "\n",
				array_map(
					array( $this, 'formatCommentInline' ),
					$this->mRev->getComments() ) ) .
			$this->postCommentForm();
		if( !$comments ) {
			return false;
		}
		return "<div class='mw-codereview-comments'>$comments</div>";
	}

	function formatCommentInline( $comment ) {
		if( $comment->id === $this->mReplyTarget ) {
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
	
	function revLink() {
		$repo = $this->mRepo->getName();
		$rev = $this->mRev->getId();
		$title = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );
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
		if( !$wgUser->isAllowed('codereview-post-comment') ) {
			return '';
		}
		return '<div class="mw-codereview-post-comment">' .
			$preview .
			Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
			($parent ? Xml::hidden( 'wpParent', $parent ) : '') .
			'<div>' .
			Xml::openElement( 'textarea', array(
				'name' => "wpReply{$parent}",
				'id' => "wpReplyTo{$parent}",
				'cols' => 40,
				'rows' => 5 ) ) .
			$text .
			'</textarea>' .
			'</div>' .
			'</div>';
	}
}
