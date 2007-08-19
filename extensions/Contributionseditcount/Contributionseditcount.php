<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Display an edit count at the top of Special:Contributions
 *
 * @addtogroup Extensions
 *
 * @bug 1725
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfContributionseditcount';
$wgExtensionCredits['other'][] = array(
	'name' => 'Contributionseditcount',
	'description' => 'displays an edit count on Special:Contributions',
	'author' => 'Ævar Arnfjörð Bjarmason'
);

function wfContributionseditcount() {

	wfUsePHP( 5.0 );
	wfUseMW( '1.6alpha' );

	class Contributionseditcount {

		public function __construct() {
			global $wgMessageCache, $wgHooks;
			require_once( dirname( __FILE__ ) . '/Contributionseditcount.i18n.php' );
			foreach( efContributionseditcountMessages() as $lang => $messages )
				$wgMessageCache->addMessages( $messages, $lang );
			$wgHooks['SpecialContributionsBeforeMainOutput'][] = array( &$this, 'hook' );
		}
		
		public function hook( $uid ) {
			global $wgOut, $wgLang;
			if ( $uid != 0 )
				$wgOut->addWikiText( wfMsg( 'contributionseditcount', $wgLang->formatNum( User::edits( $uid ) ) ) );
			return true;
		}
	}

	new Contributionseditcount();

}