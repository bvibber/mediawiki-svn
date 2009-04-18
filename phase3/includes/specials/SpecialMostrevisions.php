<?php
/**
 * A special page to show pages in the
 *
 * @ingroup SpecialPage
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

/**
 * @ingroup SpecialPage
 */
class MostrevisionsPage extends FewestrevisionsPage {
	function sortDescending() {
		return true;
	}
}

/**
 * constructor
 */
function wfSpecialMostrevisions() {
	list( $limit, $offset ) = wfCheckLimits();

	$wpp = new MostrevisionsPage();

	$wpp->doQuery( $offset, $limit );
}
