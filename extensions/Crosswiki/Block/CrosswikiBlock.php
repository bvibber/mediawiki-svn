<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	exit(1);
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Crosswiki Blocking',
	'author' => 'VasilievVV',
	'version' => '1.0alpha',
//	'url' => 'http://www.mediawiki.org/wiki/Extension:Crosswiki_Blocking',
	'description' => 'Allows to block users on other wikis'
);

$wgExtensionMessagesFiles['CrosswikiBlock'] = dirname( __FILE__ ) . '/CrosswikiBlock.i18n.php';
$wgAutoloadClasses['SpecialCrosswikiBlock'] = dirname( __FILE__ ) . '/CrosswikiBlock.page.php';

//$wgExtensionFunctions[] = 'efSetupTitleBlacklistHooks';

$wgAvailableRights[] = 'crosswikiblock';
$wgGroupPermissions['steward']['crosswikiblock'] = true;

$wgSpecialPages['Crosswikiblock'] = 'SpecialCrosswikiBlock';