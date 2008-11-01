<?php

class CodeBrowseView {
	static function newFromPath( $path, $request ) {
		$error = null;
		if ( !wfRunHooks( 'CodeBrowseBeforeView', array( &$path, &$request, &$error ) ) )
			return new CodeBrowseErrorView( $path, $request, $error );
		if ( ltrim( $path, '/' ) == '' )
			return new CodeBrowseRepoListView( $path, $request );
		else
			return new CodeBrowseItemView( $path, $request );
	}
	
	function __construct( $path, $request ) {
		$this->mPath = $path;
		$this->mAction = $request->getText( 'action', 'view' );
		$this->mRev = $request->getText( 'rev', 'HEAD' );
	}
	
	function getHeader() {
		return '';		
	}
	
	function getContent() {
		return '';
	}
	
	function getFooter() {
		return '';
	}
	
	
}

class CodeBrowseErrorView {
	function __construct( $path, $request, $error ) {
		parent::__construct( $path, $request );
		$this->mError = $error;
	}
	function getContent() {
		if ( is_array( $this->mError ) ) {
			$args = $this->mError;
			$key = array_shift( $args );
		} else {
			$args = array();
			$key = $this->mError;
		}
		
		global $wgOut;
		return $wgOut->parse( wfMsgReal( $key, $args ) );
	}
}