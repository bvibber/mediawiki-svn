<?php
/**
 * Hooks for LogEntry extension
 *
 * @file
 * @ingroup Extensions
 */

// LogEntry hooks
class LogEntryHooks {

	/* Functions */
	
	// Initialization
	static function register() {
		global $wgParser;
		
		// Register the hook with the parser
		$wgParser->setHook( 'logentry', 'LogEntryHooks::render' );
		
		// Continue
		return true;
	}
	
	// Render the entry form
	static function render( $input, $args, &$parser ) {
		global $wgUser;
		
		// Don't cache since we are passing the token in the form
		$parser->disableCache();
		
		// Internationalization
		wfLoadExtensionMessages( 'LogEntry' );
		
		// Build HTML
		$htmlResult = Xml::openElement( 'form',
			array(
				'id' => 'logentryform',
				'name' => 'logentryform',
				'method' => 'post',
				'action' => SpecialPage::getTitleFor( 'LogEntry' )->escapeLocalURL(),
				'enctype' => 'multipart/form-data'
			)
		);
		$htmlResult .= Xml::element( 'input',
			array(
				'type' => 'text',
				'name' => 'line',
				'style' => 'width:80%;'
			)
		);
		$htmlResult .= Xml::element( 'input',
			array(
				'type' => 'submit',
				'name' => 'append',
				'value' => wfMsg( 'logentry-append' )
			)
		);
		$htmlResult .= Xml::element( 'input',
			array(
				'type' => 'hidden',
				'name' => 'page',
				'value' => $parser->getTitle()->getPrefixedText()
			)
		);
		$htmlResult .= Xml::element( 'input',
			array(
				'type' => 'hidden',
				'name' => 'token',
				'value' => $wgUser->editToken()
			)
		);
		$htmlResult .= Xml::closeElement( 'form' );
		
		// Return HTML output
		return $htmlResult;
	}
}
