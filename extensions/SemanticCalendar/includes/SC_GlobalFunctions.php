<?php
/**
 * Global functions and constants for Semantic Calendar
 *
 * @author Yaron Koren
 */

if (!defined('MEDIAWIKI')) die();

define('SC_VERSION','0.1.2');

// constants for special properties

$wgExtensionFunctions[] = 'scgSetupExtension';
$wgExtensionFunctions[] = 'scgParserFunctions';
$wgHooks['LanguageGetMagic'][] = 'scgLanguageGetMagic';
$wgExtensionMessagesFiles['SemanticCalendar'] = $scgIP . '/languages/SC_Messages.php';

require_once($scgIP . '/includes/SC_ParserFunctions.php');
require_once($scgIP . '/languages/SC_Language.php');

/**
 *  Do the actual intialization of the extension. This is just a delayed init that makes sure
 *  MediaWiki is set up properly before we add our stuff.
 */
function scgSetupExtension() {
	global $scgNamespace, $scgIP, $wgExtensionCredits, $wgArticlePath, $wgScriptPath, $wgServer;

	scfInitMessages();

	/**********************************************/
	/***** register specials                  *****/
	/**********************************************/

	/**********************************************/
	/***** register hooks                     *****/
	/**********************************************/

	/**********************************************/
	/***** create globals for outside hooks   *****/
	/**********************************************/

	/**********************************************/
	/***** credits (see "Special:Version")    *****/
	/**********************************************/
	$wgExtensionCredits['parserhook'][]= array(
		'name'        => 'Semantic Calendar',
		'version'     => SC_VERSION,
		'author'      => 'Yaron Koren',
		'url'         => 'http://www.mediawiki.org/wiki/Extension:Semantic_Calendar',
		'description' =>  'A calendar that displays semantic date information',
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
function scfInitContentLanguage($langcode) {
	global $scgIP, $scgContLang;

	if (!empty($scgContLang)) { return; }

	$scContLangClass = 'SC_Language' . str_replace( '-', '_', ucfirst( $langcode ) );

	if (file_exists($scgIP . '/languages/'. $scContLangClass . '.php')) {
		include_once( $scgIP . '/languages/'. $scContLangClass . '.php' );
	}

	// fallback if language not supported
	if ( !class_exists($scContLangClass)) {
		include_once($scgIP . '/languages/SC_LanguageEn.php');
		$scContLangClass = 'SC_LanguageEn';
	}

	$scgContLang = new $scContLangClass();
}

/**
 * Initialise the global language object for user language. This
 * must happen after the content language was initialised, since
 * this language is used as a fallback.
 */
function scfInitUserLanguage($langcode) {
	global $scgIP, $scgLang;

	if (!empty($scgLang)) { return; }

	$scLangClass = 'SC_Language' . str_replace( '-', '_', ucfirst( $langcode ) );
	if (file_exists($scgIP . '/languages/'. $scLangClass . '.php')) {
		include_once( $scgIP . '/languages/'. $scLangClass . '.php' );
	}

	// fallback if language not supported
	if ( !class_exists($scLangClass)) {
		global $scgContLang;
		$scgLang = $scgContLang;
	} else {
		$scgLang = new $scLangClass();
	}
}

/**
 * Initialize messages - these settings must be applied later on, since
 * the MessageCache does not exist yet when the settings are loaded in
 * LocalSettings.php.
 * Function based on version in ContributionScores extension
 */
function scfInitMessages() {
        global $wgVersion, $wgExtensionFunctions;
        if (version_compare($wgVersion, '1.11', '>=' )) {
                wfLoadExtensionMessages( 'SemanticCalendar' );
        } else {
                $wgExtensionFunctions[] = 'scfLoadMessagesManually';
        }
}

/**
 * Setting of message cache for versions of MediaWiki that do not support
 * wgExtensionFunctions - based on ceContributionScores() in
 * ContributionScores extension
 */
function scfLoadMessagesManually() {
        global $scgIP, $wgMessageCache;

        # add messages
        require($scgIP . '/languages/SC_Messages.php');
        foreach($messages as $key => $value) {
                $wgMessageCache->addMessages($messages[$key], $key);
        }
}

/**********************************************/
/***** other global helpers               *****/
/**********************************************/

// replacement for cal_days_in_month(), since not all versions of PHP
// have it; code taken from http://www.webmasterworld.com/forum88/10544.htm
function scfDaysInMonth($month, $year) {
	return date('t', mktime(0, 0, 0, $month + 1, 0, $year));
}

function scfGetEvents_1_0($date_property, $filter_query) {
	global $smwgIP;
	include_once($smwgIP . "/includes/SMW_QueryProcessor.php");
	$events = array();
	$query_string = "[[$date_property::*]][[$date_property::+]]$filter_query";
	$params = array();
	$inline = true;
	$format = 'auto';
	$printlabel = "";
	$printouts[] = new SMWPrintRequest(SMW_PRINT_THIS, $printlabel);
	$query  = SMWQueryProcessor::createQuery($query_string, $params, $inline, $format, $printouts);
	$results = smwfGetStore()->getQueryResult($query);
	while ($row = $results->getNext()) {
		$event_names = $row[0];
		$event_dates = $row[1];
		$event_title = $event_names->getNextObject()->getTitle();
		while ($event_date = $event_dates->getNextObject()) {
			$actual_date = date("Y-m-d", $event_date->getNumericValue());
			$events[] = array($event_title, $actual_date);
		}
	}
	return $events;
}

function scfGetEvents_0_7($date_property) {
	$db = wfGetDB( DB_SLAVE );

	$events = array();
	$date_property = str_replace(' ', '_', $date_property);
	$sql = "SELECT subject_title, value_xsd FROM smw_attributes
		WHERE value_datatype = 'datetime'
		AND attribute_title = '$date_property'";
	$res = $db->query( $sql );
	while ($row = $db->fetchRow($res)) {
		$event_title = Title::newFromText($row[0]);
		$events[] = array($event_title, $row[1]);
	}
	$db->freeResult($res);
	return $events;
}
