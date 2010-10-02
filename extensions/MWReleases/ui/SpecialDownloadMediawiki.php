<?php

class SpecialDownloadMediaWiki extends SpecialPage {

	public function __construct() {
		parent::__construct( 'DownloadMediaWiki' );
	}

	public function execute( $par ) {
		$this->setHeaders();
		$releases = ReleaseRepo::singleton()->getSupportedReleases();
	}
}