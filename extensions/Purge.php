<?php
/**
 * An extension that adds a purge tab on each page
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfPurge';
$wgExtensionCredits['other'][] = array(
	'name' => 'Purge',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => 'Adds a purge tab on each page allowing for quick purging of the cache'
);


function wfPurge() {
	global $wgMessageCache, $wgHooks;
	
	$wgMessageCache->addMessage( 'purge', 'Purge' );
	
	$wgHooks['SkinTemplateContentActions'][] = 'wfPurgeHook';
}

function wfPurgeHook( &$content_actions ) {
	global $wgRequest, $wgTitle;
	
	$action = $wgRequest->getText( 'purge' );

	if ( $wgTitle->getNamespace() != NS_SPECIAL ) {
		$content_actions['purge'] = array(
			'class' => false,
			'text' => wfMsg( 'purge' ),
			'href' => $wgTitle->getLocalUrl( 'action=purge' )
		);
	}

	return true;
}
