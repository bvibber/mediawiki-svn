<?php

if ( !defined( 'MEDIAWIKI' ) ) {
        echo "CentralNotice extension\n";
        exit( 1 );
}

class SpecialNoticeTranslate extends UnlistedSpecialPage {
	
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
		
		// Show translation form
  	  	$this->showForm();
	}
	
	private function showForm() {
		global $wgOut, $wgUser, $wgRequest, $wgContLanguageCode;
		
		// Get token
		$token = $wgUser->editToken();
		
		// Get user's language
		$wpUserLang = $wgRequest->getVal('wpUserLanguage') ? $wgRequest->getVal('wpUserLanguage') : $wgContLanguageCode;
		
		// Get current template
		$currentTemplate = $wgRequest->getText( 'template' );
		
		// Show preview
		$htmlOut = SpecialNoticeTemplate::previewTemplate(
			$wgRequest->getText( 'template' ),
			$wgRequest->getVal( 'wpUserLanguage' ) 
		);
		
		// Build HTML
		$htmlOut .= Xml::openElement( 'form', array( 'method' => 'post' ) );
		$htmlOut .= Xml::fieldset( wfMsgHtml( 'centralnotice-translate-heading', $currentTemplate ) );
		$htmlOut .= Xml::openElement( 'table',
			array (
				'cellpadding' => 9,
				'width' => '100%'
			)
		);
		
		// Headers
		$htmlOut .= Xml::element( 'th', array( 'width' => '15%' ), wfMsg( 'centralnotice-message' ) );
		$htmlOut .= Xml::element( 'th', array( 'width' => '5%' ), wfMsg ( 'centralnotice-number-uses')  );
		$htmlOut .= Xml::element( 'th', array( 'width' => '40%' ), wfMsg ( 'centralnotice-english') );
		$languages = Language::getLanguageNames();
		$htmlOut .= Xml::element( 'th', array( 'width' => '40%' ), $languages[$wpUserLang] );
		
		// Generate fields from parsing the body
		$fields = array();
		preg_match_all( '/\{\{\{([A-Za-z0-9\_\-}]+)\}\}\}/', wfMsg( "Centralnotice-template-{$currentTemplate}" ), $fields );
		
		// Remove duplicates
		$filteredFields = array();
		foreach( $fields[1] as $field ) {
			$filteredFields[$field] = array_key_exists( $field, $filteredFields ) ? $filteredFields[$field] + 1 : 1;
		}
		
		// Rows
		foreach( $filteredFields as $field => $count ) {
			// Message
			$message = ( $wpUserLang == 'en' ) ? "Centralnotice-{$currentTemplate}-{$field}" : "Centralnotice-{$currentTemplate}-{$field}/{$wpUserLang}";
			
			// English value
			$htmlOut .= Xml::openElement( 'tr' );
			
			$title = Title::newFromText( "MediaWiki:{$message}" );
			$htmlOut .= Xml::tags( 'td', null,
				Xml::element( 'a', array( 'href' => $title->getFullURL() ), $field )
			);
			
			$htmlOut .= Xml::element( 'td', null, $count);
			
			$htmlOut .= Xml::element( 'td', null,
				wfMsgExt( "Centralnotice-{$currentTemplate}-{$field}", array( 'language' => 'en') )
			);
			
			// Input
			$text = '';
			if( Title::newFromText( $message, NS_MEDIAWIKI )->exists() ) {
				$text = wfMsgExt( "Centralnotice-{$currentTemplate}-{$field}",
					array( 'language' => $wpUserLang )
				);
			}
			$htmlOut .= Xml::tags( 'td', null,
				Xml::input( "updateText[{$wpUserLang}][{$currentTemplate}-{$field}]", '', $text,
					array( 'style' => 'width:100%;' . ( $text == '' ? 'color:red' : '' ) )
				)
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
			Xml::submitButton( wfMsg('centralnotice-modify', array( 'name' => 'update') ) )
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
