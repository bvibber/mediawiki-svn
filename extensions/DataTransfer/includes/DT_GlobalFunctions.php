<?php
/**
 * Global functions and constants for the Data Transfer extension.
 *
 * @author Yaron Koren
 */

if (!defined('MEDIAWIKI')) die();

define('DT_VERSION','0.1.8');

// constants for special properties
define('DT_SP_HAS_XML_GROUPING', 1);
define('DT_SP_IS_EXCLUDED_FROM_XML', 2);

$wgExtensionFunctions[] = 'dtgSetupExtension';

$dtgIP = $IP . '/extensions/DataTransfer';
require_once($dtgIP . '/languages/DT_Language.php');

if (version_compare($wgVersion, '1.11', '>=' )) {
	$wgExtensionMessagesFiles['DataTransfer'] = $dtgIP . '/languages/DT_Messages.php';
} else {
	$wgExtensionFunctions[] = 'dtfLoadMessagesManually';
}

/**
 *  Do the actual intialisation of the extension. This is just a delayed init that makes sure
 *  MediaWiki is set up properly before we add our stuff.
 */
function dtgSetupExtension() {
	global $dtgIP, $dtgVersion, $dtgNamespace;
	global $wgVersion, $wgLanguageCode, $wgExtensionCredits;

	if (version_compare($wgVersion, '1.11', '>='))
		wfLoadExtensionMessages('DataTransfer');

	dtfInitContentLanguage($wgLanguageCode);

	/**********************************************/
	/***** register specials                  *****/
	/**********************************************/
	require_once($dtgIP . '/specials/DT_ViewXML.php');

	/**********************************************/
	/***** register hooks                     *****/
	/**********************************************/

	/**********************************************/
	/***** credits (see "Special:Version")    *****/
	/**********************************************/
	$wgExtensionCredits['specialpage'][]= array(
		'name'           => 'Data Transfer',
		'version'        => DT_VERSION,
		'author'         => 'Yaron Koren',
		'url'            => 'http://www.mediawiki.org/wiki/Extension:Data_Transfer',
		'description'    => 'Exports wiki pages as XML, using template calls as the data structure',
		'descriptionmsg' => 'dt-desc',
	);

	return true;
}

/**********************************************/
/***** namespace settings                 *****/
/**********************************************/

/**********************************************/
/***** language settings                  *****/
/**********************************************/

/**
 * Initialise a global language object for content language. This
 * must happen early on, even before user language is known, to
 * determine labels for additional namespaces. In contrast, messages
 * can be initialised much later when they are actually needed.
 */
function dtfInitContentLanguage($langcode) {
	global $dtgIP, $dtgContLang;

	if (!empty($dtgContLang)) { return; }

	$dtContLangClass = 'DT_Language' . str_replace( '-', '_', ucfirst( $langcode ) );

	if (file_exists($dtgIP . '/languages/'. $dtContLangClass . '.php')) {
		include_once( $dtgIP . '/languages/'. $dtContLangClass . '.php' );
	}

	// fallback if language not supported
	if ( !class_exists($dtContLangClass)) {
		include_once($dtgIP . '/languages/DT_LanguageEn.php');
		$dtContLangClass = 'DT_LanguageEn';
	}

	$dtgContLang = new $dtContLangClass();
}

/**
 * Initialise the global language object for user language. This
 * must happen after the content language was initialised, since
 * this language is used as a fallback.
 */
function dtfInitUserLanguage($langcode) {
	global $dtgIP, $dtgLang;

	if (!empty($dtgLang)) { return; }

	$dtLangClass = 'DT_Language' . str_replace( '-', '_', ucfirst( $langcode ) );

	if (file_exists($dtgIP . '/languages/'. $dtLangClass . '.php')) {
		include_once( $dtgIP . '/languages/'. $dtLangClass . '.php' );
	}

	// fallback if language not supported
	if ( !class_exists($dtLangClass)) {
		global $dtgContLang;
		$dtgLang = $dtgContLang;
	} else {
		$dtgLang = new $dtLangClass();
	}
}

/**
 * Setting of message cache for versions of MediaWiki that do not support
 * wgExtensionMessagesFiles
 */
function dtfLoadMessagesManually() {
	global $dtgIP, $wgMessageCache;

	# add messages
	require($dtgIP . '/languages/DT_Messages.php');
	foreach($messages as $key => $value) {
		$wgMessageCache->addMessages($messages[$key], $key);
	}
}

/**********************************************/
/***** other global helpers               *****/
/**********************************************/
