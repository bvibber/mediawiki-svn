<?php
/**
 * Additional input types for [http://www.mediawiki.org/wiki/Extension:SemanticForms Semantic Forms].
 *
 * @author Stephan Gambke
 * @author Sanyam Goyal
 * @version 0.3.1
  */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point.' );
}

if ( !defined( 'SF_VERSION' ) ) {
	die( 'This is a Semantic Forms extension. You need to install Semantic Forms first.' );
}

// create and initialize settings
$sfigSettings = new SFISettings();

// register extension
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Semantic Forms Inputs',
	'author' => array( '[http://www.mediawiki.org/wiki/User:F.trott Stephan Gambke]', 'Sanyam Goyal', 'Yaron Koren' ),
	'url' => 'http://www.mediawiki.org/wiki/Extension:Semantic_Forms_Inputs',
	'descriptionmsg' => 'semanticformsinputs-desc',
	'version' => '0.3.1',
);

// load user settings
require_once( 'SFI_Settings.php' );

$dir = dirname( __FILE__ );
$wgExtensionMessagesFiles['SemanticFormsInputs'] = $dir . '/SemanticFormsInputs.i18n.php';
$wgExtensionFunctions[] = "wfSFISetup";
$wgAutoloadClasses['SFIInputs'] = $dir . '/SFI_Inputs.php';

/*
 * Class to encapsulate all settings
 */
class SFISettings {
	// general settings
	public $scriptPath;
	public $yuiBase;

	// settings for input type datepicker
	public $datePickerFirstDate;
	public $datePickerLastDate;
	public $datePickerDateFormat;
	public $datePickerWeekStart;
	public $datePickerShowWeekNumbers;
	public $datePickerDisableInputField;
	public $datePickerShowResetButton;
	public $datePickerDisabledDaysOfWeek;
	public $datePickerHighlightedDaysOfWeek;
	public $datePickerDisabledDates;
	public $datePickerHighlightedDates;
	public $datePickerMonthNames;
	public $datePickerDayNames;
}

/*
 * Registers the input types with Semantic Forms.
 */
function wfSFISetup() {
	global $sfgFormPrinter, $wgOut;

	$sfgFormPrinter->setInputTypeHook( 'regexp', array( 'SFIInputs', 'regexpHTML' ), array() );
	$sfgFormPrinter->setInputTypeHook( 'datepicker', array( 'SFIInputs', 'jqDatePickerHTML' ), array() );
	$sfgFormPrinter->setInputTypeHook( 'simpledatepicker', array( 'SFIInputs', 'jqDatePickerHTML' ), array() );

	$wgOut->addInlineScript( 'sfiElements = new Object();' );
}
