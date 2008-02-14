<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	exit(1);
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Crosswiki Blocking',
	'author' => 'VasilievVV',
	'version' => '1.0alpha',
//	'url' => 'http://www.mediawiki.org/wiki/Extension:Crosswiki_Blocking',
	'description' => 'Allows to block users on other wikis',
	'descriptionmsg' => 'crosswikiblock-desc',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['CrosswikiBlock'] = $dir . 'CrosswikiBlock.i18n.php';
$wgAutoloadClasses['SpecialCrosswikiBlock'] = $dir . 'CrosswikiBlock.page.php';

//$wgExtensionFunctions[] = 'efSetupTitleBlacklistHooks';

$wgAvailableRights[] = 'crosswikiblock';
$wgGroupPermissions['steward']['crosswikiblock'] = true;

$wgSpecialPages['Crosswikiblock'] = 'SpecialCrosswikiBlock';
