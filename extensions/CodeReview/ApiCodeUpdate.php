<?php

class ApiCodeUpdate extends ApiBase {

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
		
		$endRev = intval( $params['rev'] );
		if( $lastStoredRev >= $endRev ) {
			// Nothing to do, we're up to date.
			return;
		}
		
		// fixme: this could be a lot?
		$log = $svn->getLog( '', $lastStoredRev + 1, $endRev );
		if( !$log ) {
			throw new MWException( "Something awry..." );
		}
		
		foreach( $log as $data ) {
			$codeRev = CodeRevision::newFromSvn( $repo, $data );
			$codeRev->save();
			// would be nice to output something but the api code is weird
			// and i don't feel like figuring it out right now :)
		}
	}
	
	public function mustBePosted() {
		// Discourage casual browsing :)
		return true;
	}
	
	public function getAllowedParams() {
		return array(
			'repo' => null,
			'rev' => null );
	}
	
	public function getParamDescription() {
		return array(
			'repo' => 'Name of repository to update',
			'rev' => 'Revision ID number to update to' );
	}
	
	public function getDescription() {
		return array(
			'Update CodeReview repository data from master revision control system.' );
	}
	
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
