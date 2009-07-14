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
	private $mOriginQuery = '';
	private $mOriginLink = '';
	private $mOriginURL = '';
	
	/* Static Functions */
	
	public static function isOptedIn( $user ) {
		global $wgOptInPrefs;
		
		if ( $user->isAnon() )
			return false;
		
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
		
		$par = $wgRequest->getVal( 'from', $par );
		$this->mOriginTitle = Title::newFromText( $par );
		if ( $this->mOriginTitle ) {
			$this->mOrigin = $this->mOriginTitle->getPrefixedDBKey();
			$this->mOriginQuery = $wgRequest->getVal( 'fromquery' );
			$this->mOriginLink = $wgUser->getSkin()->link(
				$this->mOriginTitle, null, array(),
				$this->mOriginQuery );
			$this->mOriginURL = $this->mOriginTitle->getFullURL(
				$this->mOriginQuery );
		}
		$this->setHeaders();
		
		if ( self::isOptedIn( $wgUser ) ) {
			if ( $wgRequest->getVal( 'opt' ) == 'out' )
				// Just opted out
				$wgOut->setPageTitle( wfMsg( 'optin-title-justoptedout' ) );
			else
				// About to opt out
				$wgOut->setPageTitle( wfMsg( 'optin-title-optedin' ) );
		}
		else
		{
			if ( $wgRequest->getVal( 'opt' ) == 'in' )
				// Just opted in
				$wgOut->setPageTitle( wfMsg( 'optin-title-justoptedin' ) );
			else
				// About to opt in
				$wgOut->setPageTitle( wfMsg( 'optin-title-optedout' ) );
		}

		if ( $wgRequest->getCheck( 'opt' ) ) {
			if ( $wgRequest->getVal( 'opt' ) === 'in' ) {
				self::optIn( $wgUser );
				$wgOut->addWikiMsg( 'optin-success-in' );
			} else {
				self::optOut( $wgUser );
				$this->saveSurvey();
				$wgOut->addWikiMsg( 'optin-success-out' );
			}
			if ( $this->mOriginTitle )
				$wgOut->addHTML( wfMsg( 'returnto',
					$this->mOriginLink ) );
		}
		else
			$this->showForm();
	}
	
	/* Private Functions */

	private function showForm() {
		global $wgUser, $wgOut;
		
		$opt = ( self::isOptedIn( $wgUser ) ? 'out' : 'in' );
		if ( $opt == 'out' ) {
			$wgOut->addWikiMsg( 'optin-survey-intro' );
			if ( $this->mOriginTitle )
				$wgOut->addHTML( wfMsg( 'optin-leave-cancel',
					$this->mOriginLink ) );
			$this->showSurvey();
		}
		else
		{
			$wgOut->wrapWikiMsg(
				"<div class='optin-intro'>\n$1\n</div>",
				array( 'optin-intro' )
			);
			$this->showOptInButtons();
			$wgOut->addWikiMsg( 'optin-improvements' );
		}
	}
	
	function showOptInButtons() {
		global $wgUser, $wgOut, $wgOptInStyleVersion;
		
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addStyle( 'OptIn/OptIn.css',
				$wgOptInStyleVersion );
		
		$query = array(	'opt' => 'in',
				'from' => $this->mOrigin,
				'fromquery' => $this->mOriginQuery
		);
		if ( $wgUser->isLoggedIn() )
			$url = $this->getTitle()->getFullURL( $query );
		else
			$url = SpecialPage::getTitleFor( 'Userlogin' )->getFullURL(
				array(	'returnto' => $this->getTitle(),
					'returntoquery' => wfArrayToCGI( $query )
				) );
		
		$wgOut->addHTML(
			Xml::tags( 'div', array( 'class' => 'optin-accept' ),
				Xml::tags( 'div', array(),
				Xml::tags( 'div', array(),
				Xml::tags( 'div', array(),
				Xml::tags( 'div', array(),
					Xml::tags(
						'a',
						array( 'href' => $url ),
						Xml::element( 'span',
							array( 'class' => 'optin-button-shorttext' ),
							wfMsg( 'optin-accept-short' )
						) .
						Xml::element( 'br' ) .
						Xml::element( 'span',
							array( 'class' => 'optin-button-longtext' ),
							$wgUser->isLoggedIn() ?
							wfMsg( 'optin-accept-long' ) :
							wfMsg( 'optin-accept-long-anon' )
						)
					)
				) ) ) )
			)
		);
		if ( $this->mOriginTitle ) {
			$wgOut->addHTML(
				Xml::tags( 'div', array( 'class' => 'optin-deny' ),
					Xml::tags( 'div', array(),
					Xml::tags( 'div', array(),
					Xml::tags( 'div', array(),
					Xml::tags( 'div', array(),
						Xml::tags(
							'a',
							array(
								'href' => $this->mOriginURL
							),
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
				)
			);
		}
		$wgOut->addHTML(
			Xml::element(
				'div', array( 'style' => 'clear: both; ' ), '', false
			)
		);
	}

	private function showSurvey() {
		global $wgOptInSurvey, $wgOut, $wgOptInStyleVersion;
		
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addScript( 'OptIn/OptIn.js',
			$wgOptInStyleVersion );
		UsabilityInitiativeHooks::addStyle( 'OptIn/OptIn.css',
				$wgOptInStyleVersion );
		
		$query = array(	'from' => $this->mOrigin,
				'fromquery' => $this->mOriginQuery
		);
		$retval = Xml::openElement(
			'form', array(
				'method' => 'post',
				'action' => $this->getTitle()->getLinkURL( $query ),
				'id' => 'optin-survey',
			)
		);
		$retval .= Xml::hidden( 'opt', 'out' );
		$retval .= Xml::openElement( 'dl' );
		foreach ( $wgOptInSurvey as $id => $question ) {
			switch ( $question['type'] ) {
			case 'dropdown':
				$retval .= Xml::tags(
					'dt', null, wfMsgWikiHtml( $question['question'] )
				);
				$retval .= Xml::openElement( 'dd' );
				$attrs = array(
					'id' => "survey-$id", 'name' => "survey-$id"
				);
				if ( isset( $question['other'] ) ) {
					$attrs['class'] = 'optin-need-other';
				}
				$retval .= Xml::openElement( 'select', $attrs );
				$retval .= Xml::option( '', '' );
				foreach ( $question['answers'] as $aid => $answer ) {
					$retval .= Xml::option(	wfMsg( $answer ), $aid );
				}
				if ( isset( $question['other'] ) ) {
					$retval .= Xml::option(
						wfMsg( $question['other'] ), 'other'
					);
				}
				$retval .= Xml::closeElement( 'select' );
				if ( isset( $question['other'] ) ) {;
					$retval .= Xml::tags( 'div', array(),
						Xml::input(
							"survey-$id-other",
							false,
							false,
							array(
								'class' => 'optin-other-select',
								'id' => "survey-$id-other"
							)
						)
					);
				}
				$retval .= Xml::closeElement( 'dd' );
			break;
			case 'radios':
				$retval .= Xml::tags(
					'dt', null, wfMsgWikiHtml( $question['question'] )
				);
				$retval .= Xml::openElement( 'dd' );
				$radios = array();
				foreach ( $question['answers'] as $aid => $answer ) {
					$radios[] = Xml::radioLabel(
						wfMsg( $answer ), "survey-$id", $aid, "survey-$id-$aid"
					);
				}
				if ( isset( $question['other'] ) ) {
					$radios[] = Xml::radioLabel(
						wfMsg( $question['other'] ),
						"survey-$id",
						'other',
						"survey-$id-other-radio"
					) .
					'&nbsp;' .
					Xml::input(
						"survey-$id-other",
						false,
						false,
						array( 'class' => 'optin-other-radios' )
					);
				}
				$retval .= implode( Xml::element( 'br' ), $radios );
				$retval .= Xml::closeElement( 'dd' );
			break;
			case 'checkboxes':
				$retval .= Xml::tags(
					'dt', null, wfMsgWikiHtml( $question['question'] )
				);
				$retval .= Xml::openElement( 'dd' );
				$checkboxes = array();
				foreach ( $question['answers'] as $aid => $answer ) {
					$checkboxes[] = Xml::checkLabel(
						wfMsg( $answer ),
						"survey-{$id}[]",
						"survey-$id-$aid",
						false,
						array( 'value' => $aid )
					);
				}
				if ( isset( $question['other'] ) ) {
					$checkboxes[] = Xml::checkLabel(
						wfMsg( $question['other'] ),
						"survey-{$id}[]",
						"survey-$id-other-check",
						false,
						array( 'value' => 'other' )
					) .
					'&nbsp;' .
					Xml::input(
						"survey-$id-other",
						false,
						false,
						array( 'class' => 'optin-other-checks' )
					);
				}
				$retval .= implode( Xml::element( 'br' ), $checkboxes );
				$retval .= Xml::closeElement( 'dd' );
			break;
			case 'yesno':
				$retval .= Xml::tags(
					'dt', null, wfMsgWikiHtml( $question['question'] )
				);
				$retval .= Xml::openElement( 'dd' );
				$retval .= Xml::radioLabel(
					wfMsg( 'optin-survey-yes' ),
					"survey-$id",
					'yes',
					"survey-$id-yes",
					false,
					array( 'class' => 'survey-yes' )
				);
				$retval .= Xml::element( 'br' );
				$retval .= Xml::radioLabel(
					wfMsg( 'optin-survey-no' ),
					"survey-$id",
					'no',
					"survey-$id-no",
					false,
					array( 'class' => 'survey-no' )
				);
				$retval .= Xml::closeElement( 'dd' );
				if ( isset( $question['ifyes'] ) ) {
					$retval .= Xml::openElement(
						'blockquote', array(
							'id' => "survey-$id-ifyes-row",
							'class' => 'survey-ifyes',
						)
					);
					$retval .= Xml::tags(
						'dt', null, wfMsgWikiHtml( $question['ifyes'] )
					);
					$retval .= Xml::tags(
						'dd', null, Xml::textarea( "survey-$id-ifyes", '' )
					);
					$retval .= Xml::closeElement( 'blockquote' );
				}
				if ( isset( $question['ifno'] ) ) {
					$retval .= Xml::openElement(
						'blockquote', array(
							'id' => "survey-$id-ifno-row",
							'class' => 'survey-ifyes',
						)
					);
					$retval .= Xml::tags(
						'dt', null, wfMsgWikiHtml( $question['ifno'] )
					);
					$retval .= Xml::tags(
						'dd', null, Xml::textarea( "survey-$id-ifno", '' )
					);
					$retval .= Xml::closeElement( 'blockquote' );
				}
			break;
			case 'resolution':
				$retval .= Xml::tags(
					'dt', null, wfMsgWikiHtml( $question['question'] )
				);
				$retval .= Xml::openElement( 'dd' );
				$retval .= Xml::input(
					"survey-$id-x",
					5,
					false,
					array(
						'class' => 'optin-resolution-x',
						'id' => "survey-$id-x",
					)
				);
				$retval .= ' x ';
				$retval .= Xml::input(
					"survey-$id-y",
					5,
					false,
					array(
						'class' => 'optin-resolution-y',
						'id' => "survey-$id-y",
					)
				);
				$retval .= Xml::closeElement( 'dd' );
			break;
			case 'textarea':
				$retval .= Xml::tags(
					'dt', null, wfMsgWikiHtml( $question['question'] )
				);
				$retval .= Xml::tags(
					'dd', null, Xml::textarea( "survey-$id", '' )
				);
			break;
			}
		}
		$retval .= Xml::tags(
			'dt',
			array( 'class' => 'optin-survey-submit' ),
			Xml::element( 'a', array( 'id' => 'leave' ), '', false ) .
				Xml::submitButton( wfMsg( 'optin-submit-out' ) )
		);
		$retval .= Xml::closeElement( 'dl' );
		$retval .= Xml::closeElement( 'form' );
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
					$insert['ois_answer_data'] =
						$wgRequest->getVal( "survey-$id-other" );
				} else if ( $answer === '' ) {
					$insert['ois_answer'] = null;
					$insert['ois_answer_data'] = null;
				} else  {
					$insert['ois_answer'] = intval( $answer );
					$insert['ois_answer_data'] = null;
				}
			break;
			case 'checkboxes':
				$checked = array_map(
					'intval', $wgRequest->getArray( "survey-$id", array() )
				);
				$insert['ois_answer'] =
					( count( $checked ) ? implode( ',', $checked ) : null );
				$insert['ois_answer_data'] = ( in_array( 'other', $checked ) ?
					$wgRequest->getVal( "survey-$id-other" ) : null
				);
			break;
			case 'yesno':
				$insert['ois_answer'] =
					$wgRequest->getVal( "survey-$id", null );
				$data = '';
				if ( $insert['ois_answer'] == 'yes' )
					$data += $wgRequest->getVal( "survey-$id-ifyes", '' );
				if ( $insert['ois_answer'] == 'no' )
					$data += $wgRequest->getVal( "survey-$id-ifno", '' );
				$insert['ois_answer_data'] = ( $data ? $data : null );
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
