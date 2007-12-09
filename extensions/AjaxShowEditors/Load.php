<?php
/** Number of seconds before an user is considered as no more editing */
$wgAjaxShowEditorsTimeout = 60;

$wgExtensionCredits['other'][] = array(
	'name' => 'Ajax Show Editors',
	'version' => '1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:AjaxShowEditors',
	'author' => 'Ashar Voultoiz',
	'description' => 'Let you see who is editing the page you are editing yourself.',
);

// Load the ajax responder and register it
require_once('extensions/AjaxShowEditors/Response.php');

// Load the hooks
require_once('extensions/AjaxShowEditors/Hooks.php');

// Function to load the i18n messages
function wfAjaxShowEditorsLoadI18n() {
	global $wgAjaxShowEditorsMessages ;
	require_once('extensions/AjaxShowEditors/AjaxShowEditors.i18n.php');
	global $wgMessageCache ;
	foreach( $wgAjaxShowEditorsMessages as $lang => $msg ) {
		$wgMessageCache->addMessages( $msg, $lang );
	}
}
