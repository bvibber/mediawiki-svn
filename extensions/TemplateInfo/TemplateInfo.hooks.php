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

    // Render the displayed XML, if any
    public static function render( $input, $args, $parser, $frame ) {
	// if this call is contained in a transcluded page or template,
	// or if the inpur is empty, display nothing
	if ( !$frame->title->equals( $parser->getTitle() ) || $input == '' )
		return;
	
	// Store XML in the page_props table
	// TODO: Do processing here, like parse to an array
	$parser->getOutput()->setProperty( 'templateinfo', $input );

	// Return output
	$text = "<p>" . wfMsg( 'templateinfo-header' ) . "</p>\n";
	$text .= htmlspecialchars( $input, ENT_QUOTES );
	return $text;
    }
}
