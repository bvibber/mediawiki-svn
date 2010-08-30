<?php
/**
 *
 *
 * @file
 * @ingroup API
 */
class ApiListArticleAssessment extends ApiQueryBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'aa' );
	}

	public function execute() {
		$params = $this->extractRequestParams();

		$result = $this->getResult();

		$this->addTables( array( 'article_assessment_pages', 'article_assessment_ratings' ) );

		$this->addFields( array( 'aap_page_id', 'aap_total', 'aap_count', 'aap_rating_id', 'aar_rating' ) );

		$this->addJoinConds( array(
				'article_assessment_ratings' => array( 'LEFT JOIN', array(
					'aar_id=aap_rating_id',
					) ),
				)
			);

		if ( isset( $params['pageid'] ) ) {
			$this->addWhereFld( 'aap_page_id', $params['pageid'] );
		}

		if ( $params['userrating'] ) {
			global $wgUser;

			$this->addWhereFld( 'aa_user_id', $wgUser->getId() );
			$this->addTables( 'article_assessment' );
			$this->addJoinConds( array(
				'article_assessment' => array( 'LEFT JOIN', array(
					'aa_page_id=aap_page_id',
					'aa_rating_id=aap_rating_id' ) ),
				)
			);

			$this->addFields( 'aa_rating_value' );

			if ( isset( $params['revid'] ) ){
				$this->addWhereFld( 'aa_revision', $params['revid'] );
			}
		}

		$res = $this->select( __METHOD__ );

		$ratings = array();

		foreach ( $res as $row ) {
			$pageId = $row->aap_page_id;

			if ( !isset( $ratings[$pageId] ) ) {
				$page = array(
					'pageid' => $pageId,
				);

				if ( isset( $params['revid'] ) ){
					$page['revid'] = $row->aa_revision ;
				}

				$ratings[$pageId] = $page;
			}

			 $thisRow = array(
				'ratingid' => $row->aap_rating_id,
				'ratingdesc' => $row->aar_rating,
				'total' => $row->aap_total,
				'count' => $row->aap_count,
			);

			if ( $params['userrating'] && !is_null( $row->aa_rating_value ) ) {
				$thisRow['userrating'] = $row->aa_rating_value;
			}

			$ratings[$pageId]['ratings'][] = $thisRow;
		}

		foreach ( $ratings as $rat ) {
			$result->setIndexedTagName( $rat['ratings'], 'r' );
			$result->addValue( array( 'query', $this->getModuleName() ), null, $rat );
		}

		$result->setIndexedTagName_internal( array( 'query', $this->getModuleName() ), 'aa' );
	}

	public function getAllowedParams() {
		return array(
			'pageid' => null,
			'revid' => null,
			'userrating' => false,
		);
	}

	public function getParamDescription() {
		return array(
			'pageid' => 'Page ID to get assessments for',
			'revid' => 'Specific revision to get (used in conjunction with user param, otherwise ignored)',
			'userrating' => 'Whether to get the current users ratings for the specific rev/article',
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
			'api.php?action=query&list=articleassessment',
			'api.php?action=query&list=articleassessment&aapageid=1',
			'api.php?action=query&list=articleassessment&aapageid=1&userrating',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}