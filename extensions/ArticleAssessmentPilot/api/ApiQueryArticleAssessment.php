<?php
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
				)
			),
		) );

		$this->addWhereFld( 'aap_page_id', $params['pageid'] );

		if ( $params['userrating'] ) {
			global $wgUser;

			if ( $wgUser->isAnon() ) {
				if ( !isset( $params['anontoken'] ) ) {
					$this->dieUsageMsg( array( 'missingparam', 'anontoken' ) );
				} elseif ( strlen( $params['anontoken'] ) != 32 ) {
					$this->dieUsage( 'The anontoken is not 32 characters', 'invalidtoken' );
				}

				$this->addWhereFld( 'aa_user_anon_token', $params['anontoken'] );
			}

			$this->addWhereFld( 'aa_user_id', $wgUser->getId() );
			$this->addTables( 'article_assessment' );
			$this->addJoinConds( array(
				'article_assessment' => array( 'LEFT JOIN', array(
					'aa_page_id=aap_page_id',
					'aa_rating_id=aap_rating_id' ) ),
				)
			);

			$this->addFields( array( 'aa_rating_value', 'aa_revision' ) );

			if ( isset( $params['revid'] ) ) {
				$this->addWhereFld( 'aa_revision', $params['revid'] );
			}

			$this->addOption( 'ORDER BY', 'aa_revision DESC' );
		}
		
		global $wgArticleAssessmentRatingCount;

		$this->addOption( 'LIMIT', $wgArticleAssessmentRatingCount );

		$res = $this->select( __METHOD__ );

		$ratings = array();

		$userRatedArticle = false;

		foreach ( $res as $row ) {
			$pageId = $row->aap_page_id;

			if ( !isset( $ratings[$pageId] ) ) {
				$page = array(
					'pageid' => $pageId,
				);

				if ( $params['userrating'] ) {
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
			$dbr = wfGetDb( DB_SLAVE );
			
			global $wgArticleAssessmentStaleCount;

			$res = $dbr->selectField(
				'revision',
				'COUNT(*) AS norevs',
				array(
					'rev_page' => $params['pageid'],
					'rev_id > ' . $ratings[$pageId]['revid']
				),
				__METHOD__,
				array ( 'LIMIT', $wgArticleAssessmentStaleCount + 1 )
			);

			if ( $res && (int)$res > $wgArticleAssessmentStaleCount ) {
				//it's stale!
				$ratings[$params['pageid']]['stale'] = intval( $res );
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
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => false,
				ApiBase::PARAM_TYPE => 'integer',
			),
			'revid' =>array(
				ApiBase::PARAM_ISMULTI => false,
				ApiBase::PARAM_TYPE => 'integer',
			),
			'userrating' => false,
			'anontoken' => null,
		);
	}

	public function getParamDescription() {
		return array(
			'pageid' => 'Page ID to get assessments for',
			'revid' => 'Specific revision to get (used in conjunction with userrating param, otherwise ignored)',
			'userrating' => "Whether to get the current user's ratings for the specific rev/article",
			'anontoken' => 'Token for anonymous users',
		);
	}

	public function getDescription() {
		return array(
			'List all article assessments'
		);
	}

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
				array( 'missingparam', 'anontoken' ),
				array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			)
		);
	}

	protected function getExamples() {
		return array(
			'api.php?action=query&list=articleassessment',
			'api.php?action=query&list=articleassessment&aapageid=1',
			'api.php?action=query&list=articleassessment&aapageid=1&aauserrating',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}