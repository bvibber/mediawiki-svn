<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * A querypage to list the most wanted templates - implements Special:Wantedtemplates
 * based on SpecialWantedcategories.php by Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 *
 * @ingroup SpecialPage
 *
 * @author Danny B.
 * @copyright Copyright © 2008, Danny B.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class WantedTemplatesPage extends WantedQueryPage {

	function getName() {
		return 'Wantedtemplates';
	}

	function getQueryInfo() {
		return array (
			'tables' => array ( 'templatelinks', 'page' ),
			'fields' => array ( 'tl_namespace AS namespace',
					'tl_title AS title',
					'COUNT(*) AS value' ),
			'conds' => array ( 'page_title IS NULL',
					'tl_namespace' => NS_TEMPLATE ),
			'options' => array (
				'GROUP BY' => 'tl_namespace, tl_title' ),
			'join_conds' => array ( 'page' => array ( 'LEFT JOIN',
					array ( 'page_namespace = tl_namespace',
						'page_title = tl_title' ) ) )
		);
	}
}

/**
 * constructor
 */
function wfSpecialWantedTemplates() {
	list( $limit, $offset ) = wfCheckLimits();

	$wpp = new WantedTemplatesPage();

	$wpp->doQuery( $offset, $limit );
}
