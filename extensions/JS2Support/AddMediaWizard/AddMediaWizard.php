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
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Add Media Wizard',
	'author' => 'Michael Dale and others',
	'version' => '0.1.1',
	'descriptionmsg' => 'addmediawizard-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:AddMediaWizard'
);

// Includes parent JS2Support
require_once( dirname( dirname( __FILE__ ) ) . "/JS2Support.php" );

// Add the addMediaWizard binding on pages that include the Edit Toolbar:
$wgHooks['EditPageBeforeEditToolbar'][] = 'AddMediaWizard::addJS';

// Add the Named path for js2 AddMediaWizardEditPage
$wgScriptLoaderNamedPaths[ 'AMWEditPage' ] = 'extensions/JS2Support/AddMediaWizard/AddMediaWizardEditPage.js';

class AddMediaWizard {
	public static function addJS( $toolbar) {
		global $wgOut;
		// Add the amwEditPage script to the "page" script bucket
		$wgOut->addScriptClass( 'AMWEditPage', 'page' );
		return true;
	}
}
