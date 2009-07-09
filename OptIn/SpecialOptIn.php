<?php
/**
 * Special:OptIn
 *
 * @file
 * @ingroup Extensions
 */

class SpecialOptIn extends SpecialPage {
	function __construct() {
		parent::__construct( 'OptIn' );
		wfLoadExtensionMessages( 'OptIn' );
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser;
		$this->setHeaders();
		$wgOut->setPageTitle( wfMsg( 'optin-title' ) );

		if ( $wgUser->isAnon() ) {
			$url = SpecialPage::getTitleFor( 'Userlogin' )->getFullURL(
				array( 'returnto' => $this->getTitle()->getPrefixedUrl() ) );
			$wgOut->wrapWikiMsg( "<div class='plainlinks'>\n$1\n</div>", array( 'optin-needlogin', $url ) );
			return;
		}

		if ( $wgRequest->wasPosted() ) {
			if ( $wgRequest->getVal( 'opt' ) === 'in' ) {
				$this->optIn( $wgUser );
				$wgOut->addWikiMsg( 'optin-success-in' );
			} else {
				$this->optOut( $wgUser );
				$this->saveSurvey();
				$wgOut->addWikiMsg( 'optin-success-out' );
			}
		}
		$this->showForm();
	}

	function showForm() {
		global $wgUser, $wgOut;
		$wgOut->addHTML( Xml::openElement( 'form', array(
			'method' => 'post',
			'action' => $this->getTitle()->getLinkURL()
		) ) );
		$opt = ( $this->isOptedIn( $wgUser ) ? 'out' : 'in' );
		if ( $opt == 'out' ) {
			$wgOut->addWikiMsg( 'optin-survey-intro' );
			$this->showSurvey();
		}
		else
			$wgOut->addWikiMsg( 'optin-intro' );
		$wgOut->addHTML( Xml::hidden( 'opt', $opt ) );
		// Uses the optin-submit-in or optin-submit-out message
		if ( $opt == 'in' )
			$this->showOptInButtons();
		else
			$wgOut->addHTML( Xml::submitButton( wfMsg( "optin-submit-out" ) ) );
		$wgOut->addHTML( Xml::closeElement( 'form' ) );
		$wgOut->addWikiMsg( 'optin-improvements' );
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
					Xml::tags( 'a', array(), // TODO: target
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
			) . "&nbsp; " .
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
			Xml::element( 'div', array( 'style' => 'clear: both; ' ) )
		);
	}

	function isOptedIn( $user ) {
		global $wgOptInPrefs;
		foreach ( $wgOptInPrefs as $pref => $value ) {
			if ( $user->getOption( $pref ) != $value ) {
				return false;
			}
		}
		return true;
	}

	function optIn( $user ) {
		global $wgOptInPrefs;
		foreach ( $wgOptInPrefs as $pref => $value ) {
			$user->setOption( $pref, $value );
		}
		$user->saveSettings();
	}

	function optOut( $user ) {
		global $wgOptInPrefs;
		foreach ( $wgOptInPrefs as $pref => $value ) {
			$user->setOption( $pref, null );
		}
		$user->saveSettings();
	}

	function showSurvey() {
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
		$retval .= Xml::closeElement( 'table' );
		$wgOut->addHTML( $retval );
	}

	function saveSurvey() {
		global $wgRequest, $wgUser, $wgOptInSurvey;
		$dbw = wfGetDb( DB_MASTER );
		$now = $dbw->timestamp( wfTimestamp() );
		// var_dump($wgRequest->data); die();
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
