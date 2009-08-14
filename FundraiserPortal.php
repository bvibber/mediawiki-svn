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

// on/off Switch
$wgFundraiserPortalShow = true;

// Set this to the base target of any button 
$wgFundraiserPortalURL = 'http://wikimediafoundation.org/wiki/Donate/Now/en?utm_medium=sidebar&utm_campaign=spontaneous_donation';

// Set this to the location the extensions images
$wgImageUrl = $wgScriptPath . '/extensions/FundraiserPortal/images';

// Allowable templates: Plain, Ruby, RubyText, Sapphire, Tourmaline
$wgFundraiserPortalTemplates = array( 
				'Ruby' => 25,
				'Tourmaline' => 25,
				'RubyText' => 25,
				'Sapphire' => 25,
				);

// Set this to the public path where your js is pulled from
$wgFundraiserPortalPath = 'http://192.168.250.128/sandbox';

// Set this to the systme path location that the button js file will be written to
// Must be reachable by the address in $wgNoticeProjectPath
$wgFundraiserPortalDirectory = '/var/www/sandbox';

// Only running this on wikipedia for now
$wgFundraiserPortalProject = 'wikipedia';

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


// Register hooks
function efFundraiserPortalSetup() {
	global $wgHooks;

	$wgHooks['BeforePageDisplay'][] = 'efFundraiserPortalLoader';
	$wgHooks['SkinBuildSidebar'][] = 'efFundraiserPortalNoticeDisplay';
}

// Load the js that will choose the button client side
function efFundraiserPortalLoader( $out, $skin ) {
	global $wgOut, $wgLang;
	global $wgJsMimeType, $wgStyleVersion;
	global $wgFundraiserPortalShow, $wgFundraiserPortalProject, $wgFundraiserPortalPath;
	
	// Only proceed if we are configured to show the portal
	if ( !$wgFundraiserPortalShow ) {
		return true;
	}

	// Pull in our loader
	$lang = $wgLang->getCode();
	$fundraiserLoader = "$wgFundraiserPortalProject/$lang/fundraiserportal.js";
	$encFundraiserLoader = htmlspecialchars( "$wgFundraiserPortalPath/$fundraiserLoader" );
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
