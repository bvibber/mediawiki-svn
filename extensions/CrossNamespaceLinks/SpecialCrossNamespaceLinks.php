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
	'version' => preg_replace('/^.* (\d\d\d\d-\d\d-\d\d) .*$/', '\1', '$LastChangedDate$'), #just the date of the last change
	'description' => 'lists links across namespaces that shouldn\'t exist on Wikimedia projects',
	'descriptionmsg' => 'crossnamespacelinks-desc',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'url' => 'http://www.mediawiki.org/wiki/Extension:CrossNamespaceLinks',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['CrossNamespaceLinks'] = $dir . 'SpecialCrossNamespaceLinks.i18n.php';
$wgAutoloadClasses['CrossNamespaceLinks'] = $dir . 'SpecialCrossNamespaceLinks_body.php';
$wgHooks['wgQueryPages'][] = 'wfSpecialCrossNamespaceLinksHook';
$wgSpecialPages['CrossNamespaceLinks'] = 'CrossNamespaceLinks';
$wgSpecialPageGroups['CrossNamespaceLinks'] = 'maintenance';

function wfSpecialCrossNamespaceLinksHook( &$QueryPages ) {
	$QueryPages[] = array(
		'CrossNamespaceLinksPage',
		'CrossNamespaceLinks',
		// Would probably be slow on large wikis -ævar
		//false
	);

	return true;
}
