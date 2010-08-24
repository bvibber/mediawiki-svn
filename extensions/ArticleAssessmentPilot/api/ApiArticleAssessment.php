<?php
/**
 *
 *
 * @file
 * @ingroup API
 */
class ApiArticleAssessment extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'aa' );
	}

	public function execute() {
	}

	public function getAllowedParams() {
		return array(
			'pageid' => null,
			'revid' => null,
		);
	}

	public function getParamDescription() {
		return array(
			'pageid' => '',
			'revid' => '',
		);
	}

	public function getDescription() {
		return array(
			'Submit article assessments'
		);
	}
	
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
		) );
	}
	
	protected function getExamples() {
		return array(
			'api.php?action=articleassessment'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}