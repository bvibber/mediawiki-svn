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
		$members = self::getMemberCount( $title->getPrefixedText() );
		$details = array();

		$numberLink = $skin->linkKnown(
			$title,
			wfMsgExt( 'nrevisions', array( 'parsemag', 'escape' ),
				$wgLang->formatNum( $result->edits ) ),
			array(),
			array( 'action' => 'history' )
		);
		
		$details = array( $numberLink );
		
		if ($members >= 0) {
			$details[] = wfMsgExt( 'nmembers', array( 'parsemag', 'escape' ),
					$wgLang->formatNum( $members ) );
		}
				
		$details = $wgLang->commaList( $details );
		
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
	
	static function getMemberCount( $taskForce ) {
		global $wgMemc;
		
		$key = wfMemcKey( 'taskforce-member-count', $taskForce );
		$cacheVal = $wgMemc->get( $key );
		
		if ( $cacheVal > 0 || $cacheVal === 0 ) {
			return $cacheVal;
		}
		
		$article = new Article( Title::newFromText( $taskForce ) );
		$content = $article->getContent();
		
		$count = self::parseMemberList( $content );
		
		$wgMemc->set( $key, $count, 86400 );
		
		return $count;
	}
	
	// FIXME THIS IS TOTALLY AWFUL
	static function parseMemberList( $text ) {
		$regex = "/'''Members'''.*<!--- begin --->(.*)?<!--- end --->/s";
		$matches = array();
		
		if ( !preg_match( $regex, $text, $matches ) ) {
			return -1;
		} else {
			$regex = "/^\* .*/m";
			$text = $matches[1];
			$matches = array();
			
			preg_match_all( $regex, $text, $matches );
			
			return count( $matches[0] );
		}
	}
}

