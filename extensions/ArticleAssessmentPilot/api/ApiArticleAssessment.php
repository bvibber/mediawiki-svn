<?php
/**
 *
 *
 * @file
 * @ingroup API
 */
class ApiArticleAssessment extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'aa' );
	}

	public function execute() {
		global $wgUser;
		$params = $this->extractRequestParams();

		$userName = $wgUser->getName();

		//TODO:Refactor out...?

		$this->addTables( 'article_assessment' );

		$this->addFields( array( 'aa_m1', 'aa_m2', 'aa_m3', 'aa_m3' ) );

		//$this->addWhereFld( 'aa_page_id', $params['pageid'] );

		$this->addWhereFld( 'aa_revision', $params['revid'] );
		$this->addWhereFld( 'aa_user_text', $userName );

		$res = $this->select( __METHOD__ );

		if ( $res ) {
			$lastM1 = $res[0]->aa_m1;
			$lastM2 = $res[0]->aa_m2;
			$lastM3 = $res[0]->aa_m3;
			$lastM4 = $res[0]->aa_m4;
		} else {
			$lastM1 = 0;
			$lastM2 = 0;
			$lastM3 = 0;
			$lastM4 = 0;
		}

		$m1 = isset( $params['1'] ) ? $params['1'] : 0;
		$m2 = isset( $params['2'] ) ? $params['2'] : 0;
		$m3 = isset( $params['3'] ) ? $params['3'] : 0;
		$m4 = isset( $params['4'] ) ? $params['4'] : 0;

		//Do for each metric/dimension

		$pageId = $params['pageid'];
		$revisionId = $params['revisionid'];

		$this->insertOrUpdatePages( $pageId, $revisionId, $userName, 1, $m1, ( $m1 - $lastM1 ) );
		$this->insertOrUpdatePages( $pageId, $revisionId, $userName, 2, $m1, ( $m2 - $lastM2 ) );
		$this->insertOrUpdatePages( $pageId, $revisionId, $userName, 3, $m1, ( $m3 - $lastM3 ) );
		$this->insertOrUpdatePages( $pageId, $revisionId, $userName, 4, $m1, ( $m4 - $lastM4 ) );

		//Insert (or update) a users rating for a revision 
		$dbw = wfGetDB( DB_MASTER );

		$dbw->insertOrUpdate( 'article_assessment',
			array(
				'aa_page_id' => $pageId,
				'aa_user_text' => $userName,
				'aa_revision' => $revisionId,
				'aa_user' => $userName,
				'aa_timestamp' => wfTimestampNow(),
				'aa_m1' => $m1,
				'aa_m2' => $m2,
				'aa_m3' => $m3,
				'aa_m4' => $m4,
			),
			__METHOD__,
			array(),
			array(
				'aa_timestamp' => wfTimestampNow(),
				'aa_m1' => $m1,
				'aa_m2' => $m2,
				'aa_m3' => $m3,
				'aa_m4' => $m4,
			)
		);

		$r = array();
		$r['result'] = 'Success';
		$this->getResult()->addValue( null, $this->getModuleName(), $r );
	}

	private function insertOrUpdatePages( $pageId, $revisionId, $dimension, $insert, $updateAddition ) {
		$dbw = wfGetDB( DB_MASTER );

		$dbw->insertOrUpdate( 'article_assessment_pages',
			array(
				'aap_page_id' => $pageId,
				'aap_revision' => $revisionId,
				'aap_total' => $insert,
				'aap_count' => 'aap_count + 1',
				'aap_dimension' => $dimension,
			),
			__METHOD__,
			array(),
			array(
				'aap_total' => 'aap_total + ' . $updateAddition,
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
			'1' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_MIN => 0,
				ApiBase::PARAM_MAX => 5,
			),
			'2' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_MIN => 0,
				ApiBase::PARAM_MAX => 5,
			),
			'3' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_MIN => 0,
				ApiBase::PARAM_MAX => 5,
			),
			'4' => array(
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
			'1' => 'Metric 1',
			'2' => 'Metric 2',
			'3' => 'Metric 3',
			'4' => 'Metric 4',
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