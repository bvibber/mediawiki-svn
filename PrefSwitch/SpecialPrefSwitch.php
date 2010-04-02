<?php
/**
 * Special:PrefSwitch
 *
 * @file
 * @ingroup Extensions
 */

class SpecialPrefSwitch extends SpecialPage {

	/* Private Members */
	
	private $origin = '';
	private $originTitle = null;
	private $originQuery = '';
	private $originLink = '';
	private $originUrl = '';

	/* Static Functions */

	/**
	 * Quick token matching wrapper for form processing
	 */
	public static function checkToken() {
		global $wgRequest, $wgUser;
		return $wgUser->matchEditToken( $wgRequest->getVal( 'token' ) );
	}
	/**
	 * Checks if a user's preferences are switched on
	 * 
	 * @param $user User object to check switched state for
	 * @return switched state
	 */
	public static function isSwitchedOn( $user ) {
		global $wgPrefSwitchPrefs;
		// Impossible to be switched on if not logged in
		if ( $user->isAnon() ) {
			return false;
		}
		// Swtiched on means any of the preferences in the set are turned on
		foreach ( $wgPrefSwitchPrefs['on'] as $pref => $value ) {
			if ( $pref != 'skin' && $user->getOption( $pref ) == $value ) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Switches a user's prefernces on
	 * @param $user User object to set preferences for
	 */
	public static function switchOn( $user ) {
		global $wgPrefSwitchPrefs;		
		foreach ( $wgPrefSwitchPrefs['on'] as $pref => $value ) {
			$user->setOption( $pref, $value );
		}
		$user->saveSettings();
	}
	/**
	 * Switches a user's prefernces off
	 * @param $user User object to set preferences for
	 */
	public static function switchOff( $user ) {
		global $wgPrefSwitchPrefs;
		foreach ( $wgPrefSwitchPrefs['off'] as $pref => $value ) {
			$user->setOption( $pref, $value );
		}
		$user->saveSettings();
	}

	/* Functions */
	
	public function __construct() {
		parent::__construct( 'PrefSwitch' );
		wfLoadExtensionMessages( 'PrefSwitch' );
	}
	public function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser, $wgPrefSwitchSurvey, $wgPrefSwitchStyleVersion;
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
			$this->originUrl = $this->originTitle->getLinkUrl( $this->originQuery );
		}
		// Begin output
		$this->setHeaders();
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addScript( 'PrefSwitch/PrefSwitch.js', $wgPrefSwitchStyleVersion );
		UsabilityInitiativeHooks::addStyle( 'PrefSwitch/PrefSwitch.css', $wgPrefSwitchStyleVersion );
		/*
		// Set page title
		if ( self::isSwitchedOn( $wgUser ) ) {
			switch ( $wgRequest->getVal( 'mode' ) ) {
				case 'off':
					// Just switched off
					$wgOut->setPageTitle( wfMsg( 'prefswitch-title-switched-off' ) );
					break;
				case 'feedback':
					// Giving feedback
					$wgOut->setPageTitle( wfMsg( 'prefswitch-title-feedback' ) );
					break;
				case 'on':
					// Just switched on, and reloaded... or something
					$wgOut->setPageTitle( wfMsg( 'prefswitch-title-switched-on' ) );
					break;
				default:
					// About to switch off
					$wgOut->setPageTitle( wfMsg( 'prefswitch-title-on' ) );
					break;
			}
		} else {
			switch ( $wgRequest->getVal( 'mode' ) ) {
				case 'on':
					// Just switched on
					$wgOut->setPageTitle( wfMsg( 'prefswitch-title-switched-on' ) );
					break;
				default:
					// About to switch on
					$wgOut->setPageTitle( wfMsg( 'prefswitch-title-off' ) );
					break;
			}
		}
		*/
		// Handle various modes
		if ( $wgRequest->getCheck( 'mode' ) && $wgUser->isLoggedIn() ) {
			switch ( $wgRequest->getVal( 'mode' ) ) {
				case 'on':
					// Switch on
					if ( self::checkToken() && !self::isSwitchedOn( $wgUser ) ) {
						self::switchOn( $wgUser );
						$wgOut->addWikiMsg( 'prefswitch-success-on' );
					} else {
						$this->render( 'main' );
					}
					break;
				case 'off':
					// Switch off
					if ( self::checkToken() && self::isSwitchedOn( $wgUser ) && $wgRequest->wasPosted() ) {
						self::switchOff( $wgUser );
						PrefSwitchSurvey::save( 'feedback', $wgPrefSwitchSurveys['feedback'] );
						$wgOut->addWikiMsg( 'prefswitch-success-off' );
					} else if ( self::isSwitchedOn( $wgUser ) ) {
						// User is already switched off then reloaded the page or tried to switch off again
						$wgOut->addWikiMsg( 'prefswitch-success-off' );
					} else {
						$this->render( 'off' );
					}
					break;
				case 'feedback':
					if ( self::checkToken() && self::isSwitchedOn( $wgUser ) && $wgRequest->wasPosted() ) {
						PrefSwitchSurvey::save( 'feedback', $wgPrefSwitchSurveys['feedback'] );
						$wgOut->addWikiMsg( 'prefswitch-success-feedback' );
					} else {
						$this->render( 'feedback' );
					}
					break;
				default:
					$this->render( 'main' );
					break;
			}
		} else {
			$this->render( 'main' );
		}
	}
	
	/* Private Functions */
	
	private function render( $mode = null ) {
		global $wgUser, $wgOut, $wgPrefSwitchSurveys;
		// Make sure links will retain the origin
		$query = array(	'from' => $this->origin, 'fromquery' => $this->originQuery );
		if ( isset( $wgPrefSwitchSurveys[$mode] ) ) {
			$wgOut->addWikiMsg( "prefswitch-survey-intro-{$mode}" );
			// Provide a "nevermind" link
			if ( $this->originTitle ) {
				$wgOut->addHTML( wfMsg( "prefswitch-survey-cancel-{$mode}", $this->originLink ) );
			}
			// Setup a form
			$html = Xml::openElement(
				'form', array(
					'method' => 'post',
					'action' => $this->getTitle()->getLinkURL( $query ),
					'class' => 'prefswitch-survey',
					'id' => 'prefswitch-survey-{$name}',
				)
			);
			$html .= Xml::hidden( 'mode', $mode );
			$html .= Xml::hidden( 'token', $wgUser->editToken() );
			// Render a survey
			$html .= PrefSwitchSurvey::render(
				$mode, $wgPrefSwitchSurveys[$mode]['questions'], $wgPrefSwitchSurveys[$mode]['updatable']
			);
			// Finish out the form
			$html .= Xml::openElement( 'dt', array( 'class' => 'prefswitch-survey-submit' ) );
			$html .= Xml::submitButton(
				wfMsg( $wgPrefSwitchSurveys[$mode]['submit-msg'] ),
				array( 'id' => "prefswitch-survey-submit-{$mode}", 'class' => 'prefswitch-survey-submit' )
			);
			$html .= Xml::closeElement( 'dt' );
			$html .= Xml::closeElement( 'form' );
			$wgOut->addHtml( $html );
		} else {
			$wgOut->addWikiMsgArray(
				'prefswitch-main', array( $this->originUrl, $this->originTitle ), array( 'parse' )
			);
			if ( self::isSwitchedOn( $wgUser ) ) {
				$wgOut->addWikiMsgArray(
					'prefswitch-main-on',
					array(
						$this->getTitle()->getLinkURL( array_merge( $query, array( 'mode' => 'off' ) ) ),
						$this->getTitle()->getLinkURL( array_merge( $query, array( 'mode' => 'feedback' ) ) )
					),
					array( 'parse' )
				);
			}
		}
	}
}
