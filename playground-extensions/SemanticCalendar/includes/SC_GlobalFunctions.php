<?php
/**
 * Global functions and constants for Semantic Calendar
 *
 * @author Yaron Koren
 */

if (!defined('MEDIAWIKI')) die();

define('SC_VERSION','0.2.5');

// constants for special properties

$wgExtensionFunctions[] = 'scgSetupExtension';
$wgExtensionFunctions[] = 'scgParserFunctions';
$wgHooks['LanguageGetMagic'][] = 'scgLanguageGetMagic';

require_once($scgIP . '/includes/SC_ParserFunctions.php');
require_once($scgIP . '/includes/SC_HistoricalDate.php');
require_once($scgIP . '/languages/SC_Language.php');

if (version_compare($wgVersion, '1.11', '>=')) {
	$wgExtensionMessagesFiles['SemanticCalendar'] = $scgIP . '/languages/SC_Messages.php';
} else {
	$wgExtensionFunctions[] = 'scfLoadMessagesManually';
}

/**
 *  Do the actual intialization of the extension. This is just a delayed init that makes sure
 *  MediaWiki is set up properly before we add our stuff.
 */
function scgSetupExtension() {
	global $scgNamespace, $scgIP, $wgVersion, $wgExtensionCredits;

	if (version_compare($wgVersion, '1.11', '>='))
		wfLoadExtensionMessages('SemanticCalendar');

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

function scfGetEvents_1_0($date_property, $filter_query) {
	global $smwgIP;
	include_once($smwgIP . "/includes/SMW_QueryProcessor.php");
	$events = array();
	$query_string = "[[$date_property::*]][[$date_property::+]]$filter_query";
	// set a limit sufficiently close to infinity
	$params = array('limit' => 100000);
	$inline = false;
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
