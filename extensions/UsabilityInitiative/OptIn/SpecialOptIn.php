<?php
/**
 * Special:OptIn
 *
 * @file
 * @ingroup Extensions
 */

class SpecialOptIn extends SpecialPage {
	
	/* Members */
	
	private $mOrigin = '';
	private $mOriginTitle = null;
	
	/* Static Functions */
	
	public static function isOptedIn( $user ) {
		global $wgOptInPrefs;
		
		foreach ( $wgOptInPrefs as $pref => $value ) {
			if ( $user->getOption( $pref ) != $value ) {
				return false;
			}
		}
		return true;
	}

	public static function optIn( $user ) {
		global $wgOptInPrefs;
		
		foreach ( $wgOptInPrefs as $pref => $value ) {
			$user->setOption( $pref, $value );
		}
		$user->saveSettings();
	}

	public static function optOut( $user ) {
		global $wgOptInPrefs;
		
		foreach ( $wgOptInPrefs as $pref => $value ) {
			$user->setOption( $pref, null );
		}
		$user->saveSettings();
	}
	
	/* Functions */
	
	public function __construct() {
		parent::__construct( 'OptIn' );
		wfLoadExtensionMessages( 'OptIn' );
	}

	public function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser;
		
		$this->mOriginTitle = Title::newFromText( $par );
		if ( $this->mOriginTitle )
			$this->mOrigin = $this->mOriginTitle->getPrefixedText();
		$this->setHeaders();
		
		if ( self::isOptedIn( $wgUser ) )
			$wgOut->setPageTitle( wfMsg( 'optin-title-optedin' ) );
		else
			$wgOut->setPageTitle( wfMsg( 'optin-title-optedout' ) );

		if ( $wgUser->isAnon() ) {
			$url = SpecialPage::getTitleFor( 'Userlogin' )->getFullURL(
				array( 'returnto' => $this->getTitle( $par )->getPrefixedUrl() ) );
			$wgOut->wrapWikiMsg( "<div class='plainlinks'>\n$1\n</div>", array( 'optin-needlogin', $url ) );
			return;
		}

		if ( $wgRequest->getCheck( 'opt' ) ) {
			if ( $wgRequest->getVal( 'opt' ) === 'in' ) {
				self::optIn( $wgUser );
				$wgOut->addWikiMsg( 'optin-success-in' );
				if ( $this->mOriginTitle )
					$wgOut->addWikiMsg( 'optin-success-in-return',
						$this->mOriginTitle->getPrefixedText() );
			} else {
				self::optOut( $wgUser );
				$this->saveSurvey();
				$wgOut->addWikiMsg( 'optin-success-out' );
				$this->showForm();
			}
		}
		else
			$this->showForm();
	}
	
	/* Private Functions */

	private function showForm() {
		global $wgUser, $wgOut;
		
		$wgOut->addHTML( Xml::openElement( 'form', array(
			'method' => 'post',
			'action' => $this->getTitle()->getLinkURL(),
			'class' => 'optin-survey',
		) ) );
		$opt = ( self::isOptedIn( $wgUser ) ? 'out' : 'in' );
		if ( $opt == 'out' ) {
			$wgOut->addWikiMsg( 'optin-survey-intro' );
			$this->showSurvey();
		}
		else
		{
			$wgOut->addHTML( Xml::tags( 'div', array( 'class' => 'optin-intro' ),
				wfMsg( 'optin-intro' ) ) );
			$this->showOptInButtons();
			$wgOut->addWikiMsg( 'optin-improvements' );
		}
		$wgOut->addHTML( Xml::hidden( 'opt', $opt ) );
		$wgOut->addHTML( Xml::closeElement( 'form' ) );
	}
	
	function showOptInButtons() {
		global $wgOut, $wgOptInStyleVersion;
		
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addStyle( 'OptIn/OptIn.css',
				$wgOptInStyleVersion );
		$wgOut->addHTML(
			Xml::tags( 'div', array( 'class' => 'optin-accept' ),
				Xml::tags( 'div', array(),
				Xml::tags( 'div', array(),
				Xml::tags( 'div', array(),
				Xml::tags( 'div', array(),
					Xml::tags( 'a', array( 'href' => $this->getTitle( $this->mOrigin )->getFullURL( 'opt=in' ) ),
						Xml::element( 'span',
							array( 'class' => 'optin-button-shorttext' ),
							wfMsg( 'optin-accept-short' )
						) .
						Xml::element( 'br' ) .
						Xml::element( 'span',
							array( 'class' => 'optin-button-longtext' ),
							wfMsg( 'optin-accept-long' )
						)
					)
				) ) ) )
			) .
			Xml::tags( 'div', array( 'class' => 'optin-deny' ),
				Xml::tags( 'div', array(),
				Xml::tags( 'div', array(),
				Xml::tags( 'div', array(),
				Xml::tags( 'div', array(),
					Xml::tags( 'a', array(), // TODO: target
						Xml::element( 'span',
							array( 'class' => 'optin-button-shorttext' ),
							wfMsg( 'optin-deny-short' )
						) .
						Xml::element( 'br' ) .
						Xml::element( 'span',
							array( 'class' => 'optin-button-longtext' ),
							wfMsg( 'optin-deny-long' )
						)
					)
				) ) ) )
			) .
			Xml::element( 'div', array( 'style' => 'clear: both; ' ), '', false )
		);
	}

	private function showSurvey() {
		global $wgOptInSurvey, $wgOut, $wgOptInStyleVersion;
		
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addScript( 'OptIn/OptIn.js',
			$wgOptInStyleVersion );
		$retval = Xml::openElement( 'table' );
		foreach ( $wgOptInSurvey as $id => $question ) {
			switch ( $question['type'] ) {
			case 'dropdown':
				$retval .= Xml::openElement( 'tr' );
				$retval .= Xml::tags( 'td', array( 'valign' => 'top' ),
					wfMsgWikiHtml( $question['question'] ) );
				$retval .= Xml::openElement( 'td',
					array( 'valign' => 'top' ) );
				$attrs = array(	'id' => "survey-$id",
						'name' => "survey-$id" );
				if ( isset( $question['other'] ) ) {
					$attrs['class'] = 'optin-need-other';
				}
				$retval .= Xml::openElement( 'select', $attrs );
				$retval .= Xml::option( '', '' );
				foreach ( $question['answers'] as $aid => $answer ) {
					$retval .= Xml::option(	wfMsg( $answer ), $aid );
				}
				if ( isset( $question['other'] ) ) {
					$retval .= Xml::option( wfMsg( $question['other'] ),
						'other' );
				}
				$retval .= Xml::closeElement( 'select' );
				if ( isset( $question['other'] ) ) {
					$retval .= ' ';
					$retval .= Xml::input( "survey-$id-other",
						false, false,
						array(	'class' => 'optin-other-select',
							'id' => "survey-$id-other" ) );
				}
				$retval .= Xml::closeElement( 'td' );
				$retval .= Xml::closeElement( 'tr' );
			break;
			case 'radios':
				$retval .= Xml::openElement( 'tr' );
				$retval .= Xml::tags( 'td',
					array( 'valign' => 'top' ),
					wfMsgWikiHtml( $question['question'] ) );
				$retval .= Xml::openElement( 'td',
					array( 'valign' => 'top' ) );
				$radios = array();
				foreach ( $question['answers'] as $aid => $answer ) {
					$radios[] = Xml::radioLabel( wfMsg( $answer ),
						"survey-$id", $aid, "survey-$id-$aid" );
				}
				if ( isset( $question['other'] ) ) {
					$radios[] = Xml::radioLabel( wfMsg( $question['other'] ),
						"survey-$id", 'other', "survey-$id-other-radio" ) .
						'&nbsp;' .
						Xml::input( "survey-$id-other",
							false, false,
							array( 'class' => 'optin-other-radios' ) );
				}
				$retval .= implode( Xml::element( 'br' ), $radios );
				$retval .= Xml::closeElement( 'td' );
				$retval .= Xml::closeElement( 'tr' );
			break;
			case 'resolution':
				$retval .= Xml::openElement( 'tr' );
				$retval .= Xml::tags( 'td',
					array( 'valign' => 'top' ),
					wfMsgWikiHtml( $question['question'] ) );
				$retval .= Xml::openElement( 'td',
					array( 'valign' => 'top' ) );
				$retval .= Xml::input( "survey-$id-x",
						5, false, array(
							'class' => 'optin-resolution-x',
							'id' => "survey-$id-x",
						)
				);
				$retval .= ' x ';
				$retval .= Xml::input( "survey-$id-y",
						5, false, array(
							'class' => 'optin-resolution-y',
							'id' => "survey-$id-y",
						)
				);
				$retval .= Xml::closeElement( 'td' );
				$retval .= Xml::closeElement( 'tr' );
			break;
			case 'textarea':
				$retval .= Xml::openElement( 'tr' );
				$retval .= Xml::tags( 'td',
					array( 'valign' => 'top' ),
					wfMsgWikiHtml( $question['question'] ) );
				$retval .= Xml::tags( 'td',
					array( 'valign' => 'top' ),
					Xml::textarea( "survey-$id", '' ) );
				$retval .= Xml::closeElement( 'tr' );
			break;
			}
		}
		$retval .= Xml::tags( 'tr', array(),
			Xml::tags( 'td', array( 'colspan' => 2, 'class' => 'optin-survey-submit' ),
				Xml::submitButton( wfMsg( 'optin-submit-out' ) ) ) );
		$retval .= Xml::closeElement( 'table' );
		$wgOut->addHTML( $retval );
	}

	private function saveSurvey() {
		global $wgRequest, $wgUser, $wgOptInSurvey;
		
		$dbw = wfGetDb( DB_MASTER );
		$now = $dbw->timestamp( wfTimestamp() );
		foreach ( $wgOptInSurvey as $id => $question ) {
			$insert = array(
				'ois_user' => $wgUser->getId(),
				'ois_timestamp' => $now,
				'ois_question' => $id );
			switch ( $question['type'] ) {
			case 'dropdown':
			case 'radios':
				$answer = $wgRequest->getVal( "survey-$id", '' );
				if ( $answer === 'other' ) {
					$insert['ois_answer'] = null;
					$insert['ois_answer_data'] = $wgRequest->getVal( "survey-$id-other" );
				} else if ( $answer === '' ) {
					$insert['ois_answer'] = null;
					$insert['ois_answer_data'] = null;
				} else  {
					$insert['ois_answer'] = intval( $answer );
					$insert['ois_answer_data'] = null;
				}
			break;
			case 'resolution':
				$x = $wgRequest->getVal( "survey-$id-x" );
				$y = $wgRequest->getVal( "survey-$id-y" );
				if ( $x === '' && $y === '' ) {
					$insert['ois_answer'] = null;
					$insert['ois_answer_data'] = null;
				} else {
					$insert['ois_answer'] = null;
					$insert['ois_answer_data'] = $x . 'x' . $y;
				}
			break;
			case 'textarea':
				$answer = $wgRequest->getVal( "survey-$id" );
				if ( $answer === '' ) {
					$insert['ois_answer'] = null;
					$insert['ois_answer_data'] = null;
				} else {
					$insert['ois_answer'] = null;
					$insert['ois_answer_data'] = $answer;
				}
			break;
			}
			$dbw->insert( 'optin_survey', $insert, __METHOD__ );
		}
	}
}
