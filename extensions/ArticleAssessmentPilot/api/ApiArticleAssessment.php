<?php
class ApiArticleAssessment extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'aa' );
	}

	public function execute() {
		global $wgUser, $wgArticleAssessmentRatingCount;
		$params = $this->extractRequestParams();

		$token = array();
		if ( $wgUser->isAnon() ) {
			if ( !isset( $params['anontoken'] ) ) {
				$this->dieUsageMsg( array( 'missingparam', 'anontoken' ) );
			} elseif ( strlen( $params['anontoken'] ) != 32 ) {
				$this->dieUsage( 'The anontoken is not 32 characters', 'invalidtoken' );
			}

			$token['aa_user_anon_token'] = $params['anontoken'];
		}

		$dbr = wfGetDB( DB_SLAVE );

		// TODO:Refactor out...?
		$res = $dbr->select(
			'article_assessment',
			array( 'aa_rating_id', 'aa_rating_value', 'aa_revision' ),
			array_merge(
				array(
					'aa_user_id' => $wgUser->getId(),
					'aa_user_text' => $wgUser->getName(),
					'aa_page_id' => $params['pageid'],
				),
				$token
			),
			__METHOD__,
			array(
				'ORDER BY' => 'aa_revision DESC',
				'LIMIT' => 4,
			)
		);

		$lastRatings = array();

		foreach ( $res as $row ) {
			$lastRatings[$row->aa_rating_id] = $row->aa_rating_value;
		}

		$pageId = $params['pageid'];
		$revisionId = $params['revid'];

		// TODO: Fold for loop into foreach above?
		for ( $i = 1; $i <= $wgArticleAssessmentRatingCount; $i++ ) {
			$lastRating = 0;
			if ( isset( $lastRatings[$i] ) ) {
				$lastRating = $lastRatings[$i];
			}

			$thisRating = 0;
			if ( isset( $params["r{$i}"] ) ) {
				$thisRating = $params["r{$i}"];
			}

			$this->insertPageRating( $pageId, $i, ( $thisRating - $lastRating ),
					( $lastRating == 0 && $thisRating != 0 )
			);

			$this->insertUserRatings( $pageId, $revisionId, $wgUser, $token, $i, $thisRating );
		}

		$r = array();
		$r['result'] = 'Success';
		$this->getResult()->addValue( null, $this->getModuleName(), $r );
	}

	/*
	 *
	 * @param $pageId Integer:
	 * @param $ratingId Integer:
	 * @param $updateAddition Integer: Difference between users last rating (if applicable)
	 * @param $newRating Boolean: Whether this is a new rating (for update, whether this increases the count)
	 */
	private function insertPageRating( $pageId, $ratingId, $updateAddition, $newRating ) {
		$dbw = wfGetDB( DB_MASTER );

		$dbw->insert(
			'article_assessment_pages',
			 array(
				'aap_page_id' => $pageId,
				'aap_total' => 0,
				'aap_count' => 0,
				'aap_rating_id' => $ratingId,
			),
			__METHOD__,
			 array( 'IGNORE' )
		);

		$dbw->update(
			'article_assessment_pages',
			array(
				'aap_total = aap_total + ' . $updateAddition,
				'aap_count = aap_count + ' . ( $newRating ? 1 : 0 ),
			),
			array(
				'aap_page_id' => $pageId,
				'aap_rating_id' => $ratingId,
			),
			__METHOD__
		);
	}

	/*
	 * @param $pageId Integer:
	 * @param $revisionId Integer:
	 * @param $user User:
	 * @param $token Array:
	 * @param $ratingId Integer:
	 * @param $ratingValue Integer:
	 */
	private function insertUserRatings( $pageId, $revisionId, $user, $token, $ratingId, $ratingValue ) {
		$dbw = wfGetDB( DB_MASTER );

		$res = $dbw->insert(
			'article_assessment',
			array_merge(
				array(
					'aa_page_id' => $pageId,
					'aa_user_id' => $user->getId(),
					'aa_user_text' => $user->getName(),
					'aa_revision' => $revisionId,
					'aa_timestamp' => wfTimestampNow(),
					'aa_rating_id' => $ratingId,
					'aa_rating_value' => $ratingValue,
				),
				$token
			),
			__METHOD__,
			 array( 'IGNORE' )
		);

		//TODO: Don't do this if the insert was successful
		$dbw->update(
			'article_assessment',
			array(
				'aa_timestamp' => wfTimestampNow(),
				'aa_rating_value' => $ratingValue,
			),
			array_merge(
				array(
					'aa_page_id' => $pageId,
					'aa_user_text' => $user->getName(),
					'aa_revision' => $revisionId,
					'aa_rating_id' => $ratingId,
				),
				$token
			),
			__METHOD__
		);
	}

	public function getAllowedParams() {
		global $wgArticleAssessmentRatingCount;
		$ret = array(
			'pageid' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => false,
			),
			'revid' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => false,
			),
			'anontoken' => null,
		);

		for ( $i = 1; $i <= $wgArticleAssessmentRatingCount; $i++ ) {
			$ret["r{$i}"] = array(
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
			'pageid' => 'Page ID to submit assessment for',
			'revid' => 'Revision ID to submit assessment for',
			'anontoken' => 'Token for anonymous users',
		);
		for ( $i = 1; $i <= $wgArticleAssessmentRatingCount; $i++ ) {
	        $ret["r{$i}"] = "Rating {$i}";
		}
		return $ret;
	}

	public function getDescription() {
		return array(
			'Submit article assessments'
		);
	}

	public function isWriteMode() {
		return true;
	}

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'anontoken' ),
			array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
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