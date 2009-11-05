<?php
/**
 * Hooks for TemplateInfo extension
 *
 * @file
 * @ingroup Extensions
 */

class TemplateInfoHooks {

    /* Functions */

    // Initialization
    public static function register( &$parser ) {
	// Register the hook with the parser
	$parser->setHook( 'templateinfo', array( 'TemplateInfoHooks', 'render' ) );

	// Continue
	return true;
    }

    public static function validateXML( $xml ) {
	$xmlDTD =<<<END
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE template [
<!ELEMENT template (description?,params?,data*)>
<!ELEMENT params (param|group)*>
<!ELEMENT param (label?,description?,options?,type?,data*)>
<!ATTLIST param id ID #REQUIRED>
<!ELEMENT group (label?,description?,param*,data*)>
<!ELEMENT label (#PCDATA|msg)*>
<!ELEMENT description (#PCDATA|msg)*>
<!ELEMENT options (option*)>
<!ELEMENT option (#PCDATA|msg)*>
<!ELEMENT type (field*)>
<!ATTLIST type name CDATA #REQUIRED>
<!ELEMENT field EMPTY>
<!ATTLIST field name CDATA #REQUIRED>
<!ATTLIST field value CDATA #REQUIRED>
<!ELEMENT msg (#PCDATA)>
<!ATTLIST msg lang CDATA #REQUIRED>
<!ELEMENT data (field*)>
<!ATTLIST data app CDATA #REQUIRED>
]>

END;
	// we are using the SimpleXML library to do the XML validation
	// for now - this may change later
	// hide parsing warnings
	libxml_use_internal_errors(true);
	$xml_success = simplexml_load_string($xmlDTD . $xml);
	return $xml_success;
    }

    // Render the displayed XML, if any
    public static function render( $input, $args, $parser, $frame ) {
	// if this call is contained in a transcluded page or template,
	// or if the inpur is empty, display nothing
	if ( !$frame->title->equals( $parser->getTitle() ) || $input == '' )
		return;
	
	// Store XML in the page_props table
	// TODO: Do processing here, like parse to an array
 	if ( TemplateInfoHooks::validateXML( $input ) )
		$parser->getOutput()->setProperty( 'templateinfo', $input );
	else
		$parser->getOutput()->setProperty( 'templateinfo', "Error: Invalid XML" );

	// Return output
	$text = "<p>" . wfMsg( 'templateinfo-header' ) . "</p>\n";
	$text .= htmlspecialchars( $input, ENT_QUOTES );
	return $text;
    }
}
