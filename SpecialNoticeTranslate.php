<?php
	
if ( !defined( 'MEDIAWIKI' ) ) {
        echo "CentralNotice extension\n";
        exit( 1 );
}

class SpecialNoticeTranslate extends SpecialPage 
{ 

        function __construct() {
	                        parent::__construct( "NoticeTranslate" );
				wfLoadExtensionMessages('CentralNotice');
	}
	
	function execute( $sub ) {
		global $wgOut, $wgUser, $wgRequest;
		
		$this->setHeaders();
		$sk = $wgUser->getSkin();

		if ( !$wgUser->isAllowed( 'centralnotice_translate_rights' )) {
			$wgOut->permissionRequired( 'centralnotice_translate_rights' );
			return;
		}
		
		$wgOut->addWikiText( wfMsg( 'centralnotice-summary' ));
		CentralNotice::printHeader();

		if ( $wgRequest->wasPosted() ) {
			//$body = file_get_contents('php://input');
			//$wgOut->addHtml("Body of post: $body");
		        
			$previewNotice = $wgRequest->getVal('preview');
			if ( isset( $previewNotice ) ) {
				//SpecialNoticeText::previewTemplate( $noticename, $updateText );
				return;
			}
		        $update = $wgRequest->getArray('updateText');
        		$token  = $wgRequest->getArray('token');
			if (isset ( $update ) ) {
				foreach ( $update as $lang => $messages ) {
					foreach ( $messages as $text => $translation) { 
					if ( $translation ) // if we actually got text
						$this->updateMessage( $text, $translation, $lang, $token );
					}
				}

			}
		}

		if ( $sub == 'listTranslations' ) { // Show tranlsation text
			$this->showTranslateForm();
			return;
		}

  	  	$this->showTranslateForm();
	}

	private function showTranslateForm() {
		global $wgOut,$wgUser,$wgRequest,$wgContLanguageCode;

		$token = $wgUser->editToken();
		$wpUserLang = $wgRequest->getVal('wpUserLanguage') ? $wgRequest->getVal('wpUserLanguage') : $wgContLanguageCode;
		$requiredFields = array( 'heading','target','button','hide');
		// Four column listing of field : text : field/lang : translation
		$table  = "<form name='centranoticetranslate' id='centralnoticetranslate' method='post'>";
		$table .= "<fieldset><legend>" . wfMsgHtml( "centralnotice-translate-heading" ) . "</legend>";
		$table .= "<table cellpadding=\"9\">";
		$table .= "<tr><th colspan =\"4\"></th></tr>";
		$table .= "<th>field</th>";
		$table .=  "<th>" . wfMsg ( 'centralnotice-english') . "</th>";
		$table .= "<th>field</th>"; 
		$table .= "<th>" . $wpUserLang . "</th></tr>";
		foreach( $requiredFields as $field) {
			$message = ( $wpUserLang == 'en' ) ? "Centralnotice-" . "$field" : "Centralnotice-" . "$field" . "/" . $wpUserLang;
			$title = Title::newFromText( $message, NS_MEDIAWIKI );
			$text = $title->exists() ? wfMsgExt( "centralnotice-$field", array ( 'language' => $wpUserLang ) ) : '';  // only load text if a message exists to avoild default english text display
			$table .= "<tr><td>". "$field" . "</td>";
			$table .= "<td>" . wfMsgExt( "centralnotice-$field", array ( 'language' => 'en') ) . "</td>";
			if ( $text ) {
				if ( $wpUserLang == 'en' ) {
					$table .= "<td>" . "$field" . "</td>";
				}
				else {
					$table .= "<td>". "$field/$wpUserLang" . "</td>";
				}
			}
			else {
				if ( $wpUserLang == 'en' ) {
					$table .= "<td>" . "<font color=\"red\">" . "$field" . "</td>";
				}
				else {
					$table .= "<td>" . "<font color=\"red\">" . "$field/$wpUserLang" . "</td>";
				}
			}  		
			$table .= "<td>" . Xml::input( "updateText[$wpUserLang][$field]", 80, $text) . "</td></tr>";
		}
		$table .= Xml::hidden( 'token', $token );
		$table .= Xml::hidden( 'wpUserLanguage', $wpUserLang ); //keep track of set language
		$table .= "<tr><td>" . Xml::submitButton( wfMsg('centralnotice-modify', array( 'name' => 'update'))) . "</td>" . 
			      "<td>" . Xml::submitButton( wfMsg('centralnotice-preview-template'), array( 'name' => 'preview')) . "</td></tr>";
		$table .= "</table></fieldset>";
		$table .= "</form>";

		$wgOut->addHTML( $table );
		
		$form = "<form name='translatelang' id='translatelang' method='post'>";
		$form .= "<fieldset><legend>" . wfMsgHtml( "centralnotice-change-lang" ) . "</legend>";
		list( $lsLabel, $lsSelect) = Xml::languageSelector( $wpUserLang );
		$form .= $this->tableRow( $lsLabel, $lsSelect) ;
		$form .= "<p><p>" . Xml::submitButton( wfMsgHtml('centralnotice-modify'));
		$form .= "</fieldset>";
		$form .= "</form>";
		$wgOut->addHTML( $form ) ;
	}

	public function tableRow( $td1, $td2 ) {
		$td3 = '';
		$td1 = Xml::tags( 'td', array( 'class' => 'pref-label' ), $td1 );
		$td2 = Xml::tags( 'td', array( 'class' => 'pref-input' ), $td2 );
		return Xml::tags( 'tr', null, $td1 . $td2 ). $td3 . "\n";
	}

	private function updateMessage( $text, $translation, $lang, $token ) {
		global $wgUser;

		$saveTo = ( $lang == 'en' ) ? "Centralnotice-" . $text : "Centralnotice-" . $text . "/$lang";
		
		$title = Title::newFromText( $saveTo, NS_MEDIAWIKI );
		$article = new Article( $title );	
		$article->doEdit( $translation, '' );
	}
}
