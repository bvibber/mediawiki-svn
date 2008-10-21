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

		if ( !$wgUser->isAllowed( 'centralnotice_admin_rights' )) {
			$wgOut->permissionRequired( 'centralnotice_admin_rights' );
			return;
		}
		
		$wgOut->addWikiText( wfMsg( 'centralnotice-summary' ));
		CentralNotice::printHeader();

		if ( $wgRequest->wasPosted() ) {
			$body = file_get_contents('php://input');
			$wgOut->addHtml("Body of post: $body");
		        
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
					$this->updateMessage( $text, $translation, $lang, $token );
					}
				}

			}
		}

		$method = $wgRequest->getVal('method');

		if ( $sub == 'listTranslations' ) { // Show tranlsation text
			$this->showTranslateForm();
			return;
		}

  	  	$this->showTranslateForm();
	}

	function showTranslateForm() {
		global $wgOut,$wgUser,$wgRequest,$wgContLanguageCode;

		$token = $wgUser->editToken();
		$wpUserLang = $wgRequest->getVal('wpUserLanguage') ? $wgRequest->getVal('wpUserLanguage') : $wgContLanguageCode;
		$requiredFields = array( 'counter','donate','headlines');
		// Two column listing of : text : translation
		$table  = "<form name='centranoticetranslate' id='centralnoticetranslate' method='post'>";
		$table .= "<fieldset><legend>" . wfMsgHtml( "centralnotice-translate-heading" ) . "</legend>";
		$table .= "<table cellpadding=\"9\">";
		$table .= "<tr><th colspan =\"4\"></th></tr>";
		$table .= "<th>field</th>";
		$table .=  "<th>" . wfMsg ( 'centralnotice-english') . "</th>";
		$table .= "<th>field</th>"; 
		$table .= "<th>" . $wpUserLang . "</th></tr>";
		foreach( $requiredFields as $field) {
			$table .= "<tr><td>". "$field/en" . "</td>";
			$table .= "<td>" . wfMsgExt( "centralnotice-$field", array ( language => 'en') ) . "</td>";
			$table .= "<td>". "$field/$wpUserLang" . "</td>"; //translations start with english
			$table .= "<td>" . Xml::input( "updateText[$wpUserLang][$field]", 80, wfMsgExt( "centralnotice-$field", array ( language => $wpUserLang))) . "</td></tr>";
		}
		$table .= Xml::hidden( token, $token );
		$table .= Xml::hidden( wpUserLanguage, $wpUserLang ); //keep track of set language
		$table .= "<tr><td>" . Xml::submitButton( wfMsg('centralnotice-modify', array( name => 'update'))) . "</td>" . 
			      "<td>" . Xml::submitButton( wfMsg('centralnotice-preview-template'), array( name => 'preview')) . "</td></tr>";
		$table .= "</table></fieldset>";
		$table .= "</form>";

		$wgOut->addHTML( $table );
		
		$form = "<form name='centranoticetranslate' id='centralnoticetranslate' action=\"$action\" method='post'>";
		$form .= "<fieldset><legend>" . wfMsgHtml( "centralnotice-change-lang" ) . "</legend>";
		list( $sLabel, $lsSelect) = Xml::languageSelector( $wpUserLang );
		$form .= $this->tableRow( $lsLabel, $lsSelect) ;
		$form .= "<p><p>" . Xml::submitButton( wfMsgHtml('centralnotice-modify'));
		$form .= "</fieldset>";
		$form .= "</form>";
		$wgOut->addHTML( $form ) ;
	}

	function tableRow( $td1, $td2 ) {
		$td3 = '';
		$td1 = Xml::tags( 'td', array( 'class' => 'pref-label' ), $td1 );
		$td2 = Xml::tags( 'td', array( 'class' => 'pref-input' ), $td2 );
		return Xml::tags( 'tr', null, $td1 . $td2 ). $td3 . "\n";
	}
	function updateMessage( $text, $translation, $lang, $token ) {
		global $wgUser,$wgOut;
		
		$saveTo = "Centralnotice-" . $text;
		$saveTo .= "/$lang";

		$title = Title::newFromText( $saveTo, NS_MEDIAWIKI );
		$article = new Article( $title );	
		$article->doEdit( $translation, '' );
	}
}
