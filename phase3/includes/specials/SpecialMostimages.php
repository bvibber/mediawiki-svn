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
 * implements Special:Mostimages
 * @ingroup SpecialPage
 */
class MostimagesPage extends ImageQueryPage {

	function getName() { return 'Mostimages'; }
	function isExpensive() { return true; }
	function isSyndicated() { return false; }

	function getQueryInfo() {
		return array (
			'tables' => array ( 'imagelinks' ),
			'fields' => array ( "'" . NS_FILE . "' AS namespace",
					'il_to AS title',
					'COUNT(*) AS value' ),
			'options' => array ( 'GROUP BY' => 'il_to',
					'HAVING' => 'COUNT(*) > 1' )
		);
	}

	function getCellHtml( $row ) {
		global $wgLang;
		return wfMsgExt( 'nlinks',  array( 'parsemag', 'escape' ),
			$wgLang->formatNum( $row->value ) ) . '<br />';
	}

}

/**
 * Constructor
 */
function wfSpecialMostimages() {
	list( $limit, $offset ) = wfCheckLimits();

	$wpp = new MostimagesPage();

	$wpp->doQuery( $offset, $limit );
}
