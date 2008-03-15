<?php
/** Number of seconds before an user is considered as no more editing */
$wgAjaxShowEditorsTimeout = 60;

$wgExtensionCredits['other'][] = array(
	'name' => 'Ajax Show Editors',
	'version' => preg_replace('/^.* (\d\d\d\d-\d\d-\d\d) .*$/', '\1', '$LastChangedDate$'), #just the date of the last change
	'url' => 'http://www.mediawiki.org/wiki/Extension:AjaxShowEditors',
	'author' => 'Ashar Voultoiz',
	'description' => 'Let you see who is editing the page you are editing yourself.',
	'descriptionmsg' => 'ajax-se-desc',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['AjaxShowEditors'] =  $dir . 'AjaxShowEditors.i18n.php';

// Load the ajax responder and register it
require_once( $dir . 'Response.php');

// Load the hooks
require_once( $dir . 'Hooks.php');
