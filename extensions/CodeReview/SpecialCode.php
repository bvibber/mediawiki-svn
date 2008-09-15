<?php
if (!defined('MEDIAWIKI')) die();

class SpecialCode extends SpecialPage {
	function __construct() {
		parent::__construct( 'Code' );
	}

	function execute( $subpage ) {
		global $wgOut, $wgRequest, $wgUser;

		wfLoadExtensionMessages( 'CodeReview' );

		$this->setHeaders();

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

	function formatMessage( $value ){
		$value = nl2br( htmlspecialchars( $value ) );
		$value = preg_replace_callback( '/\br(\d+)\b/', array( $this, 'messageRevLink' ), $value );
		$value = preg_replace_callback( '/\bbug (\d+)\b/i', array( $this, 'messageBugLink' ), $value );
		return "<code>$value</code>";
	}

	function messageBugLink( $arr ){
		$text = $arr[0];
		$bugNo = intval( $arr[1] );
		$url = $this->mRepo->getBugPath( $bugNo );
		return $this->mSkin->makeExternalLink( $url, $text );
	}
	
	function messageRevLink( $matches ) {
		$text = $matches[0];
		$rev = intval( $matches[1] );
		
		$repo = $this->mRepo->getName();
		$title = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );
		
		return $this->mSkin->link( $title, $text );
	}

	function messageFragment( $value ) {
		global $wgLang;
		$message = trim( $value );
		$lines = explode( "\n", $message, 2 );
		$first = $lines[0];
		$trimmed = $wgLang->truncate( $first, 60, '...' );
		return htmlspecialchars( $trimmed );
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
			return $wgUser->getSkin()->link( SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() . '/' . $value ), htmlspecialchars( $value ) );
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
		$this->mRev = $this->mRepo->getRevision( intval( $rev ) );
	}

	function execute(){
		global $wgOut, $wgUser;
		$repoLink = $wgUser->getSkin()->link( SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() ), htmlspecialchars( $this->mRepo->getName() ) );
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
			$action = wfMsgHtml( 'code-rev-modified-'.strtolower( $row->cp_action ) );
			$encPath = htmlspecialchars( $row->cp_path );
			$paths .= "<li>$encPath ($action)</li>\n";
		}
		if( $paths ){
			$paths = "<ul>\n$paths</ul>";
		}
		$html = '<table>
<tr><td>' . wfMsgHtml( 'code-rev-repo' ) . '</td><td>' . $repoLink . '</td></tr>
<tr><td>' . wfMsgHtml( 'code-rev-rev' ) . '</td><td>' . $revText . '</td></tr>
<tr><td>' . wfMsgHtml( 'code-rev-author' ) . '</td><td>' . $this->authorLink( $this->mRev->getAuthor() ) . '</td></tr>
<tr><td>' . wfMsgHtml( 'code-rev-message' ) . '</td><td>' . $this->formatMessage( $this->mRev->getMessage() ) . '</td></tr>
<tr><td>' . wfMsgHtml( 'code-rev-paths' ) . '</td><td>' . $paths . '</td></tr>
</table>';
		$wgOut->addHtml( $html );
	}
}
