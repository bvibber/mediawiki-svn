<?php

// Special:Code/MediaWiki
class CodeStatusChangeListView extends CodeView {
	public $mRepo;

	function __construct( $repoName ) {
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
	}

	function execute() {
		global $wgOut;
		$pager = $this->getPager();
		$wgOut->addHTML(
			$pager->getNavigationBar() .
			$pager->getLimitForm() .
			$pager->getBody() .
			$pager->getNavigationBar()
		);
	}

	function getPager() {
		return new CodeStatusChangeTablePager( $this );
	}
	function getRepo() {
		return $this->mRepo;
	}
}

// Pager for CodeRevisionListView
class CodeStatusChangeTablePager extends TablePager {

	function __construct( CodeStatusChangeListView $view ) {
		global $IP;
		$this->mView = $view;
		$this->mRepo = $view->mRepo;
		$this->mDefaultDirection = true;
		$this->mCurSVN = SpecialVersion::getSvnRevision( $IP );
		parent::__construct();
	}

	function isFieldSortable( $field ) {
		return $field == 'cpc_timestamp';
	}

	function getDefaultSort() { return 'cpc_timestamp'; }

	function getQueryInfo() {
		return array(
			'tables' => array( 'code_prop_changes', 'code_rev' ),
			'fields' => array_keys( $this->getFieldNames() ),
			'conds' => array( 'cpc_repo_id' => $this->mRepo->getId() ),
			'join_conds' => array(
				'code_rev' => array( 'LEFT JOIN', 'cpc_repo_id = cr_repo_id AND cpc_rev_id = cr_id' )
			)
		);
	}

	function getFieldNames() {
		return array(
			'cpc_timestamp' => wfMsg( 'code-field-timestamp' ),
			'cpc_user_text' => wfMsg( 'code-field-user' ),
			'cpc_rev_id' => wfMsg( 'code-field-id' ),
			'cpc_removed' => wfMsg( 'code-field-text' ),
			'cpc_removed' => wfMsg( 'code-old-status' ),
			'cpc_added' => wfMsg( 'code-new-status' ),
			'cr_status' => wfMsg( 'code-field-status' ),
		);
	}

	function formatValue( $name, $value ) {
		global $wgUser, $wgLang;
		switch( $name ) {
		case 'cpc_rev_id':
			return $this->mView->mSkin->link(
				SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() . '/' . $value . '#code-changes' ),
				htmlspecialchars( $value ) );
		case 'cr_status':
			return $this->mView->mSkin->link(
				SpecialPage::getTitleFor( 'Code',
					$this->mRepo->getName() . '/status/' . $value ),
				htmlspecialchars( $this->mView->statusDesc( $value ) ) );
		case 'cpc_user_text':
			return $this->mView->mSkin->userLink( -1, $value );
		case 'cpc_removed':
			return wfMsgHtml( $value ? "code-status-$value" : "code-status-new" );
		case 'cpc_added':
			return wfMsgHtml( "code-status-$value" );
		case 'cpc_timestamp':
			global $wgLang;
			return $wgLang->timeanddate( $value, true );
		}
	}

	// Note: this function is poorly factored in the parent class
	function formatRow( $row ) {
		global $wgWikiSVN;
		$class = "mw-codereview-status-{$row->cr_status}";
		if ( $this->mRepo->mName == $wgWikiSVN ) {
			$class .= " mw-codereview-" . ( $row->cpc_rev_id <= $this->mCurSVN ? 'live' : 'notlive' );
		}
		return str_replace(
			'<tr>',
			Xml::openElement( 'tr',
				array( 'class' => $class ) ),
				parent::formatRow( $row ) );
	}

	function getTitle() {
		return SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() . '/statuschanges' );
	}
}
