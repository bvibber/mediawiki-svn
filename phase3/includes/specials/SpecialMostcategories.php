<?php
/**
 * @file
 * @ingroup SpecialPage
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

/**
 * implements Special:Mostcategories
 * @ingroup SpecialPage
 */
class MostcategoriesPage extends QueryPage {

	function __construct() {
		SpecialPage::__construct( 'Mostcategories' );
	}
	
	function isExpensive() { return true; }
	function isSyndicated() { return false; }

	function getQueryInfo() {
		return array (
			'tables' => array ( 'categorylinks', 'page' ),
			'fields' => array ( 'page_namespace AS namespace',
					'page_title AS title',
					'COUNT(*) AS value' ),
			'conds' => array ( 'page_namespace' => MWNamespace::getContentNamespaces() ),
			'options' => array ( 'HAVING' => 'COUNT(*) > 1',
				'GROUP BY' => 'page_namespace, page_title' ),
			'join_conds' => array ( 'page' => array ( 'LEFT JOIN',
					'page_id = cl_from' ) )
		);
	}

	function formatResult( $skin, $result ) {
		global $wgLang;
		$title = Title::makeTitleSafe( $result->namespace, $result->title );

		$count = wfMsgExt( 'ncategories', array( 'parsemag', 'escape' ), $wgLang->formatNum( $result->value ) );
		$link = $skin->link( $title );
		return wfSpecialList( $link, $count );
	}
}
