<?php

// Please don't use this interface publicly yet.
// I don't really know what I'm doing in the API and it might explode. ;)
// -- brion

class ApiCodeDiff extends ApiBase {

	public function execute() {
		$params = $this->extractRequestParams();
		
		if( !isset( $params['repo'] ) ) {
			$this->dieUsageMsg( array( 'missingparam', 'repo' ) );
		}
		if( !isset( $params['rev'] ) ) {
			$this->dieUsageMsg( array( 'missingparam', 'rev' ) );
		}
		
		$repo = CodeRepository::newFromName( $params['repo'] );
		if( !$repo ){
			throw new MWException( "Invalid repo {$args[0]}" );
		}
		
		$svn = SubversionAdaptor::newFromRepo( $repo->getPath() );
		$lastStoredRev = $repo->getLastStoredRev();
		
		$rev = intval( $params['rev'] );
		if( $rev <= 0 || $rev > $lastStoredRev ) {
			/// @fixme more sensible error
			throw new MWException( 'Invalid input revision' );
		}
		
		$diff = $repo->getDiff( $rev );
		
		if( $diff ) {
			$hilite = new CodeDiffHighlighter();
			$html = $hilite->render( $diff );
		} else {
			$html = 'Failed to load diff.';
		}
		
		$data = array();
		$data['repo'] = $params['repo'];
		$data['id'] = $rev;
		$data['diff'] = $html;
		$this->getResult()->addValue( 'code', 'rev', $data );
	}
	
	public function getAllowedParams() {
		return array(
			'repo' => null,
			'rev' => null );
	}
	
	public function getParamDescription() {
		return array(
			'repo' => 'Name of repository to look at',
			'rev' => 'Revision ID to fetch diff of' );
	}
	
	public function getDescription() {
		return array(
			'Fetch formatted diff from CodeReview\'s backing revision control system.' );
	}
	
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
