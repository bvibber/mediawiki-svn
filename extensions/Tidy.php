<?php
/**
 * An extension that adds a tidy tab on each page
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfTidy';
$wgExtensionCredits['other'][] = array(
	'name' => 'Tidy',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => 'Adds a tidy tab on each page allowing for quick viewing of pages with HTML tidy'
);


function wfTidy() {
	global $wgMessageCache, $wgHooks;
	
	$wgMessageCache->addMessage( 'tidy', 'Tidy' );
	
	$wgHooks['SkinTemplateContentActions'][] = 'wfTidyHook';
	$wgHooks['UnknownAction'][] = 'wfTidyActionHook';
}

function wfTidyHook( &$content_actions ) {
	global $wgRequest, $wgTitle;
	
	$action = $wgRequest->getText( 'tidy' );

	if ( $wgTitle->getNamespace() != NS_SPECIAL ) {
		$content_actions['tidy'] = array(
			'class' => $action == 'tidy' ? 'selected' : false,
			'text' => wfMsg( 'tidy' ),
			'href' => $wgTitle->getLocalUrl( 'action=tidy' )
		);
	}

	return true;
}

function wfTidyActionHook( $action, &$wgArticle ) {
	global $wgUseTidy;
	
	$wgUseTidy = true;

	$wgArticle->purge();

	return false;
}
