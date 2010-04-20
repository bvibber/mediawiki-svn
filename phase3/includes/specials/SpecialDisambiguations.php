<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * @ingroup SpecialPage
 */
class DisambiguationsPage extends PageQueryPage {

	function getName() {
		return 'Disambiguations';
	}

	function isExpensive() { return true; }
	function isSyndicated() { return false; }


	function getPageHeader() {
		return wfMsgExt( 'disambiguations-text', array( 'parse' ) );
	}

	function getSQL() {
		global $wgContentNamespaces;

		$dbr = wfGetDB( DB_SLAVE );

		$dMsgText = wfMsgForContent('disambiguationspage');

		$linkBatch = new LinkBatch;

		# If the text can be treated as a title, use it verbatim.
		# Otherwise, pull the titles from the links table
		$dp = Title::newFromText($dMsgText);
		if( $dp ) {
			if($dp->getNamespace() != NS_TEMPLATE) {
				# FIXME we assume the disambiguation message is a template but
				# the page can potentially be from another namespace :/
				wfDebug("Mediawiki:disambiguationspage message does not refer to a template!\n");
			}
			$linkBatch->addObj( $dp );
		} else {
				# Get all the templates linked from the Mediawiki:Disambiguationspage
				$disPageObj = Title::makeTitleSafe( NS_MEDIAWIKI, 'disambiguationspage' );
				$res = $dbr->select(
					array('pagelinks', 'page'),
					'pl_title',
					array('page_id = pl_from', 'pl_namespace' => NS_TEMPLATE,
						'page_namespace' => $disPageObj->getNamespace(), 'page_title' => $disPageObj->getDBkey()),
					__METHOD__ );

				while ( $row = $dbr->fetchObject( $res ) ) {
					$linkBatch->addObj( Title::makeTitle( NS_TEMPLATE, $row->pl_title ));
				}

				$dbr->freeResult( $res );
		}

		$set = $linkBatch->constructSet( 'lb.tl', $dbr );
		if( $set === false ) {
			# We must always return a valid sql query, but this way DB will always quicly return an empty result
			$set = 'FALSE';
			wfDebug("Mediawiki:disambiguationspage message does not link to any templates!\n");
		}

		list( $page, $pagelinks, $templatelinks) = $dbr->tableNamesN( 'page', 'pagelinks', 'templatelinks' );

		if ( $wgContentNamespaces ) {
			$nsclause = 'IN (' . $dbr->makeList( $wgContentNamespaces ) . ')';
		} else {
			$nsclause = '= ' . NS_MAIN;
		}

		$sql = "SELECT 'Disambiguations' AS \"type\", pb.page_namespace AS namespace,"
			." pb.page_title AS title, la.pl_from AS value"
			." FROM {$templatelinks} AS lb, {$page} AS pb, {$pagelinks} AS la, {$page} AS pa"
			." WHERE $set"  # disambiguation template(s)
			.' AND pa.page_id = la.pl_from'
			.' AND pa.page_namespace ' . $nsclause
			.' AND pb.page_id = lb.tl_from'
			.' AND pb.page_namespace = la.pl_namespace'
			.' AND pb.page_title = la.pl_title'
			.' ORDER BY lb.tl_namespace, lb.tl_title';

		return $sql;
	}
	
	function getQueryInfo() {
		$dbr = wfGetDB( DB_SLAVE );
		$dMsgText = wfMsgForContent('disambiguationspage');
		$linkBatch = new LinkBatch;

		# If the text can be treated as a title, use it verbatim.
		# Otherwise, pull the titles from the links table
		$dp = Title::newFromText($dMsgText);
		if( $dp ) {
			if($dp->getNamespace() != NS_TEMPLATE) {
				# FIXME we assume the disambiguation message is a template but
				# the page can potentially be from another namespace :/
				wfDebug("Mediawiki:disambiguationspage message does not refer to a template!\n");
			}
			$linkBatch->addObj( $dp );
		} else {
				# Get all the templates linked from the Mediawiki:Disambiguationspage
				$disPageObj = Title::makeTitleSafe( NS_MEDIAWIKI, 'disambiguationspage' );
				$res = $dbr->select(
					array('pagelinks', 'page'),
					'pl_title',
					array('page_id = pl_from', 'pl_namespace' => NS_TEMPLATE,
						'page_namespace' => $disPageObj->getNamespace(), 'page_title' => $disPageObj->getDBkey()),
					__METHOD__ );

				while ( $row = $dbr->fetchObject( $res ) ) {
					$linkBatch->addObj( Title::makeTitle( NS_TEMPLATE, $row->pl_title ));
				}

				$dbr->freeResult( $res );
		}
		$set = $linkBatch->constructSet( 'tl', $dbr );
		if( $set === false ) {
			# We must always return a valid SQL query, but this way
			# the DB will always quickly return an empty result
			$set = 'FALSE';
			wfDebug("Mediawiki:disambiguationspage message does not link to any templates!\n");
		}
		
		// FIXME: What are pagelinks and p2 doing here?
		return array (
			'tables' => array( 'templatelinks', 'page AS p1', 'pagelinks', 'page AS p2' ),
			'fields' => array( "'{$this->getName()}' AS type",
					'p1.page_namespace AS namespace',
					'p1.page_title AS title',
					'pl_from AS value' ),
			'conds' => array( $set,
					'p1.page_id = tl_from',
					'pl_namespace = p1.page_namespace',
					'pl_title = p1.page_title',
					'p2.page_id = pl_from',
					'p2.page_namespace' => NS_MAIN )
		);
	}

	function getOrderFields() {
		return array('tl_namespace', 'tl_title', 'value');
	}
	
	function sortDescending() {
		return false;
	}

	function formatResult( $skin, $result ) {
		global $wgContLang;
		$title = Title::newFromID( $result->value );
		$dp = Title::makeTitle( $result->namespace, $result->title );

		$from = $skin->link( $title );
		$edit = $skin->link( $title, wfMsgExt( 'parentheses', array( 'escape' ), wfMsg( 'editlink' ) ) , array(), array( 'redirect' => 'no', 'action' => 'edit' ) );
		$arr  = $wgContLang->getArrow();
		$to   = $skin->link( $dp );

		return "$from $edit $arr $to";
	}
}

/**
 * Constructor
 */
function wfSpecialDisambiguations() {
	list( $limit, $offset ) = wfCheckLimits();

	$sd = new DisambiguationsPage();

	return $sd->doQuery( $offset, $limit );
}
