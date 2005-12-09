<?php
/**
 * An extension that adds two new permissions that relate to viewing
 * Special:Contributions and Special:Listusers
 *
 * @bug 3294
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgGroupPermissions['*']['listusers'] = true;
$wgGroupPermissions['*']['listcontributions'] = true;

$wgHooks['wfSpecialContributionsAfterId'][] = 'wfContributionsviewpermissionSpecialContributionsAfterIdHook';
$wgHooks['wfSpecialListusers'][] = 'wfContributionsviewpermissionSpecialListusersHook';

$wgExtensionCredits['other'][] = array(
	'name' => 'Contributionsviewpermission',
	'description' => 'Adds a permission which allows limiting the ability to view [[Special:Contributions|Contributions]] and [[Special:Listusers|Listusers]]',
	'author' => 'Ævar Arnfjörð Bjarmason',
);

function wfContributionsviewpermissionSpecialContributionsAfterIdHook( &$contributions, &$id ) {
	global $wgUser, $wgOut;

	if ( ! $wgUser->isAllowed( 'listcontributions' ) && $wgUser->getId() !== (int)$id ) {
		$wgOut->permissionRequired( 'listcontributions' );
		return false;
	} else
		return true;
}

function wfContributionsviewpermissionSpecialListusersHook() {
	global $wgUser, $wgOut;

	if ( ! $wgUser->isAllowed( 'listusers' ) ) {
		$wgOut->permissionRequired( 'listusers' );
		return false;
	} else
		return true;
}
