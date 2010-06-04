<?php
/**
 * Integration extension by Tisane
 * URL: http://www.mediawiki.org/wiki/Extension:Integration
 *
 * This program is free software. You can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version. You can also redistribute it and/or
 * modify it under the terms of the Creative Commons Attribution 3.0 license
 * or any later version.
 * 
 * This extension causes interwiki links to turn blue if the target page exists on
 * the target wiki, and red if it does not. It only works when the target wiki
 * is on the same wiki farm and is set up with this same extension.
 */
 
/* Alert the user that this is not a valid entry point to MediaWiki if they try to access the
special pages file directly.*/
 
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
		To install the Integration extension, put the following line in LocalSettings.php:
		require( "extensions/Integration/Integration.php" );
EOT;
	exit( 1 );
}
 
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Integration',
	'author' => 'Tisane',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Integration',
	'descriptionmsg' => 'integration-desc',
	'version' => '1.0.0',
);
 
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['IntegrationHooks'] = $dir . 'Integration.hooks.php';
$wgAutoloadClasses['PopulateIntegrationTable'] = "$dir/SpecialIntegration.php";
$wgExtensionMessagesFiles['Integration'] = $dir . 'Integration.i18n.php';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'IntegrationHooks::IntegrationCreateTable';
$wgHooks['LinkBegin'][] = 'IntegrationHooks::IntegrationLink';
$wgSpecialPages['PopulateIntegrationTable'] = 'PopulateIntegrationTable'; 
$wgSharedTables[] = 'integration_db';
$wgSharedTables[] = 'integration_namespace';
$wgIntegrationBrokenLinkStyle = "color: red";

$wgAvailableRights[] = 'integration';
$wgGroupPermissions['bureaucrat']['integration']    = true;