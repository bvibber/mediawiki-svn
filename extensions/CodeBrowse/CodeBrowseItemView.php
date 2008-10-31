<?php

class CodeBrowseItemView extends CodeBrowseView {
	function __construct( $path, $request ) {
		parent::__construct( $path, $request );
		$parts = explode( '/', $path, 2 );
		$this->mRepoName = $parts[0];
		$this->mBasePath = '/'.$parts[1];
		$this->mRepository = CodeRepository::newFromName( $this->mRepoName );
	}
	
	function getContent() {
		if ( is_null( $this->mRepository ) )
			return wfMsgHtml( 'codebrowse-not-found', $this->mRepoName );
		$this->svn = SubversionAdaptor::newFromRepo( $this->mRepository->getPath() );
		$contents = $this->svn->getDirList( $this->mBasePath );
		
		if ( !is_array( $contents ) )
			return wfMsgHtml( 'codebrowse-not-found', $this->mPath ); // FIXME
		if ( count( $contents ) == 1 && $contents[0]['type'] == 'file' ) {
			return $this->showFile();
		} else {
			if ( substr( $this->mPath, -1 ) !== '/' ) {
				$this->mPath .= '/';
				$this->mBasePath .= '/';
			}
			return $this->listDir( $contents );
		}
		
	}
	
	function listDir( $contents ) {

		$html = "<h2>".wfMsgHtml( 'codebrowse-dir-listing', $this->mPath )."</h2>\n".
			"<table id=\"codebrowse-dir-listing\">\n". 
			"<tr><th>".wfMsgHtml( 'codebrowse-name' )."</th><th>".
			wfMsgHtml( 'codebrowse-revision' )."</th><th>".wfMsgHtml( 'codebrowse-lastmodifier' ).
			"</th><th>".wfMsgHtml( 'codebrowse-lastchange' )."</th><th>".
			wfMsgHtml( 'codebrowse-size' )."</th></tr>\n";
		
		$dirs = array();
		$files = array();
		foreach ( $contents as $item ) {
			if ( $item['type'] == 'dir' )
				$dirs[$item['name']] = $item;
			else
				$files[$item['name']] = $item;
		}
		
		ksort( $dirs );
		ksort( $files );
		
		foreach ( $dirs as $dir )
			$html .= $this->contentLine( $dir );
		foreach ( $files as $file )
			$html .= $this->contentLine( $file );
		$html .= "</table>";
		return $html;
	}
	
	function contentLine( $item ) {
		global $wgUser, $wgLang;
		$sk = $wgUser->getSkin();
		
		$name = $item['name'];
		if ( $item['type'] == 'dir' && substr( $name, -1 ) != '/' )
			$name .= '/'; 
		
		return 
			"\t<tr><td>".$sk->link( SpecialPage::getTitleFor( 'CodeBrowse'  ), 
				$name, array(), array( 'path' => $this->mPath.$name )
				 ).
			"</td><td>".$item['created_rev'].
			"</td><td>".$sk->link( SpecialPage::getTitleFor( 
				'Code', "{$this->mRepoName}/author/{$item['last_author']}" ), $item['last_author'] ).
			"</td><td>".$wgLang->timeanddate( $item['time_t'] ).
			"</td><td>".$wgLang->formatSize( $item['size'] ).
			"</td></tr>\n";
			
	}
	
	function showFile() {
		$contents = $this->svn->getFile( $this->mBasePath, $this->mRev == 'HEAD' ? null : $this->mRev );
		if ( $this->mAction == 'raw' || $this->mAction == 'download' ) {
			global $wgOut;
			$wgOut->disable();
			
			if ( $this->mAction == 'raw' )
				header( 'Content-Type: text/plain' );
			else
				header( 'Content-Type: application/octet-stream' );
			header( 'Content-Length: '.strlen( $contents ) );
			print $contents;
			return;
		}
		
		return Xml::element( 'pre', array( 'style' => 'overflow: auto;' ), $contents );			
	}
}
