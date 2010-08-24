<?php
/**
 * Extend the API for ArticleAssessment
 *
 * @file
 * @ingroup API
 */
class ApiListArticleAssessment extends ApiQueryBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'aa' );
	}

	/**
	 * runs when the API is called with "articleasessment"
	 * @see includes/api/ApiBase#execute()
	 */
	public function execute() {
		$params = $this->extractRequestParams();
		
		$result = $this->getResult();
		
		$this->addTables( 'article_assessment_pages' );
		
		$this->addFields( array( 'aa_page_id', 'aa_revision', 'aa_total', 'aa_count', 'aa_dimension' ) );
		
		if ( isset( $params['pageid'] ) ) {
			$this->addWhereFld( 'aa_page_id', $params['pageid'] );
		}
		
		if ( isset( $params['revid'] ) ) {
			$this->addWhereFld( 'aa_revision', $params['revid'] );
		}
		
		$res = $this->select( __METHOD__ );

		$assessments = array();
		
		foreach( $res as $row ) {
			if ( !isset( $assessments[$row->aa_revision] ) ) {
				$assessments[$row->aa_revision] = array( 
					'pageid' => $row->aa_page_id,
					'revisionid' => $row->aa_revision,
				);
			}
			
			$assessments[$row->aa_revision]['dimensions']['d' . $row->aa_dimension] = array( 'dimension' => $row->aa_dimension, 'total' => $row->aa_total, 'count' => $row->aa_count );
		}

		foreach( $assessments as $ass ) {
			$result->addValue( array( 'query', $this->getModuleName() ), null, $ass );
		}
		
		$result->setIndexedTagName_internal( array( 'query', $this->getModuleName() ), 'aa' );
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
	
	protected function getExamples() {
		return array(
			'api.php?action=query&list=articleassessment'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}