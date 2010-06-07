<?php

class ActiveStrategy {
	static function getTaskForces() {
		$dbr = wfGetDB( DB_SLAVE );
		
		$res = $dbr->select( "page",
				array(
					'page_id',
					'page_namespace',
					'page_title',
					"substring_index(page_title, '/', 2) AS tf_name"
				),
				array(
					'page_namespace' => 0,
					"page_title LIKE 'Task_force/%'",
				), __METHOD__ );
		
		return $res;
	}

	static function formatResult( $skin, $taskForce, $number, $type ) {
		global $wgContLang, $wgLang, $wgActiveStrategyColors;

		$title = Title::newFromText( $taskForce );
		$text = $wgContLang->convert( $title->getPrefixedText() );
		$text = substr( $text, strpos($text, '/') + 1 );
		$pageLink = $skin->linkKnown( $title, $text );
		$colors = null;
		$color = null;
		
		if ( isset( $wgActiveStrategyColors[$type] ) ) {
			$colors = $wgActiveStrategyColors[$type];
		} else {
			$colors = $wgActiveStrategyColors['default'];
		}
		
		ksort($colors);
		
		foreach( $colors as $threshold => $curColor ) {
			if ( $number >= $threshold ) {
				$color = $curColor;
			} else {
				break;
			}
		}
		
		$style = 'padding-left: 3px; border-left: 1em solid #'.$color;
		
		$pageLink .= " <!-- $number -->";
		
		$item = Xml::tags( 'li', array( 'style' => $style ), $pageLink );
		
		return $item;
	}
	
	static function getOutput( $args ) {
		global $wgUser, $wgActiveStrategyPeriod;
		
		$html = '';
		$db = wfGetDB( DB_MASTER );
		$sk = $wgUser->getSkin();
		
		$sortField = 'members';
		
		if ( isset($args['sort']) ) {
			$sortField = $args['sort'];
		}
		
		$taskForces = self::getTaskForces();
		$categories = array();
		
		// Sorting by number of members doesn't require any 
		if ($sortField == 'members' ) {
			return self::handleSortByMembers( $taskForces );
		}
		
		foreach( $taskForces as $row ) {
			$tempTitle = Title::makeTitleSafe( NS_CATEGORY, $row->tf_name );
			$categories[] = $tempTitle->getDBkey();
		}
		
		$tables = array( 'page', 'categorylinks' );
		$fields = array( 'categorylinks.cl_to' );
		$conds = array( 'categorylinks.cl_to' => $categories );
		$options = array( 'GROUP BY' => 'categorylinks.cl_to' );
		$joinConds = array( 'categorylinks' =>
				array( 'left join', 'categorylinks.cl_from=page.page_id' ) );
		
		// Extra categories to consider
		$tables[] = 'categorylinks as tfcategory';
		$tables[] = 'categorylinks as finishedcategory';
		
		$joinConds['categorylinks as tfcategory'] =
			array( 'left join',
				array(
					'tfcategory.cl_from=page.page_id',
					'tfcategory.cl_to' => 'Task_force'
				),
			);
		$joinConds['categorylinks as finishedcategory'] = 
			array( 'left join',
				array(
					'finishedcategory.cl_from=page.page_id',
					'finishedcategory.cl_to' => 'Task_force_finished'
				),
			);
			
		$conds[] = 'tfcategory.cl_from IS NOT NULL';
		$conds[] = 'finishedcategory.cl_from IS NULL';
		
		if ( $sortField == 'edits' ) {
			$tables[] = 'revision';
			$joinConds['revision'] =
				array( 'left join', 'rev_page=page_id' );
			$fields[] = 'count(distinct rev_id) as value';
			$cutoff = $db->timestamp( time() - $wgActiveStrategyPeriod );
			$conds[] = "rev_timestamp > $cutoff";
		} elseif ( $sortField == 'ranking' ) {
			$tables[] = 'pagelinks';
			$joinConds['pagelinks'] = array( 'left join',
				array( 'pl_namespace=page_namespace', 'pl_title=page_title' ) );
			$fields[] = 'count(distinct pl_from) as value';
		}
		
		$result = $db->select( $tables, $fields, $conds,
					__METHOD__, $options, $joinConds );
		
		foreach( $result as $row ) {
			$number = $row->value;
			$taskForce = $row->cl_to;
			
			$html .= self::formatResult( $sk, $taskForce, $number, $sortField );
		}
		
		$html = Xml::tags( 'ul', null, $html );
		
		return $html;
	}
	
	static function handleSortByMembers( $taskForces ) {
		global $wgUser;
		
		$memberCount = array();
		$output = '';
		$sk = $wgUser->getSkin();
		
		foreach( $taskForces as $row ) {
			$title = Title::makeTitle( $row->page_namespace, $row->page_title );
			$memberCount[$row->tf_name] =
				self::getMemberCount( $title->getPrefixedText() );
		}
		
		asort( $memberCount );
		$memberCount = array_reverse( $memberCount );
		
		foreach( $memberCount as $name => $count ) {
			$output .= self::formatResult( $sk, $name, $count, 'members' );
		}
		
		$output = Xml::tags( 'ul', null, $output );
		
		return $output;
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

