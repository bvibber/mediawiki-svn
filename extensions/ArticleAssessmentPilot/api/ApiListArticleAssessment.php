<?php
/**
 * Extend the API for ArticleAssessment
 *
 * @file
 * @ingroup API
 */
class ApiListArticleAssessment extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'aa' );
	}

	/**
	 * runs when the API is called with "articleasessment"
	 * @see includes/api/ApiBase#execute()
	 */
	public function execute() {
		$params = $this->extractRequestParams();

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
			'List all article assessments'
		);
	}
	
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
		) );
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}