<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A special page querypage extension (the first querypage extension) that
 * lists cross-namespace links that shouldn't exist on Wikimedia projects.
 *
 * @addtogroup Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Cross-namespace links',
	'version' => '2008-01-09',
	'description' => 'lists links across namespaces that shouldn\'t exist on Wikimedia projects',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'url' => 'http://www.mediawiki.org/wiki/Extension:CrossNamespaceLinks',
);

$wgExtensionMessagesFiles['CrossNamespaceLinks'] = dirname(__FILE__) . '/SpecialCrossNamespaceLinks.i18n.php';
$wgHooks['wgQueryPages'][] = 'wfSpecialCrossNamespaceLinksHook';

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialCrossNamespaceLinks_body.php', 'CrossNamespaceLinks', 'CrossNamespaceLinks' );

function wfSpecialCrossNamespaceLinksHook( &$QueryPages ) {
	wfLoadExtensionMessages( 'CrossNamespaceLinks' );

	$QueryPages[] = array(
		'CrossNamespaceLinksPage',
		'CrossNamespaceLinks',
		// Would probably be slow on large wikis -ævar
		//false
	);

	return true;
}
