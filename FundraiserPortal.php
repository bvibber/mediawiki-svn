<?php error_reporting(E_ALL);
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
$wgFundraiserPortalURL = 'http://wikimediafoundation.org/wiki/Donate/Now/en?utm_medium=sidebar&utm_campaign=spontaneous_donation';
// Allowable templates: Plain, Ruby, RubyText, Sapphire, Tourmaline
$wgFundraiserPortalTemplates = array( 
				'Tourmaline' => 25,
				'Ruby' => 25,
				'RubyText' => 25,
				'Sapphire' => 25,);

$wgNoticeProjectPath = 'http://192.168.250.128/sandbox';

$wgFundraiserPortalDirectory = '/mnt/upload5/wikipedia/en';

/* Setup */

// Sets Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'FundraiserPortal',
	'author' => 'Trevor Parscal, Tomasz Finc',
	'version' => '0.2.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:FundraiserPortal',
	'descriptionmsg' => 'fundraiserportal-desc',
);


// Load some classes
$wgAutoloadClasses['DonateButton'] = dirname( __FILE__ ) . '/' . 'DonateButton.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['FundraiserPortal'] =
	dirname( __FILE__ ) . "/FundraiserPortal.i18n.php";

$wgExtensionFunctions[] = 'efFundraiserPortalSetup';


// Setup everything
function efFundraiserPortalSetup() {
	global $wgHooks;

	$wgHooks['BeforePageDisplay'][] = 'efFundraiserPortalLoader';
	$wgHooks['BuildSideBar'][] = 'efFundraiserPortalDisplay';

}

// Load the js that will choose the button client side
function efFundraiserPortalLoader( $out, $skin ) {
	global $wgOut,$wgLang;
	global $wgJsMimeType, $wgStyleVersion;
	global $wgNoticeProject, $wgNoticeProjectPath, $wgFundraiserPortalShow;
	
	// Only proceed if we are configured to show the portal
	if ( !$wgFundraiserPortalShow ) {
		return true;
	}

	// Pull in our loader
	//$lang = $wgLang->getCode();
	//$fundraiserLoader = "$wgNoticeProject/$lang/fundraiserportal.js";
	$fundraiserLoader = "fundraiserportal.js";
	$encFundraiserLoader = htmlspecialchars( "$wgNoticeProjectPath/$fundraiserLoader" );
	$wgOut->addInlineScript( "var wgFundraiserPortal='';");
	$wgOut->addScript( "<script type=\"{$wgJsMimeType}\" src=\"$encFundraiserLoader?$wgStyleVersion\"></script>\n" );

	return true;
}

// Finally display it if we got content
function efFundraiserPortalNoticeDisplay( $skin, &$bar ) {
	$bar =
		"<script type='text/javascript'>" .
		"if (wgFundraiserPortal != '') document.writeln(wgFundraiserPortal);" .
		"</script>" .
		$bar;
	return true;
}
