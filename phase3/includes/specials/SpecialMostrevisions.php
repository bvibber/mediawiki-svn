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
class MostrevisionsPage extends FewestrevisionsPage {
	function __construct() {
		SpecialPage::__construct( 'Mostrevisions' );
	}
	
	function sortDescending() {
		return true;
	}
}
