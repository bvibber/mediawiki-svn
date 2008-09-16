<?php

class CodeRevisionAuthorView extends CodeRevisionListView {
	function __construct( $repoName, $author ) {
		parent::__construct( $repoName );
		$this->mAuthor = $author;
	}
	
	function getPager() {
		return new SvnRevAuthorTablePager( $this, $this->mAuthor );
	}
}

class SvnRevAuthorTablePager extends SvnRevTablePager {
	function __construct( $view, $author ) {
		parent::__construct( $view );
		$this->mAuthor = $author;
	}
	
	function getQueryInfo() {
		$info = parent::getQueryInfo();
		$info['conds']['cr_author'] = $this->mAuthor; // fixme: normalize input?
		return $info;
	}

	function getTitle(){
		$repo = $this->mRepo->getName();
		return SpecialPage::getTitleFor( 'Code', "$repo/author/$this->mAuthor" );
	}
}
