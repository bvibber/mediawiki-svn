<?php

class ActiveStrategy {
	static function getSQL() {
		global $wgActiveStrategyPeriod;

		$dbr = wfGetDB( DB_SLAVE );
		$revisionTable = $dbr->tableName( 'revision' );
		$pageTable = $dbr->tableName( 'page' );
		$start = time() - $wgActiveStrategyPeriod;
		$encPeriodStart = $dbr->addQuotes( $dbr->timestamp( $start ) );

		$sql = <<<SQL
			SELECT 
				page_namespace AS namespace,
				substring_index(page_title, '/', 2) AS title,
				COUNT(*) AS edits
			FROM $revisionTable
			JOIN $pageTable ON page_id = rev_page
			WHERE 
				page_namespace = 0 AND 
				page_title LIKE 'Task_force/%' AND
				rev_timestamp > $encPeriodStart
			GROUP BY page_namespace, title
SQL;
		$sql = strtr( $sql, "\r\n\t", '   ' );
		return $sql;
	}

	static function formatResult( $skin, $result ) {
		global $wgContLang, $wgLang;

		$title = Title::makeTitle( $result->namespace, $result->title );
		$text = $wgContLang->convert( $title->getPrefixedText() );
		$pageLink = $skin->linkKnown( $title, $text );
		$members = 0;
		$details = '';

		$numberLink = $skin->linkKnown(
			$title,
			wfMsgExt( 'nrevisions', array( 'parsemag', 'escape' ),
				$wgLang->formatNum( $result->edits ) ),
			array(),
			array( 'action' => 'history' )
		);
		
		$members = wfMsgExt( 'nmembers', array( 'parsemag', 'escape' ),
				$wgLang->formatNum( $members ) );
				
		$details = $wgLang->commaList( array( $numberLink, $members ) );
		
		return wfSpecialList( $pageLink, $details );
	}
	
	static function getOutput() {
		global $wgUser;
		
		$html = '';
		$sql = self::getSQL();
		$db = wfGetDB( DB_MASTER );
		$sk = $wgUser->getSkin();
		
		$result = $db->query( $sql, __METHOD__ );
		
		foreach( $result as $row ) {
			$html .= Xml::tags( 'li', null, self::formatResult( $sk, $row ) );
		}
		
		$html = Xml::tags( 'ul', null, $html );
		
		return $html;
	}
}

