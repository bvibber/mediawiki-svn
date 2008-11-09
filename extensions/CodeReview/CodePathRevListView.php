<?php

// Special:Code/MediaWiki
class CodePathRevListView extends CodeRevisionListView {
	function __construct( $repoName ) {
		parent::__construct( $repoName );
	}

	function execute() {
		global $wgOut;
		if( !$this->mRepo ) {
			$view = new CodeRepoListView();
			$view->execute();
			return;
		}
		// No path! Use regular lister!
		if( !$this->mPath ) {
			$view = new CodeRevisionListView( $this->mRepo->getName() );
			$view->execute();
			return;
		}
		$this->showForm();
		// Path should have a bit of length...
		if( strlen($this->mPath) > 3 ) {
			$pager = $this->getPager();
			$wgOut->addHTML( 
				$pager->getNavigationBar() .
				$pager->getLimitForm() . 
				$pager->getBody() . 
				$pager->getNavigationBar()
			);
		}
	}
	
	function getPager() {
		return new SvnPathRevTablePager( $this );
	}
}

// Pager for CodeRevisionListView
class SvnPathRevTablePager extends SvnRevTablePager {
	function getTitle() {
		return SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() . '/path' );
	}
}
