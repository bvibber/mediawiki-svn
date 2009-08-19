<?php

/**
 * Class definition for MWReleases API Module
 */

class ApiMWReleases extends ApiBase {
	public function __construct($main, $action) {
		parent :: __construct($main, $action);
	}

	public function execute() {
		$results = array();
		$releases = explode( "\n", wfMsgForContent( 'mwreleases-list' ) );
		foreach( $releases as $release ) {
			list( $status, $version ) = explode( ':', $release );
			$r = array( 'version' => $version );
			if( $status == 'current' )
				$r['current'] = '';
			$results[] = $r;
		}
		$this->getResult()->setIndexedTagName($results, 'release');
		$this->getResult()->addValue(null, $this->getModuleName(), $results);
	}

	public function getDescription() {
		return array (
			'Get the list of current Mediawiki releases'
		);
	}

	protected function getExamples() {
		return array(
			'api.php?action=mwreleases'
		);
	}
	public function getVersion() {
		return __CLASS__ . ': ' . MWRELEASES_VERSION;
	}
}