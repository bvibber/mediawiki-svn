<?php
/**
 * Add Media Wizard extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the Add Media Wizard support
 * The addMediaWizard is dependent on JS2Support and
 * the core "AddMedia" module
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/JS2Support/AddMediaWizard/AddMediaWizard.php" );
 *
 * @author Michael Dale <mdale@wikimedia.org> and others
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Credits
$wgExtensionCredits['jsModule'][] = array(
	'path' => __FILE__,
	'name' => 'Add Media Wizard',
	'author' => array( 'Michael Dale', 'others' ),
	'version' => '0.1.1',
	'descriptionmsg' => 'addmediawizard-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:AddMediaWizard'
);

$AMWdir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['AddMediaWizard'] = $AMWdir . 'AddMediaWizard.i18n.php';

// Check for JS2 support
if( ! isset( $wgEnableJS2system ) ){
	throw new MWException( 'AddMediaWizard requires JS2 Support. Please include the JS2Support extension.');
}

// Add the Named path for JS2 AddMediaWizard "activator" for easy addition to EditPageBeforeEditToolbar
$wgScriptLoaderNamedPaths[ 'AMWEditPage' ] = 'extensions/AddMediaWizard/AddMediaWizardEditPage.js';

// Add the addMediaWizard binding on pages that include the Edit Toolbar:
$wgHooks['EditPageBeforeEditToolbar'][] = 'AddMediaWizard::addJS';


// Add the javascript loader for "AddMedia module"
$wgExtensionJavascriptLoader[] = 'extensions/AddMediaWizard/AddMedia/loader.js';

// Add the javascript loader for "ClipEdit module"
$wgExtensionJavascriptLoader[] = 'extensions/AddMediaWizard/ClipEdit/loader.js';

// Add the apiProxy ( client ) so that we can upload cross domain to commons
$wgExtensionJavascriptLoader[] = 'extensions/AddMediaWizard/ApiProxy/loader.js';

class AddMediaWizard {
	public static function addJS( $toolbar) {
		global $wgOut;
		// Add the AMWEditPage activator to the edit page in the "page" script bucket
		$wgOut->addScriptClass( 'AMWEditPage', 'page' );
		return true;
	}
}
