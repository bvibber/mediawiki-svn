<?php
	
if ( !defined( 'MEDIAWIKI' ) ) {
        echo "CentralNotice extension\n";
        exit( 1 );
}

class SpecialNoticeTranslate extends SpecialPage {
	
	/* Functions */
	
	function __construct() {
		// Register the special page
		parent::__construct( 'NoticeTranslate' );
		
		// Internationalization
		wfLoadExtensionMessages( 'CentralNotice' );
	}
	
	function execute( $sub ) {
		global $wgOut, $wgUser, $wgRequest;
		
		// Begin output
		$this->setHeaders();
		
		// Check permissions
		if ( !$wgUser->isAllowed( 'centralnotice_translate_rights' )) {
			$wgOut->permissionRequired( 'centralnotice_translate_rights' );
			return;
		}
		
		// Show summary
		$wgOut->addWikiText( wfMsg( 'centralnotice-summary' ));
		
		// Show header
		CentralNotice::printHeader( $sub );
		
		// Handle form submissions
		if ( $wgRequest->wasPosted() ) {
		    // Handle preview
			$previewNotice = $wgRequest->getVal('preview');
			if ( isset( $previewNotice ) ) {
				//SpecialNoticeText::previewTemplate( $noticename, $updateText );
				return;
			}
			
			// Handle update
			$update = $wgRequest->getArray('updateText');
			$token  = $wgRequest->getArray('token');
			if (isset ( $update ) ) {
				foreach ( $update as $lang => $messages ) {
					foreach ( $messages as $text => $translation) {
						// If we actually have text
						if ( $translation ) {
							$this->updateMessage( $text, $translation, $lang, $token );
						}
					}
				}
			}
		}
		
		// Show tranlsation text
		if ( $sub == 'listTranslations' ) {
			$this->showTranslateForm();
			return;
		}
		
		// Show translation form
  	  	$this->showTranslateForm();
	}
	
	private function showTranslateForm() {
		global $wgOut, $wgUser, $wgRequest, $wgContLanguageCode;
		
		// Get token
		$token = $wgUser->editToken();
		
		// Get user's language
		$wpUserLang = $wgRequest->getVal('wpUserLanguage') ? $wgRequest->getVal('wpUserLanguage') : $wgContLanguageCode;
		
		// Build HTML
		$htmlOut = Xml::openElement( 'form', array( 'method' => 'post' ) );
		$htmlOut .= Xml::fieldset( wfMsgHtml( "centralnotice-translate-heading" ) );
		$htmlOut .= Xml::openElement( 'table', array ( 'cellpadding' => 9 ) );
		
		// Headers
		$htmlOut .= Xml::element( 'th' );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-english') );
		$htmlOut .= Xml::element( 'th' );
		$htmlOut .= Xml::element( 'th', null, $wpUserLang );
		
		// Rows
		$fields = array( 'heading', 'target', 'button', 'hide' );
		foreach( $fields as $field) {
			// Message
			$message = ( $wpUserLang == 'en' ) ? "Centralnotice-{$field}" : "Centralnotice-{$field}/{$wpUserLang}";
			
			// Text -- only load text if a message exists to avoild default english text display
			$title = Title::newFromText( $message, NS_MEDIAWIKI );
			$text = '';
			if( $title->exists() ) {
				$text = wfMsgExt( "centralnotice-{$field}", array ( 'language' => $wpUserLang ) );
			}
			
			// English value
			$htmlOut .= Xml::openElement( 'tr' );
			$htmlOut .= Xml::element( 'td', null, $field );
			$htmlOut .= Xml::element( 'td', null,
				wfMsgExt( "centralnotice-{$field}", array( 'language' => 'en') )
			);
			
			// Field
			$style = $text !== '' ? array( 'style' => 'color:red' ) : array();
			if ( $wpUserLang == 'en' ) {
				$htmlOut .= Xml::element( 'td', $style, $field );
			}
			else {
				$htmlOut .= Xml::element( 'td', $style, "{$field}/{$wpUserLang}" );
			}
			
			// Input
			$htmlOut .= Xml::tags( 'td', null,
				Xml::input( "updateText[$wpUserLang][$field]", 80, $text )
			);
			
			$htmlOut .= Xml::closeElement( 'tr' );
		}
		
		// Keep track of token
		$htmlOut .= Xml::hidden( 'token', $token );
		
		// Keep track of set language
		$htmlOut .= Xml::hidden( 'wpUserLanguage', $wpUserLang );
		
		// Submit and Preview
		$htmlOut .= Xml::openElement( 'tr' );
		$htmlOut .= Xml::tags( 'td', array( 'colspan' => 4 ),
			Xml::submitButton( wfMsg('centralnotice-modify', array( 'name' => 'update') ) ) .
			Xml::submitButton( wfMsg('centralnotice-preview-template'), array( 'name' => 'preview') )
		);
		
		$htmlOut .= Xml::closeElement( 'tr' );
		$htmlOut .= Xml::closeElement( 'table' );
		$htmlOut .= Xml::closeElement( 'fieldset' );
		$htmlOut .= Xml::closeElement( 'form' );
		
		// Language selection
		$htmlOut .= Xml::openElement( 'form', array( 'method' => 'post' ) );
		$htmlOut .= Xml::fieldset( wfMsgHtml( 'centralnotice-change-lang' ) );
		$htmlOut .= Xml::openElement( 'table', array ( 'cellpadding' => 9 ) );
		list( $lsLabel, $lsSelect) = Xml::languageSelector( $wpUserLang );
		$htmlOut .= Xml::tags( 'tr', null,
			Xml::tags( 'td', null, $lsLabel ) .
			Xml::tags( 'td', null, $lsSelect ) .
			Xml::tags( 'td', array( 'colspan' => 2 ),
				Xml::submitButton( wfMsgHtml('centralnotice-modify') )
			)
		);
		$htmlOut .= Xml::closeElement( 'table' );
		$htmlOut .= Xml::closeElement( 'fieldset' );
		$htmlOut .= Xml::closeElement( 'form' );
		
		// Output HTML
		$wgOut->addHTML( $htmlOut );
	}
	
	private function updateMessage( $text, $translation, $lang, $token ) {
		global $wgUser;
		
		$title = Title::newFromText(
			( $lang == 'en' ) ? "Centralnotice-{$text}" : "Centralnotice-{$text}/{$lang}",
			NS_MEDIAWIKI
		);
		$article = new Article( $title );	
		$article->doEdit( $translation, '' );
	}
}
