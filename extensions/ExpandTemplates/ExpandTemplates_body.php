<?php

# Add messages
wfLoadExtensionMessages( 'ExpandTemplates' );

class ExpandTemplates extends SpecialPage {

	/* 50MB allows fixing those huge pages */
	const MAX_INCLUDE_SIZE = 50000000;

	function __construct() {
		parent::__construct( 'ExpandTemplates' );
	}

	function execute( $subpage ) {
		global $wgRequest, $wgParser, $wgOut;

		$this->setHeaders();

		$titleStr = $wgRequest->getText( 'contexttitle' );
		$title = Title::newFromText( $titleStr );
		$selfTitle = $this->getTitle();
		if ( !$title ) {
			$title = $selfTitle;
		}
		$input = $wgRequest->getText( 'input' );
		$generateXML = $wgRequest->getBool( 'generate_xml' );
		if ( strlen( $input ) ) {
			$removeComments = $wgRequest->getBool( 'removecomments', false );
			$options = new ParserOptions;
			$options->setRemoveComments( $removeComments );
			$options->setMaxIncludeSize( self::MAX_INCLUDE_SIZE );
			if ( $generateXML ) {
				$wgParser->startExternalParse( $title, $options, OT_PREPROCESS );
				$dom = $wgParser->preprocessToDom( $input );
				$xml = $dom->saveXML();
			}
			$output = $wgParser->preprocess( $input, $title, $options );
		} else {
			$removeComments = $wgRequest->getBool( 'removecomments', true );
			$output = false;
		}

		$wgOut->addWikiText( wfMsg( 'expand_templates_intro' ) );
		$wgOut->addHtml( $this->makeForm( $titleStr, $removeComments, $input, $generateXML ) );

		if( $output !== false ) {
			if ( $generateXML ) {
				$wgOut->addHtml( $this->makeOutput( $xml, 'expand_templates_xml_output' ) );
			}
			$wgOut->addHtml( $this->makeOutput( $output ) );
			$this->showHtmlPreview( $title, $output, $wgOut );
		}

	}

	/**
	 * Generate a form allowing users to enter information
	 *
	 * @param $title Value for context title field
	 * @param $removeComments Toggle the remove comments box checked/unchecked
	 * @param $input Value for input textbox
	 * @return string
	 */
	private function makeForm( $title, $removeComments, $input, $generateXML ) {
		$self = $this->getTitle();
		$form  = Xml::openElement( 'form', array( 'method' => 'post', 'action' => $self->getLocalUrl() ) );
		$form .= "<fieldset><legend>" . wfMsgHtml( 'expandtemplates' ) . "</legend>\n";
		$form .= '<p>' . Xml::inputLabel( wfMsgNoTrans( 'expand_templates_title' ), 'contexttitle', 'contexttitle', 60, $title ) . '</p>';
		$form .= '<p>' . Xml::label( wfMsg( 'expand_templates_input' ), 'input' ) . '</p>';
		$form .= Xml::openElement( 'textarea', array( 'name' => 'input', 'id' => 'input', 'rows' => 10, 'cols' => 10 ) );
		$form .= htmlspecialchars( $input );
		$form .= Xml::closeElement( 'textarea' );
		$form .= '<p>' . Xml::checkLabel( wfMsg( 'expand_templates_remove_comments' ), 'removecomments', 'removecomments', $removeComments ) . '</p>';
		$form .= '<p>' . Xml::checkLabel( wfMsg( 'expand_templates_generate_xml' ), 'generate_xml', 'generate_xml', $generateXML ) . '</p>';
		$form .= '<p>' . Xml::submitButton( wfMsg( 'expand_templates_ok' ) ) . '</p>';
		$form .= "</fieldset>\n";
		$form .= Xml::closeElement( 'form' );
		return $form;
	}

	/**
	 * Generate a nice little box with a heading for output
	 *
	 * @param $output Wiki text output
	 * @return string
	 */
	private function makeOutput( $output, $heading = 'expand_templates_output' ) {
		$out  = "<h2>" . wfMsgHtml( $heading ) . "</h2>\n";
		$out .= Xml::openElement( 'textarea', array( 'id' => 'output', 'rows' => 10, 'cols' => 10, 'readonly' => 'readonly' ) );
		$out .= htmlspecialchars( $output );
		$out .= Xml::closeElement( 'textarea' );
		return $out;
	}

	/**
	 * Render the supplied wiki text and append to the page as a preview
	 *
	 * @param Title $title
	 * @param string $text
	 * @param OutputPage $out
	 */
	private function showHtmlPreview( $title, $text, $out ) {
		global $wgParser;
		$pout = $wgParser->parse( $text, $title, new ParserOptions() );
		$out->addHtml( "<h2>" . wfMsgHtml( 'expand_templates_preview' ) . "</h2>\n" );
		$out->addHtml( $pout->getText() );
	}

}
