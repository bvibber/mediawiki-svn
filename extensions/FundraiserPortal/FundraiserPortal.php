<?php
/**
 * FundraiserPortal extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the main include file for the FundraiserPortal
 * extension of MediaWiki.
 *
 * Usage: Add the following line to your LocalSettings.php file
 * require_once( "$IP/extensions/FundraiserPortal/FundraiserPortal.php" );
 *
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * Allow "or a later version" here?
 * @license GPL v2
 * @version 0.1.1
 */

/* Configuration */

$wgFundraiserPortalShow = true;
$wgFundraiserPortalURL = 'http://wikimediafoundation.org/wiki/Donate/Now/en';

/* Setup */

// Sets Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'FundraiserPortal',
	'author' => 'Trevor Parscal',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:FundraiserPortal',
	'descriptionmsg' => 'fundraiserportal-desc',
);

// Adds Autoload Classes
$wgAutoloadClasses['FundraiserPortalHooks'] =
	dirname( __FILE__ ) . "/FundraiserPortal.hooks.php";

// Adds Internationalized Messages
$wgExtensionMessagesFiles['FundraiserPortal'] =
	dirname( __FILE__ ) . "/FundraiserPortal.i18n.php";

// Registers Hooks
$wgHooks['SkinBuildSidebar'][] = 'FundraiserPortalHooks::buildSidebar';