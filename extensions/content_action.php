<?php
/**
 * An extension that demonstrated how to use the SkinTemplateContentActions
 * hook to add a new content action
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 */

$wgExtensionFunctions[] = 'wfAddaction';
$wgExtensionCredits['other'][] = array(
	'name' => 'Content action hook',
	'author' => 'Ævar Arnfjörð Bjarmason',
);


function wfAddaction() {
	global $wgHooks, $wgMessageCache;
	$wgMessageCache->addMessage( 'myact', 'My action' );
	$wgHooks['SkinTemplateContentActions'][] = 'wfAddactionContentHook';
	$wgHooks['UnknownAction'][] = 'wfAddactActionHook';
}

function wfAddActionContentHook( &$content_actions ) {
	global $wgRequest, $wgRequest, $wgTitle;
	
	$action = $wgRequest->getText( 'action' );

	if ($wgTitle->getNamespace() != NS_SPECIAL) {
		$content_actions['myact'] = array(
			'class' => $action == 'myact' ? 'selected' : false,
			'text' => wfMsg( 'myact' ),
			'href' => $wgTitle->getLocalUrl( 'action=myact' )
		);
	}

	return true;
}

function wfAddactActionHook( $action, &$wgArticle ) {
	global $wgOut;
	
	$title = $wgArticle->getTitle(); 
	
	if ($action == 'myact')
		$wgOut->addHTML( 'The page name is ' . $title->getText() . ' and you are ' . $wgArticle->getUserText() );

	return false;
}
