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
	// display nothing
	if ($frame->title->getFullText() != $parser->getTitle()->getFullText())
		return;

        // also display nothing if there are no contents
	if (empty($input)) {
		return;
	}

        // Return output
	global $egTemplateInfoXML;
	$egTemplateInfoXML = $input;
        $templateInfo = new TemplateInfo( $parser );
        return $templateInfo->render($input);
    }
}
