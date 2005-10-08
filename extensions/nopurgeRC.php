<?php
/**
 * An extension that prevents old recentchanges entries from being deleted
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfNopurgeRC';
$wgExtensionCredits['other'][] = array(
	'name' => 'NopurgeRC',
	'description' => 'A hook that prevents old recentchanges entries from being deleted',
	'author' => 'Ævar Arnfjörð Bjarmason',
);

function wfNopurgeRC() {
	global $wgHooks;

	$wgHooks['ArticleEditUpdatesDeleteFromRecentchanges'][] = 'wfNopurgeRCHook';
}

function wfNopurgeRCHook() {
	return false;
}
