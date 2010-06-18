<?php

class ActiveStrategy {
	static function getTaskForces() {
		$dbr = wfGetDB( DB_SLAVE );
		
		$res = $dbr->select(
			array( "page", 'categorylinks',
				'categorylinks as finishedcategory' ),
			array(
				'page_id',
				'page_namespace',
				'page_title',
				"substring_index(page_title, '/', 2) AS tf_name"
			),
			array(
				'page_namespace' => 0,
				"page_title LIKE 'Task_force/%'",
				"page_title NOT LIKE 'Task_force/%/%'",
				'finishedcategory.cl_from IS NULL',
			),
			__METHOD__,
			array(),
			array(
				'categorylinks' => array( 'RIGHT JOIN',
					array(
						'categorylinks.cl_from=page_id',
						'categorylinks.cl_to' => 'Task_force',
					),
				),
				'categorylinks as finishedcategory' =>
					array( 'left join',
						array(
							'finishedcategory.cl_from=page.page_id',
							'finishedcategory.cl_to' => 'Task_force_finished'
						),
					),
			) );
		
		return $res;
	}

	static function formatResult( $skin, $taskForce, $number, $type ) {
		global $wgContLang, $wgLang, $wgActiveStrategyColors;

		if ( ! $taskForce ) {
			// Fail.
			return;
		}

		$title = Title::newFromText( $taskForce );
		$text = $wgContLang->convert( $title->getPrefixedText() );
		$text = self::getTaskForceName( $text );
		$pageLink = $skin->linkKnown( $title, $text );
		$colors = null;
		$color = null;
		$style = '';
		
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
		
		if ($color) {
			$style = 'padding-left: 3px; border-left: 1em solid #'.$color;
		}
		
		if ( $type == 'members' ) {
			$pageLink .= ' ('.wfMsg( 'nmembers', $number ).')';
		}
		
		$pageLink .= " <!-- $number -->";
		
		if ($style) {
			$item = Xml::tags( 'span', array( 'style' => $style ), $pageLink );
		} else {
			$item = $pageLink;
		}
		
		$item .= "<br/>";
		
		return $item;
	}
	
	static function getTaskForceName( $text ) {
		$text = substr( $text, strpos($text, '/') + 1 );
		
		if ( strpos( $text, '/' ) ) {
			$text = substr( $text, 0, strpos( $text, '/' ) );
		}
		
		return $text;
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
			$text = self::getTaskForceName( $row->tf_name );
			$tempTitle = Title::makeTitleSafe( NS_CATEGORY, $text );
			$categories[$tempTitle->getDBkey()] = $row->tf_name;
			$categories[$tempTitle->getDBkey()."_task_force"] = $row->tf_name;
			$categories[$tempTitle->getDBkey()."_Task_Force"] = $row->tf_name;
		}
		
		$tables = array( 'page', 'categorylinks' );
		$fields = array( 'categorylinks.cl_to' );
		$conds = array( 'categorylinks.cl_to' => array_keys($categories) );
		$options = array( 'GROUP BY' => 'categorylinks.cl_to', 'ORDER BY' => 'value DESC' );
		$joinConds = array( 'categorylinks' =>
				array( 'left join', 'categorylinks.cl_from=page.page_id' ) );
		
		if ( $sortField == 'edits' ) {
			$cutoff = $db->timestamp( time() - $wgActiveStrategyPeriod );
			$cutoff = $db->addQuotes( $cutoff );
			$tables[] = 'revision';
			$joinConds['revision'] =
				array( 'left join',
					array( 'rev_page=page_id',
						"rev_timestamp > $cutoff",
						"rev_page IS NOT NULL" ) );
			$fields[] = 'count(distinct rev_id) + count(distinct thread_id) as value';
			
			// Include LQT posts
			$tables[] = 'thread';
			$joinConds['thread'] =
				array( 'left join',
					array( 'thread.thread_article_title=page.page_title',
						"thread.thread_modified > $cutoff" )
				);
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
			$taskForce = $categories[$row->cl_to];
			
			if ( $number > 0 ) {
				$html .= self::formatResult( $sk, $taskForce, $number, $sortField );
			}
		}
		
		$html = Xml::tags( 'div', array( 'class' => 'mw-activestrategy-output' ),
				$html );
		
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
			if ( $count > 0 ) {
				$output .= self::formatResult( $sk, $name, $count, 'members' );
			}
		}
		
		$output = Xml::tags( 'div', array( 'class' => 'mw-activestrategy-output' ),
				$output );
		
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

		$dbr = wfGetDB( DB_SLAVE );
		
		$count = $dbr->selectField( 'pagelinks', 'count(*)',
				array( 'pl_from' => $article->getId(),
					'pl_namespace' => NS_USER ), __METHOD__ );
		
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

