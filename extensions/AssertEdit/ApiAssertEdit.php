<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	// Eclipse helper - will be ignored in production
	require_once( 'ApiBase.php' );
}

class ApiAssertEdit extends ApiBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		$this->dieUsage( '', 'assertedit' );
	}

	public function getDescription() {
		return 'Allows bots to make assertions. Can only be used during of editing';
	}

	public function isReadMode() {
		return false;
	}

	public function shouldCheckMaxlag() {
		return false;
	}

	public function getParamDescription() {
		return array(
			'user' => 'Verify that bot is logged in, to prevent anonymous edits.',
			'bot' => 'Verify that bot is logged in and has a bot flag.',
			'true' => 'Always true; nassert=true will fail if the extension is installed.',
			'false' => 'Always false; assert=false will fail if the extension is installed.',
			'exists' => 'Verify that page exists. Could be useful from other extensions, i.e. adding nassert=exists to the inputbox extension.',
			'test' => 'Verify that this wiki allows random testing. Defaults to false, but can be overridden in LocalSettings.php.'
		);
	}

	public function getPossibleErrors() {
		return array();
	}

	public function getAllowedParams() {
		return array(
			'user' => null,
			'bot' => null,
			'true' => null,
			'false' => null,
			'exists' => null,
			'test' => null
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
