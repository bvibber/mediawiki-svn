<?php
class ApiArticleAssessment extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'aa' );
	}

	public function execute() {
		global $wgUser;
		$params = $this->extractRequestParams();

		$userName = $wgUser->getName();

		$dbr = wfGetDB( DB_SLAVE );

		// TODO:Refactor out...?
		$res = $dbr->select(
			'article_assessment',
			array( 'aa_rating_id', 'aa_rating_value' ),
			array( 'aa_revision' => $params['revid'],
				'aa_user_text' => $userName,
				/* 'aa_page_id' => $params['pageid'],*/
				),
			__METHOD__
		);

		$res = $res->fetchRow();

		$lastRatings = array();

		foreach ( $res as $row ) {
			$lastRatings[$row->aa_rating_id] = $row->aa_rating_value;
		}

		// Do for each metric/dimension

		$pageId = $params['pageid'];
		$revisionId = $params['revid'];

		// TODO: Fold for loop into foreach above?
		global $wgArticleAssessmentRatingCount;
		for ( $i = 1; $i <= $wgArticleAssessmentRatingCount; $i++ ) {
			$lastRating = 0;
			if ( isset( $lastRatings[$i] ) ) {
				$lastRating = $lastRatings[$i];
			}

			$thisRating = 0;
			if ( isset( $params["r{i}"] ) ) {
				$thisRating = $params["r{i}"];
			}

			$this->insertOrUpdatePageRating( $pageId, $revisionId, $i, $thisRating, ( $thisRating - $lastRating ),
					( $lastRating == 0 && $thisRating != 0 )
			);

			$this->insertOrUpdateUserRatings( $pageId, $revisionId, $userName, $i, $thisRating );
		}

		// Insert (or update) a users rating for a revision

		$r = array();
		$r['result'] = 'Success';
		$this->getResult()->addValue( null, $this->getModuleName(), $r );
	}
	/*
	 *
	 *
	 * @param $pageId Integer:
	 * @param $revisionId Integer:
	 * @param $dimension Integer:
	 * @param $insert Integer: Users rating
	 * @param $updateAddition Integer: Difference between users last rating (if applicable)
	 * @param $newRating Boolean: Whether this is a new rating (for update, whether this increases the count)
	 */
	private function insertOrUpdatePageRating( $pageId, $revisionId, $rating, $insert, $updateAddition, $newRating ) {
		$dbw = wfGetDB( DB_MASTER );

		$dbw->insertOrUpdate( 'article_assessment_pages',
			array(
				'aap_page_id' => $pageId,
				'aap_revision' => $revisionId,
				'aap_total' => $insert,
				'aap_count' => 1,
				'aap_rating' => $rating,
			),
			__METHOD__,
			array(),
			array(
				'aap_total' => 'aap_total + ' . $updateAddition,
				'aap_count' => 'aap_count + ' . ( $newRating ? 1 : 0 ),
			)
		);
	}
	/*
	 * @param $pageId Integer:
	 * @param $revisionId Integer:
	 * @param $user String:
	 * @param $ratingId Integer:
	 * @param $ratingValue Integer:
	 */
	private function insertOrUpdateUserRatings( $pageId, $revisionId, $user, $ratingId, $ratingValue ) {
		$dbw = wfGetDB( DB_MASTER );

		$dbw->insertOrUpdate( 'article_assessment',
			array(
				'aa_page_id' => $pageId,
				'aa_user_text' => $user,
				'aa_revision' => $revisionId,
				'aa_timestamp' => wfTimestampNow(),
				'aa_rating_id' => $ratingId,
				'aa_rating_value' => $ratingValue,
			),
			__METHOD__,
			array(),
			array(
				'aa_timestamp' => wfTimestampNow(),
				'aa_rating_id' => $ratingId,
				'aa_rating_value' => $ratingValue,
			)
		);

	}

	public function getAllowedParams() {
		global $wgArticleAssessmentRatingCount;
		$ret = array(
			'pageid' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'revid' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			)
		);

		for ( $i = 1; $i <= $wgArticleAssessmentRatingCount; $i++ ) {
			$ret['r{$i}'] = array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_MIN => 0,
				ApiBase::PARAM_MAX => 5,
			);
		}
		return $ret;
	}

	public function getParamDescription() {
		global $wgArticleAssessmentRatingCount;
		$ret = array(
			'pageid' => '',
			'revid' => ''
		);
		for ( $i = 1; $i <= $wgArticleAssessmentRatingCount; $i++ ) {
	        $ret['r{$i}'] = 'Rating {$i}';
		}
		return $ret;
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