<?php

// Special:Code/MediaWiki
class CodePathRevListView extends CodeRevisionListView {
	function __construct( $repoName ) {
		global $wgRequest;
		parent::__construct( $repoName );
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
			$wgOut->addHtml( 
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
	function getSVNPath() {
		return $this->mView->mPath;
	}

	function getTitle() {
		return SpecialPage::getTitleFor( 'Code', $this->mRepo->getName() . '/path' );
	}
}
