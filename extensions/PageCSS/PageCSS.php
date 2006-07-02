<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A parser hook to add per-page CSS to pages with the <css> tag
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfCssHook';
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'Page CSS',
	'description' => 'A parser hook to add per-page css to pages with the <nowiki><css></nowiki> tag',
	'author' => 'Ævar Arnfjörð Bjarmason'
);

function wfCssHook() {
	wfUsePHP( 5.1 );
	wfUseMW( '1.6alpha' );
	
	class CssHook {
		private $mCss;
		
		public function __construct() {
			global $wgParser, $wgHooks;

			$wgParser->setHook( 'css' , array( &$this, 'parseHook' ) );
			
			$wgHooks['SkinTemplateSetupPageCss'][] = array( &$this, 'hook' );
		}

		public function parseHook( $in, array $argv, Parser $parser ) {
			global $wgCssHookCss;

			$this->mCss .= trim( Sanitizer::checkCss( $in ) );
			$parser->disableCache(); // workaround for now
		}

		public function hook( &$css ) {
			if ( $this->mCss != '' )
				$css = "/*<![CDATA[*/\n" . htmlspecialchars( $this->mCss ) . "\n/*]]>*/";

			return false;
		}
	}

	// Establish a singleton.
	new CssHook;
}
