<?php
if (!defined('MEDIAWIKI')) die();

class SpecialCode extends SpecialPage {
	function __construct() {
		parent::__construct( 'Code' );
	}

	function execute( $subpage ) {
		global $wgOut, $wgRequest, $wgUser, $wgScriptPath;

		wfLoadExtensionMessages( 'CodeReview' );

		$this->setHeaders();
		$wgOut->addStyle( "$wgScriptPath/extensions/CodeReview/codereview.css" );

		if( $subpage == '' ) {
			$view = new CodeRepoListView();
		} else {
			$params = explode( '/', $subpage );
			switch( count( $params ) ) {
			case 1:
				$view = new CodeRevisionListView( $params[0] );
				break;
			case 2:
				$view = new CodeRevisionView( $params[0], $params[1] );
				break;
			default:
				throw new MWException( "Unexpected number of parameters" );
			}
		}
		$view->execute();
	}
}

/**
 * Extended by CodeRevisionListView and CodeRevisionView
 */
abstract class CodeView {
	var $mRepo;

	function __construct() {
		global $wgUser;
		$this->mSkin = $wgUser->getSkin();
	}
	
	abstract function execute();

	function authorLink( $author ) {
		static $userLinks = array();
		if( isset( $userLinks[$author] ) )
			return $userLinks[$author];

		$dbr = wfGetDB( DB_SLAVE );
		$wikiUser = $dbr->selectField(
			'code_authors',
			'ca_user_text',
			array(
				'ca_repo_id' => $this->mRepo->getId(),
				'ca_author' => $author,
			),
			__METHOD__
		);
		$user = null;
		if( $wikiUser )
			$user = User::newFromName( $wikiUser );
		if( $user instanceof User )
			$link = $author . ' (' . $this->mSkin->userLink( $user->getId(), $user->getName() ) . ')';
		else
			$link = htmlspecialchars( $author );
		return $userLinks[$author] = $link;
	}

	function formatMessage( $text ){
		$text = nl2br( htmlspecialchars( $text ) );
		$linker = new CodeCommentLinkerHtml( $this->mRepo );
		return $linker->link( $text );
	}

	function messageFragment( $value ) {
		global $wgLang;
		$message = trim( $value );
		$lines = explode( "\n", $message, 2 );
		$first = $lines[0];
		$trimmed = $wgLang->truncate( $first, 60, '...' );
		return $this->formatMessage( $trimmed );
	}
}

class CodeCommentLinker {
	function __construct( $repo ) {
		global $wgUser;
		$this->mSkin = $wgUser->getSkin();
		$this->mRepo = $repo;
	}
	
	function link( $text ) {
		$text = preg_replace_callback( '/\br(\d+)\b/', array( $this, 'messageRevLink' ), $text );
		$text = preg_replace_callback( '/\bbug #?(\d+)\b/i', array( $this, 'messageBugLink' ), $text );
		return $text;
	}

	function messageBugLink( $arr ){
		$text = $arr[0];
		$bugNo = intval( $arr[1] );
		$url = $this->mRepo->getBugPath( $bugNo );
		
		return $this->makeExternalLink( $url, $text );
	}
	
	function messageRevLink( $matches ) {
		$text = $matches[0];
		$rev = intval( $matches[1] );
		
		$repo = $this->mRepo->getName();
		$title = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );
		
		return $this->makeInternalLink( $title, $text );
	}

}

class CodeCommentLinkerHtml extends CodeCommentLinker {
	function makeExternalLink( $url, $text ) {
		return $this->mSkin->makeExternalLink( $url, $text );
	}
	
	function makeInternalLink( $title, $text ) {
		return $this->mSkin->link( $title, $text );
	}
}

class CodeCommentLinkerWiki extends CodeCommentLinker {
	function makeExternalLink( $url, $text ) {
		return "[$url $text]";
	}
	
	function makeInternalLink( $title, $text ) {
		return "[[" . $title->getPrefixedText() . "|$text]]";
	}
}

// Special:Code
class CodeRepoListView {

	function execute() {
		global $wgOut;
		$repos = CodeRepository::getRepoList();
		if( !count( $repos ) ){
			$wgOut->addWikiMsg( 'code-no-repo' );
			return;
		}
		$text = '';
		foreach( $repos as $repo ){
			$name = $repo->getName();
			$text .= "* [[Special:Code/$name|$name]]\n";
		}
		$wgOut->addWikiText( $text );
	}
}

// Special:Code/MediaWiki
class CodeRevisionListView extends CodeView {
	function __construct( $repoName ) {
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
	}

	function execute() {
		global $wgOut;
		if( !$this->mRepo ) {
			$view = new CodeRepoListView();
			$view->execute();
			return;
		}
		$pager = new SvnRevTablePager( $this );
		$wgOut->addHtml( $pager->getBody() . $pager->getNavigationBar() );
	}
}

// Pager for CodeRevisionListView
class SvnRevTablePager extends TablePager {

	function __construct( CodeRevisionListView $view ){
		$this->mView = $view;
		$this->mRepo = $view->mRepo;
		$this->mDefaultDirection = true;
		parent::__construct();
	}

	function isFieldSortable( $field ){
		return $field == 'cr_id';
	}

	function getDefaultSort(){ return 'cr_id'; }

	function getQueryInfo(){
		return array(
			'tables' => array( 'code_rev' ),
			'fields' => array_keys( $this->getFieldNames() ),
			'conds' => array( 'cr_repo_id' => $this->mRepo->getId() ),
		);
	}

	function getFieldNames(){
		return array(
			'cr_id' => wfMsg( 'code-field-id' ),
			'cr_message' => wfMsg( 'code-field-message' ),
			'cr_author' => wfMsg( 'code-field-author' ),
			'cr_timestamp' => wfMsg( 'code-field-timestamp' ),
		);
	}

	function formatValue( $name, $value ){
		global $wgUser, $wgLang;
		switch( $name ){
		case 'cr_id':
			global $wgUser;
			return $wgUser->getSkin()->link( 
				SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() . '/' . $value ), htmlspecialchars( $value )
			);
		case 'cr_author':
			return $this->mView->authorLink( $value );
		case 'cr_message':
			return $this->mView->messageFragment( $value );
		case 'cr_timestamp':
			global $wgLang;
			return $wgLang->timeanddate( $value );
		}
	}

	function getTitle(){
		return SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() );
	}
}

// Special:Code/MediaWiki/40696
class CodeRevisionView extends CodeView {

	function __construct( $repoName, $rev ){
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
		$this->mRev = $this->mRepo ? $this->mRepo->getRevision( intval( $rev ) ) : null;
	}

	function execute(){
		global $wgOut, $wgUser;
		if( !$this->mRepo || !$this->mRev ) {
			$view = new CodeRepoListView();
			$view->execute();
			return;
		}
		
		$this->checkPostings();
		
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
		$wgOut->addHtml( $html );
	}
	
	function checkPostings() {
		global $wgRequest, $wgUser;
		if( $wgRequest->wasPosted()
			&& $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) ) {
			// Look for a posting...
			$text = $wgRequest->getText( 'wpTextbox1' );
			$parent = $wgRequest->getIntOrNull( 'wpParent' );
			$review = $wgRequest->getInt( 'wpReview' );
			$isPreview = $wgRequest->getCheck( 'wpCommentPreview' );
			if( $isPreview ) {
				// NYI
			} else {
				$this->mRev->saveComment( $text, $review, $parent );
				// fixme -- won't show up in slave load
			}
		}
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
					array( $this, 'formatComment' ),
					$this->mRev->getComments() ) ) .
			$this->postCommentForm() .
			"</div>";
	}
	
	function formatComment( $comment ) {
		global $wgOut, $wgLang;
		$linker = new CodeCommentLinkerWiki( $this->mRepo );
		return '<div class="mw-codereview-comment">' .
			'<div class="mw-codereview-comment-meta">' .
			wfMsgHtml( 'code-rev-comment-by' ) . ' ' .
			$this->mSkin->userLink( $comment->user, $comment->userText ) .
			$this->mSkin->userToolLinks( $comment->user, $comment->userText ) .
			' ' .
			$wgLang->timeanddate( $comment->timestamp ) .
			'</div>' .
			'<div class="mw-codereview-comment-text">' .
			$wgOut->parse( $linker->link( $comment->text ) ) .
			'</div>' .
			'</div>';
	}
	
	function postCommentForm( $parent=null ) {
		global $wgUser;
		return '<div class="mw-codereview-post-comment">' .
			Xml::openElement( 'form',
				array(
					'action' => '', // fixme
					'method' => 'post' ) ) .
			Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
			Xml::hidden( 'wpCodeRepo', $this->mRepo->getName() ) .
			Xml::hidden( 'wpCodeRev', $this->mRev->getId() ) .
			($parent ? Xml::hidden( 'wpCodeParent', $parent ) : '') .
			'<div>' .
			Xml::textArea( 'wpTextbox1', '' ) .
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