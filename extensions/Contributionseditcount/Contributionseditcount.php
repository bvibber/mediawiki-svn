<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Display an edit count at the top of Special:Contributions
 *
 * @package MediaWiki
 * @subpackage Extensions
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

			$wgMessageCache->addMessage( 'contributionseditcount', 'This user has $1 edits.' );

			$wgHooks['SpecialContributionsBeforeMainOutput'][] = array( &$this, 'hook' );
		}
		
		public function hook( $uid ) {
			global $wgOut, $wgLang;

			if ( $uid != 0 )
				$wgOut->addWikiText( wfMsg( 'contributionseditcount', $wgLang->formatNum( User::edits( $uid ) ) ) );

			return true;
		}
	}

	// Establish a singleton.
	new Contributionseditcount;
}
?>
