<?php

class ActiveTaskForcesSP extends SpecialPage {
	function __construct() {
		parent::__construct( 'ActiveTaskForces' );
	}

	function execute() {
		global $wgOut, $wgLang, $wgActiveTaskForcesPeriod;
		$this->setHeaders();
		$wgOut->addWikiMsg( 'active-task-forces-intro', 
			$wgLang->formatNum( round( $wgActiveTaskForcesPeriod / 86400, 1 ) ) );
		$qp = new ActiveTaskForcesQP;
		list( $limit, $offset ) = wfCheckLimits();
		$qp->doQuery( $offset, $limit );
	}
}

class ActiveTaskForcesQP extends QueryPage {
	function getName() {
		return 'ActiveTaskForces';
	}

	function isExpensive() {
		return true;
	}

	function isSyndicated() {
		return false;
	}

	function getSQL() {
		global $wgActiveTaskForcesPeriod;

		$dbr = wfGetDB( DB_SLAVE );
		$revisionTable = $dbr->tableName( 'revision' );
		$pageTable = $dbr->tableName( 'page' );
		$start = time() - $wgActiveTaskForcesPeriod;
		$encPeriodStart = $dbr->addQuotes( $dbr->timestamp( $start ) );

		$sql = <<<SQL
			SELECT 
				'ActiveTaskForces' AS type,
				page_namespace AS namespace,
				page_title AS title, 
				COUNT(*) AS value
			FROM $revisionTable
			JOIN $pageTable ON page_id = rev_page
			WHERE 
				page_namespace = 0 AND 
				page_title LIKE 'Task_force/%' AND
				rev_timestamp > $encPeriodStart
			GROUP BY page_namespace, page_title
SQL;
		$sql = strtr( $sql, "\r\n\t", '   ' );
		return $sql;
	}

	function formatResult( $skin, $result ) {
		global $wgContLang, $wgLang;

		$title = Title::makeTitle( $result->namespace, $result->title );
		$text = $wgContLang->convert( $title->getPrefixedText() );
		$pageLink = $skin->linkKnown( $title, $text );

		$numberLink = $skin->linkKnown(
			$title,
			wfMsgExt( 'nrevisions', array( 'parsemag', 'escape' ),
				$wgLang->formatNum( $result->value ),
			array(),
			array( 'action' => 'history' )
		) );
		return wfSpecialList( $pageLink, $numberLink );
	}
}

