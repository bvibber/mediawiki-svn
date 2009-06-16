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

	function formatResult( $skin, $result ) {
		global $wgLang, $wgContLang;

		$nt = Title::makeTitle( $result->namespace, $result->title );
		$text = $wgContLang->convert( $nt->getPrefixedText() );

		$plink = $skin->linkKnown( $nt, $text );

		$nl = wfMsgExt( 'nrevisions', array( 'parsemag', 'escape'),
			$wgLang->formatNum( $result->value ) );
		$nlink = $skin->linkKnown(
			$nt,
			$nl,
			array(),
			array( 'action' => 'history' )
		);

		return wfSpecialList($plink, $nlink);
	}

	function sortDescending() {
		return true;
	}
}
