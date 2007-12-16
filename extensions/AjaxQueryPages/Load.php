<?php
// Common credits entries:
$aqpCredits = array(
		'name' => 'Ajax Query Pages',
		'author' => 'Ashar Voultoiz',
);

// We require 1.11alpha or later:
if( version_compare( $wgVersion, '1.11alpha', '<' ) ) {
	$wgExtensionCredits['other'][] = $aqpCredits + array(
		'description' => "Add some AJAX to QueryPages such as [[Special:Shortpages]].<br/>'''Disabled''', requires MediaWiki 1.11alpha or later.",
		);
} else {
	$wgExtensionCredits['other'][] = array(
		'name' => 'Ajax Query Pages',
		'url' => 'http://www.mediawiki.org/wiki/Extension:AjaxQueryPages',
		'author' => 'Ashar Voultoiz',
		'description' => 'Add some AJAX to QueryPages such as [[Special:Shortpages]]',
	);

	// Load hooks
	require_once('extensions/AjaxQueryPages/Hooks.php');

	// Set up AJAX entry point:
	require_once('extensions/AjaxQueryPages/Response.php');
}
