<?php
/**
 * Extend the API for ArticleAssessment
 *
 * @file
 * @ingroup API
 */

class ApiArticleAssessment extends ApiBase {

	/**
	 * runs when the API is called with "articleasessment"
	 * @see includes/api/ApiBase#execute()
	 */
	public function execute() {
		$params = $this->extractRequestParams();
		$this->validateParams( $params );
		
		if(isset($params['getResults'])){
		
		}
	}

	/**
	 * Required parameter check
	 * @param $params params extracted from the POST
	 */
 	protected function validateParams( $params ) {
		$required = array( );
		foreach ( $required as $arg ) {
			if ( !isset( $params[$arg] ) ) {
				$this->dieUsageMsg( array( 'missingparam', $arg ) );
			}
		}
	}

	public function getParamDescription() {
		return array(
			'getResults' => 'set if you want to get results',
		);
	}

	public function getDescription() {
		return array(
			'get and set article assessment data'
		);
	}
	
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'mode' ),
		) );
	}

	public function getAllowedParams() {
		return array(
			'getResults' => null,
		);
	}

	public function getVersion() {
		return __CLASS__ . ':0';
	}

}