<?php

// Special:Code/MediaWiki
class CodePathRevListView extends CodeView {
	function __construct( $repoName ) {
		global $wgRequest;
		parent::__construct();
		$this->mRepo = CodeRepository::newFromName( $repoName );
		$this->mPath = htmlspecialchars( trim( $wgRequest->getVal( 'path' ) ) );
		if( strlen($this->mPath) && $this->mPath[strlen($this->mPath)-1] !== '/' ) {
			$this->mPath .= '/'; // make sure this is a dir
		}
		if( strlen($this->mPath) && $this->mPath[0] !== '/' ) {
			$this->mPath = "/{$this->mPath}"; // make sure this is a dir
		}
	}

	function execute() {
		global $wgOut;
		if( !$this->mRepo ) {
			$view = new CodeRepoListView();
			$view->execute();
			return;
		}
		$this->showForm();
		// Path should have a bit of length...
		if( strlen($this->mPath) > 3 ) {
			$pager = $this->getPager();
			$wgOut->addHtml( 
				$pager->getNavigationBar() .
				$pager->getLimitForm() . 
				$pager->getBody() . 
				$pager->getNavigationBar()
			);
		}
	}
	
	function showForm() {
		global $wgOut, $wgScript;
		$special = SpecialPage::getTitleFor( 'Code', $this->mRepo->getName().'/path' );
		$action = $wgScript;
		$wgOut->addHTML( "<form action=\"$action\" method=\"get\">\n" .
			"<fieldset><legend>".wfMsgHtml('code-pathsearch-legend')."</legend>" .
				Xml::hidden( 'title', $special->getPrefixedDBKey() ) .
				Xml::inputlabel( wfMsg("code-pathsearch-path"), 'path', 'path', 55, $this->mPath ) .
				'&nbsp;' . Xml::submitButton( wfMsg( 'allpagessubmit' ) ) . "\n" .
			"</fieldset></form>"
		);
	}
	
	function getPager() {
		return new SvnPathRevTablePager( $this, $this->mPath );
	}
}

// Pager for CodeRevisionListView
class SvnPathRevTablePager extends TablePager {

	function __construct( CodePathRevListView $view, $path ){
		global $IP;
		$this->mView = $view;
		$this->mRepo = $view->mRepo;
		$this->mDefaultDirection = true;
		$this->mCurSVN = SpecialVersion::getSvnRevision( $IP );
		$this->mPath = $path;
		parent::__construct( $view );
	}

	function isFieldSortable( $field ){
		return $field == 'cp_rev_id';
	}

	function getDefaultSort(){ return 'cp_rev_id'; }

	function getQueryInfo(){
		return array(
			'tables' => array( 'code_paths', 'code_rev', 'code_comment' ),
			'fields' => array_keys( $this->getFieldNames() ),
			'conds' => array( 
				'cp_repo_id' => $this->mRepo->getId(),
				'cp_repo_id = cr_repo_id',
				'cp_rev_id = cr_id',
				'cp_path LIKE '.$this->mDb->addQuotes($this->mPath.'%'),
				// performance
				'cp_rev_id > '.$this->mRepo->getLastStoredRev() - 20000
			),
			'options' => array( 'GROUP BY' => 'cp_rev_id',
				'USE INDEX' => array( 'code_path' => 'cp_repo_id') ),
			'join_conds' => array( 
				'code_comment' => array( 'LEFT JOIN', 'cc_repo_id = cr_repo_id AND cc_rev_id = cr_id' )
			)
		);
	}

	function getFieldNames(){
		return array(
			'cp_rev_id' => wfMsg( 'code-field-id' ),
			'cr_status' => wfMsg( 'code-field-status' ),
			'COUNT(cc_rev_id)' => wfMsg( 'code-field-comments' ),
			'cr_path' => wfMsg( 'code-field-path' ),
			'cr_message' => wfMsg( 'code-field-message' ),
			'cr_author' => wfMsg( 'code-field-author' ),
			'cr_timestamp' => wfMsg( 'code-field-timestamp' ),
		);
	}

	function formatValue( $name, $value ){
		global $wgUser, $wgLang;
		switch( $name ){
		case 'cp_rev_id':
			return $this->mView->mSkin->link(
				SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() . '/' . $value ),
				htmlspecialchars( $value ) );
		case 'cr_status':
			return $this->mView->mSkin->link(
				SpecialPage::getTitleFor( 'Code',
					$this->mRepo->getName() . '/status/' . $value ),
				htmlspecialchars( $this->mView->statusDesc( $value ) ) );
		case 'cr_author':
			return $this->mView->authorLink( $value );
		case 'cr_message':
			return $this->mView->messageFragment( $value );
		case 'cr_timestamp':
			global $wgLang;
			return $wgLang->timeanddate( $value );
		case 'COUNT(cc_rev_id)':
			return intval( $value );
		case 'cr_path':
			return $wgLang->truncate( $value, 30, '...' );
		}
	}
	
	// Note: this function is poorly factored in the parent class
	function formatRow( $row ) {
		global $wgWikiSVN;
		$class = "mw-codereview-status-{$row->cr_status}";
		if($this->mRepo->mName == $wgWikiSVN){
			$class .= " mw-codereview-" . ( $row->cp_rev_id <= $this->mCurSVN ? 'live' : 'notlive' );
		}
		return str_replace(
			'<tr>',
			Xml::openElement( 'tr',
				array( 'class' => $class ) ),
				parent::formatRow( $row ) );
	}

	function getTitle(){
		return SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() . '/path' );
	}
}
