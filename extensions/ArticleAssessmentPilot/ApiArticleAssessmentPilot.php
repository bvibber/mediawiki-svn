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
		
		if( isset( $params['getCumulativeResults'] )  ){
			//get cumulative results
			
			//query to add 'n' reviews together
			
		}
		else if (  isset( $params['setUserVals'] ) ){
			//set user values
			
			//validate authid
			
			//insert
		}
		
	}

	/**
	 * Required parameter check
	 * @param $params params extracted from the POST
	 */
 	protected function validateParams( $params ) {
		
 		if( isset( $params['getCumulativeResults'] )  ){
 			//get cumulative results	
			$required = array('pageId', 'revId');
 			foreach ( $required as $arg ) {
				if ( !isset( $params[$arg] ) ) {
					$this->dieUsageMsg( array( 'missingparam', $arg ) );
				}
			}
 		}
 		else if (  isset( $params['setUserVals'] ) ){
 			//set user values
 			$required = array('userId', 'authId', 'pageId', 'revId', 'review');
 			foreach ( $required as $arg ) {
				if ( !isset( $params[$arg] ) ) {
					$this->dieUsageMsg( array( 'missingparam', $arg ) );
				}
			}
 		}
 		else{
 			//FIXME: better usage message
 			$this->dieUsageMsg( array('missingparam', "mode")  );
 		}
	}

	/*
	public function getParamDescription() {
		return array(
			'getResults' => 'set if you want to get results',
		);
	}*/

	public function getDescription() {
		return array(
			'get and set article assessment data'
		);
	}
	

	public function getVersion() {
		return __CLASS__ . ':0';
	}

}