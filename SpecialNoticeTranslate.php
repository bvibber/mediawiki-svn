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
		$this->printHeader();

	

		if ( $wgRequest->wasPosted() ) {
			$body = file_get_contents('php://input');
			$wgOut->addHtml("Body of post: $body");
		}

		$method = $wgRequest->getVal('method');
		$wgOut->addHtml("<p>got method $method<p>");
		$wgOut->addHtml("got sub $sub<p>");

		if ( $sub == 'listTranslations' ) { // Show tranlsation text
			$this->showTranslateForm();
			return;
		}

  	  	$this->showTranslateForm();
	}

	private function printHeader() {
		global $wgOut;
		$wgOut->addWikiText(   '[[' . 'Special:CentralNotice/listNotices ' . '|' . wfMsg( 'centralnotice-notices') . ']]' . " | "
				     . '[[' . 'Special:NoticeTemplate/listTemplates' . '|' . wfMsg ( 'centralnotice-templates' ) . ']]' . " | "
				     . '[[' . 'Special:NoticeTranslate/listTranslations' . '|' . wfMsg( 'centralnotice-translate') . ']]' . " | ");
	
	}

	function showTranslateForm() {
		global $wgOut;
		$translateTo = 'pl'; //hack till we can autodetect
		$requiredFields = array( 'counter','donate','headlines');
		// Two column listing of : text : translation
		$table  = "<form name='centranoticetranslate' id='centralnoticetranslate' action=\"$action\" method='post'>";
		$table .= "<fieldset><legend>" . wfMsgHtml( "centralnotice-translate-heading" ) . "</legend>";
		$table .= "<table cellpadding=\"9\">";
		$table .= "<tr><th colspan =\"4\"></th></tr>";
		$table .= "<th>source</th>";
		$table .=  "<th>" . wfMsg ( 'centralnotice-english') . "</th>";
		$table .= "<th>source</th>"; 
		$table .= "<th>" . "$translateTo" . "</th></tr>";
		foreach( $requiredFields as $field) {
			$table .= "<tr><td>". "$field" . "</td>";
			$table .= "<td>" . wfMsg( "centralnotice-$field" ) . "</td>";
			$table .= "<td>". "$field/$translateTo" . "</td>";
			$table .= "<td><input type=\"text\" name=\"centralnotice-$field/$translateTo\"" .
			  "value=\"" . wfMsgExt( "centralnotice-$field", array ( language => $translateTo) ) . "\" size=\"50\">". "</td></tr>";
		}
		$table .= "<td><input type='submit' value='Submit'></td>";
		$table .= "</tr></table></fieldset>";
		$table .= "</form>";

		$wgOut->addHTML( $table );
	}

# Private Functions

	####
 	# checkTranslateUpdate
	###
	# Check data source to see if a newer update has already been entered and warng accordingly.
	###

	private function checkTranslateUpdate() {
	 	global $wgOut, $wgUser, $wgRequest;

		$errors = array();
		
		$textToTranslate = $wgRequest->getArray('textToTranslate');
		foreach( $textToTranslate as $text ) {
			if ( $text == 'new' ) { // Somehow detect new
				$warning = "<b>$text</b>";
			}
		}
		$wgOut->addHTML("$warning");
	}

}
