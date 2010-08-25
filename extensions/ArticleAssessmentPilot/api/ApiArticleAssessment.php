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

		//TODO:Refactor out...?
		$res = $dbr->select(
			'article_assessment',
			array( 'aa_r1', 'aa_r2', 'aa_r3', 'aa_r4' ),
			array( 'aa_revision' => $params['revid'],
				'aa_user_text' => $userName,
				/* 'aa_page_id' => $params['pageid'],*/
				),
			__METHOD__
		);

		$res = $res->fetchRow();

		$userHasRated = false;
		if ( $res ) {
			$lastM1 = $res->aa_r1;
			$lastM2 = $res->aa_r2;
			$lastM3 = $res->aa_r3;
			$lastM4 = $res->aa_r4;
			$userHasRated = true;
		} else {
			$lastM1 = 0;
			$lastM2 = 0;
			$lastM3 = 0;
			$lastM4 = 0;
		}

		$r1 = $params['r1'];
		$r2 = $params['r2'];
		$r3 = $params['r3'];
		$r4 = $params['r4'];

		//Do for each metric/dimension

		$pageId = $params['pageid'];
		$revisionId = $params['revid'];

		$this->insertOrUpdatePages( $pageId, $revisionId, $userName, 1, $r1, ( $r1 - $lastM1 ), $userHasRated );
		$this->insertOrUpdatePages( $pageId, $revisionId, $userName, 2, $r1, ( $r2 - $lastM2 ), $userHasRated );
		$this->insertOrUpdatePages( $pageId, $revisionId, $userName, 3, $r1, ( $r3 - $lastM3 ), $userHasRated );
		$this->insertOrUpdatePages( $pageId, $revisionId, $userName, 4, $r1, ( $r4 - $lastM4 ), $userHasRated );

		//Insert (or update) a users rating for a revision 
		$dbw = wfGetDB( DB_MASTER );

		$dbw->insertOrUpdate( 'article_assessment',
			array(
				'aa_page_id' => $pageId,
				'aa_user_text' => $userName,
				'aa_revision' => $revisionId,
				'aa_user_text' => $userName,
				'aa_timestamp' => wfTimestampNow(),
				'aa_r1' => $r1,
				'aa_r2' => $r2,
				'aa_r3' => $r3,
				'aa_r4' => $r4,
			),
			__METHOD__,
			array(),
			array(
				'aa_timestamp' => wfTimestampNow(),
				'aa_r1' => $r1,
				'aa_r2' => $r2,
				'aa_r3' => $r3,
				'aa_r4' => $r4,
			)
		);

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
	private function insertOrUpdatePages( $pageId, $revisionId, $rating, $insert, $updateAddition, $newRating ) {
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

	public function getAllowedParams() {
		return array(
			'pageid' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'revid' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'r1' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_MIN => 0,
				ApiBase::PARAM_MAX => 5,
			),
			'r2' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_MIN => 0,
				ApiBase::PARAM_MAX => 5,
			),
			'r3' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_MIN => 0,
				ApiBase::PARAM_MAX => 5,
			),
			'r4' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_MIN => 0,
				ApiBase::PARAM_MAX => 5,
			),
		);
	}

	public function getParamDescription() {
		return array(
			'pageid' => '',
			'revid' => '',
			'r1' => 'Rating 1',
			'r2' => 'Rating 2',
			'r3' => 'Rating 3',
			'r4' => 'Rating 4',
		);
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