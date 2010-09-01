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
			$this->addWhereFld( 'aa_page_id', $params['pageid'] );
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

			$this->addFields( array( 'aa_rating_value', 'aa_revision' ) );

			if ( isset( $params['revid'] ) ){
				$this->addWhereFld( 'aa_revision', $params['revid'] );
			}
		}

		$this->addOption( 'ORDER BY', 'aa_revision DESC' );

		$limit = $params['limit'];
		$this->addOption( 'LIMIT', $limit * 4 ); //4 "Ratings"

		$res = $this->select( __METHOD__ );

		$ratings = array();

		$userRatedArticle = false;

		foreach ( $res as $row ) {
			$pageId = $row->aap_page_id;

			if ( !isset( $ratings[$pageId] ) ) {
				$page = array(
					'pageid' => $pageId,
				);

				if ( isset( $params['revid'] ) || $params['userrating'] ){
					$page['revid'] = $row->aa_revision;
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

				$userRatedArticle = true;
			}

			$ratings[$pageId]['ratings'][] = $thisRow;
		}

		//Only can actually be "stale" if the user has rated the article before
		if ( $params['userrating'] && $userRatedArticle ) {
			$revid = isset( $params['revid'] ) ? $params['revid'] : $ratings[$pageId]['revid'];

			$this->resetQueryParams();

			$this->addTables( 'revision' );
			$this->addFields( array( 'COUNT(rev_id) AS norevs', 'rev_page' ) );

			$this->addWhereFld( 'rev_page', $params['pageid'] );
			$this->addWhere( 'rev_id > ' . $revid );

			$res = $this->select( __METHOD__ );

			global $wgArticleAssessmentStaleCount;

			if ( $res && $res->fetchRow()->norevs > $wgArticleAssessmentStaleCount ){
				//it's stale!
				$ratings[$params['pageid']]['stale'] = '';
			}
		}

		$count = 0;
		foreach ( $ratings as $rat ) {
			if ( ++ $count > $limit ) {
				//$this->setContinueEnumParameter( 'from', $this->keyToTitle( $row->page_title ) );
				break;
			}

			$result->setIndexedTagName( $rat['ratings'], 'r' );
			$result->addValue( array( 'query', $this->getModuleName() ), null, $rat );
		}

		$result->setIndexedTagName_internal( array( 'query', $this->getModuleName() ), 'aa' );
	}

	public function getAllowedParams() {
		return array(
			'pageid' => array(
				ApiBase::PARAM_ISMULTI => false,
				ApiBase::PARAM_TYPE => 'integer',
			),
			'revid' => null,
			'userrating' => false,
			'limit' => array(
				ApiBase::PARAM_DFLT => 1,
				ApiBase::PARAM_TYPE => 'limit',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => ApiBase::LIMIT_BIG1,
				ApiBase::PARAM_MAX2 => ApiBase::LIMIT_BIG1,
			),
		);
	}

	public function getParamDescription() {
		return array(
			'pageid' => 'Page ID to get assessments for',
			'revid' => 'Specific revision to get (used in conjunction with userrating param, otherwise ignored. Needed for stale calculation)',
			'userrating' => 'Whether to get the current users ratings for the specific rev/article',
			'limit' => 'Amount of pages to get the ratings for',
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