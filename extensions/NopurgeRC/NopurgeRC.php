<?php
/**
 * An extension that prevents old recentchanges entries from being deleted
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgHooks['ArticleEditUpdatesDeleteFromRecentchanges'][] = 'wfNopurgeRCHook';
$wgExtensionCredits['other'][] = array(
	'name' => 'NopurgeRC',
	'description' => 'A hook that prevents old recentchanges entries from being deleted',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'version'     => '1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:NoPurgeRC',
);

function wfNopurgeRCHook() {
	return false;
}
