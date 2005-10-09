<?php
/**
 * An extension to make the wiki issue HTTP redirects rather than wiki redirects
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgHooks['ArticleViewRedirect'][] = 'wfHTTPRedirectHook';
$wgExtensionCredits['other'][] = array(
	'name' => 'HTTP redirect',
	'description' => 'A hook to make the wiki issue HTTP redirects rather than wiki redirects',
	'author' => 'Ævar Arnfjörð Bjarmason',
);

function wfHTTPRedirectHook( &$article ) {
	global $wgOut;
	
	$wgOut->redirect( $article->mTitle->escapeFullURL(), 302 );
	
	return false;
}
