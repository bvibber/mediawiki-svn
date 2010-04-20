<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * A querypage to list the most wanted categories - implements Special:Wantedcategories
 *
 * @ingroup SpecialPage
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class WantedCategoriesPage extends WantedQueryPage {

	function getName() {
		return 'Wantedcategories';
	}

	function getQueryInfo() {
		return array (
			'tables' => array ( 'categorylinks', 'page' ),
			'fields' => array ( "'{$this->getName()}' AS type",
					"'" . NS_CATEGORY . "' AS namespace",
					'cl_to AS title',
					'COUNT(*) AS value' ),
			'conds' => array ( 'page_title IS NULL' ),
			'options' => array ( 'GROUP BY' => 'cl_to' ),
			'join_conds' => array ( 'page' => array ( 'LEFT JOIN',
				array ( 'page_title = cl_to',
					'page_namespace' => NS_CATEGORY ) ) )
		);
	}

	function formatResult( $skin, $result ) {
		global $wgLang, $wgContLang;

		$nt = Title::makeTitle( $result->namespace, $result->title );
		$text = htmlspecialchars( $wgContLang->convert( $nt->getText() ) );

		$plink = $this->isCached() ?
			$skin->link( $nt, $text ) :
			$skin->link(
				$nt,
				$text,
				array(),
				array(),
				array( 'broken' )
			);

		$nlinks = wfMsgExt( 'nmembers', array( 'parsemag', 'escape'),
			$wgLang->formatNum( $result->value ) );
		return wfSpecialList($plink, $nlinks);
	}
}

/**
 * constructor
 */
function wfSpecialWantedCategories() {
	list( $limit, $offset ) = wfCheckLimits();

	$wpp = new WantedCategoriesPage();

	$wpp->doQuery( $offset, $limit );
}
