<?php
/**
 * Special:PrefSwitch
 *
 * @file
 * @ingroup Extensions
 */

class SpecialSimpleSurvey extends SpecialPage {

	/* Private Members */
	
	private $origin = '';
	private $originTitle = null;
	private $originQuery = '';
	private $originLink = '';
	private $originLinkUrl = '';
	private $originFullUrl = '';

	/* Functions */
	
	public function __construct() {
		parent::__construct( 'SimpleSurvey' );
		wfLoadExtensionMessages( 'SimpleSurvey' );
	}
	
	
	public function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser, $wgPrefSwitchSurveys, $wgPrefSwitchStyleVersion, $wgValidSurveys;
		$this->setHeaders();
		// Set page title
		$wgOut->setPageTitle( wfMsg( 'simple-survey-title' )  );
		$surveyName = $wgRequest->getVal("survey");
		
		if($wgRequest->wasPosted()){
				if($surveyName && in_array($surveyName,$wgValidSurveys )){
					SimpleSurvey::save( $surveyName, $wgPrefSwitchSurveys[$surveyName] );
					$wgOut->addHtml("<b>" . wfMsg( 'simple-survey-confirm' ). "</b>");
				}
					//forward to new page
				return;
		}
		
		// Get the origin from the request
		$par = $wgRequest->getVal( 'from', $par );
		$this->originTitle = Title::newFromText( $par );
		// $this->originTitle should never be Special:Userlogout
		if (
			$this->originTitle &&
			$this->originTitle->getNamespace() == NS_SPECIAL &&
			SpecialPage::resolveAlias( $this->originTitle->getText() ) == 'Userlogout'
		) {
			$this->originTitle = null;
		}
		// Get some other useful information about the origin
		if ( $this->originTitle ) {
			$this->origin = $this->originTitle->getPrefixedDBKey();
			$this->originQuery = $wgRequest->getVal( 'fromquery' );
			$this->originLink = $wgUser->getSkin()->link( $this->originTitle, null, array(), $this->originQuery );
			$this->originLinkUrl = $this->originTitle->getLinkUrl( $this->originQuery );
			$this->originFullUrl = $this->originTitle->getFullUrl( $this->originQuery );
		}
		
		// Begin output
		$this->setHeaders();
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addScript( 'PrefSwitch/PrefSwitch.js', $wgPrefSwitchStyleVersion );
		UsabilityInitiativeHooks::addStyle( 'PrefSwitch/PrefSwitch.css', $wgPrefSwitchStyleVersion );
		$wgOut->addHtml( '<div class="plainlinks">' );
		// Handle various modes
		
		$this->render( $wgRequest->getVal("survey") );
		
		
		$wgOut->addHtml( '</div>' );
	}
	
	/* Private Functions */
	
	private function render( $mode = null ) {
		global $wgUser, $wgOut, $wgPrefSwitchSurveys, $wgValidSurveys;
		// Make sure links will retain the origin
		$query = array(	'from' => $this->origin, 'fromquery' => $this->originQuery );
		if ( isset( $wgPrefSwitchSurveys[$mode] )  && in_array($mode, $wgValidSurveys) ){
			$wgOut->addWikiMsg( "simple-survey-intro-{$mode}" );
			
			// Setup a form
			$html = Xml::openElement(
				'form', array(
					'method' => 'post',
					'action' => $this->getTitle()->getLinkURL( $query ),
					'class' => 'simple-survey',
					'id' => "simple-survey-{$mode}"
				)
			);
			$html .= Xml::hidden( 'survey', $mode );
			// Render a survey
			$html .= SimpleSurvey::render(
				$wgPrefSwitchSurveys[$mode]['questions']
			);
			// Finish out the form
			$html .= Xml::openElement( 'dt', array( 'class' => 'prefswitch-survey-submit' ) );
			$html .= Xml::submitButton(
				wfMsg( $wgPrefSwitchSurveys[$mode]['submit-msg'] ),
				array( 'id' => "simple-survey-submit-{$mode}", 'class' => 'prefswitch-survey-submit' )
			);
			$html .= Xml::closeElement( 'dt' );
			$html .= Xml::closeElement( 'form' );
			$wgOut->addHtml( $html );
		}
		else{
			$wgOut->addWikiMsg( "simple-survey-invalid" );
			if ( $this->originTitle ) {	
				$wgOut->addHTML( wfMsg( "simple-survey-back", $this->originLink ) );
			}
		}
		
	}
}
