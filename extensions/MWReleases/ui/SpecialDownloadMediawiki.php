<?php

class SpecialDownloadMediawiki extends SpecialPage {

	public function __construct() {
		parent::__construct( 'DownloadMediawiki' );
	}

	public function execute( $par ) {
		$this->setHeaders();
		$releases = ReleaseRepo::singleton()->getSupportedReleases();
	}
}