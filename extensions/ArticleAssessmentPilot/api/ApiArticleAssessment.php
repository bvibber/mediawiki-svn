<?php
class ApiArticleAssessment extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, '' );
	}

	public function execute() {
		global $wgUser, $wgArticleAssessmentRatings;
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

		// Query the latest ratings by this user for this page,
		// possibly for an older revision
		// TODO: Make this query saner once $wgArticleAssessmentRatingCount has been redone
		// TODO:Refactor out...?
		$res = $dbr->select(
			'article_assessment',
			array( 'aa_rating_id', 'aa_rating_value', 'aa_revision' ),
			array_merge(
				array(
					'aa_user_id' => $wgUser->getId(),
					'aa_user_text' => $wgUser->getName(),
					'aa_page_id' => $params['pageid'],
					'aa_rating_id' => $wgArticleAssessmentRatings,
				),
				$token
			),
			__METHOD__,
			array(
				'ORDER BY' => 'aa_revision DESC',
				'LIMIT' => count( $wgArticleAssessmentRatings ),
			)
		);

		$lastRatings = array();

		foreach ( $res as $row ) {
			$lastRatings[$row->aa_rating_id] = $row->aa_rating_value;
		}

		$pageId = $params['pageid'];
		$revisionId = $params['revid'];

		foreach( $wgArticleAssessmentRatings as $rating ) {
			$lastRating = false;
			if ( isset( $lastRatings[$rating] ) ) {
				$lastRating = $lastRatings[$rating];
			}

			$thisRating = false;
			if ( isset( $params["r{$rating}"] ) ) {
				$thisRating = $params["r{$rating}"];
			}

			$this->insertPageRating( $pageId, $i, ( $thisRating - $lastRating ),
					( $lastRating === false && $thisRating !== false )
			);

			$this->insertUserRatings( $pageId, $revisionId, $wgUser, $token, $rating, $thisRating );
		}

		$r = array( 'result' => 'Success' );
		$this->getResult()->addValue( null, $this->getModuleName(), $r );
	}

	/**
	 * Inserts (or Updates, where appropriate) the aggregate page rating
	 * 
	 * @param $pageId Integer: Page Id
	 * @param $ratingId Integer: Rating Id
	 * @param $updateAddition Integer: Difference between user's last rating (if applicable)
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

	/**
	 * Inserts (or Updates, where appropriate) the users ratings for a specific revision
	 *
	 * @param $pageId Integer: Page Id
	 * @param $revisionId Integer: Revision Id
	 * @param $user User: Current User object
	 * @param $token Array: Token if necessary
	 * @param $ratingId Integer: Rating Id
	 * @param $ratingValue Integer: Value of the Rating
	 */
	private function insertUserRatings( $pageId, $revisionId, $user, $token, $ratingId, $ratingValue ) {
		$dbw = wfGetDB( DB_MASTER );
		
		$timestamp = $dbw->timestamp();

		$dbw->insert(
			'article_assessment',
			array_merge(
				array(
					'aa_page_id' => $pageId,
					'aa_user_id' => $user->getId(),
					'aa_user_text' => $user->getName(),
					'aa_revision' => $revisionId,
					'aa_timestamp' => $timestamp,
					'aa_rating_id' => $ratingId,
					'aa_rating_value' => $ratingValue,
				),
				$token
			),
			__METHOD__,
			 array( 'IGNORE' )
		);

		if ( !$dbw->affectedRows() ) {
			$dbw->update(
				'article_assessment',
				array(
					'aa_timestamp' => $timestamp,
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
	}

	public function getAllowedParams() {
		global $wgArticleAssessmentRatings;
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
		
		foreach( $wgArticleAssessmentRatings as $rating ) {
			$ret["r{$rating}"] = array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_MIN => 0,
				ApiBase::PARAM_MAX => 5,
			);
		}
		return $ret;
	}

	public function getParamDescription() {
		global $wgArticleAssessmentRatings;
		$ret = array(
			'pageid' => 'Page ID to submit assessment for',
			'revid' => 'Revision ID to submit assessment for',
			'anontoken' => 'Token for anonymous users',
		);
		foreach( $wgArticleAssessmentRatings as $rating ) {
		        $ret["r{$rating}"] = "Rating {$rating}";
		}
		return $ret;
	}

	public function getDescription() {
		return array(
			'Submit article assessments'
		);
	}
	
	public function mustBePosted() {
		return true;
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