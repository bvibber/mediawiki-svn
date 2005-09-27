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
	'description' => 'A parser hook to add per-page css to pages with the <css> tag',
	'author' => 'Ævar Arnfjörð Bjarmason'
);
$wgCssHookCss = '';

function wfCssHook() {
	global $wgParser, $wgHooks;
	
	$wgParser->setHook( 'css' , 'wfCssHookParse' );
	$wgHooks['SkinTemplateSetupPageCss'][] = 'wfCssHookHook';
}

function wfCssHookParse( $in, $argv ) {
	global $wgCssHookCss;

	$wgCssHookCss .= trim( $in );
}

function wfCssHookHook( &$css ) {
	global $wgCssHookCss;
	
	if ( $wgCssHookCss != '' )
		$css = "/*<![CDATA[*/\n" . htmlspecialchars( $wgCssHookCss ) . "\n/*]]>*/";

	return false;
}
