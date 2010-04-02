<?php

class PrefSwitchSurvey {
	
	/* Private Static Members */
	
	// Correlation between names of field types and implementation classes
	private static $fieldTypes = array(
		'select' => 'PrefSwitchSurveyFieldSelect',
		'radios' => 'PrefSwitchSurveyFieldRadios',
		'checks' => 'PrefSwitchSurveyFieldChecks',
		'boolean' => 'PrefSwitchSurveyFieldBoolean',
		'dimensions' => 'PrefSwitchSurveyFieldDimensions',
		'text' => 'PrefSwitchSurveyFieldText',
	);
	
	/* Static Functions */
	
	public static function render( $name, $questions, $loadFromDB = false ) {
		global $wgUser, $wgOut;
		// Load existing data from the database - allowing the user to change their answers
		$loaded = array();
		if ( $loadFromDB ) {
			$dbr = wfGetDb( DB_SLAVE );
			$res = $dbr->select(
				'prefswitch_survey',
				array( 'pss_question', 'pss_answer', 'pss_answer_data' ),
				array( 'pss_user' => $wgUser->getID(), 'pss_name' => $name ),
				__METHOD__
			);
			foreach( $res as $row ) {
				$loaded[$row->pss_field] = array( $row->pss_answer, $row->pss_answer_data );
			}
		}
		$html = Xml::openElement( 'dl' );
		foreach ( $questions as $field => $config ) {
			$answer = isset( $loaded[$field] ) ? $loaded[$field][0] : null;
			$answerData = isset( $loaded[$field] ) ? $loaded[$field][1] : null;
			if ( in_array( 'PrefSwitchSurveyField', class_implements( self::$fieldTypes[$config['type']] ) ) ) {
				$html .= call_user_func(
					array( self::$fieldTypes[$config['type']], 'render' ), $field, $config, $answer, $answerData
				);
			}
		}
		$html .= Xml::closeElement( 'dl' );
		return $html;
	}
	public static function save( $survey, $name ) {
		global $wgRequest, $wgUser;
		$dbw = wfGetDb( DB_MASTER );
		$now = $dbw->timestamp( wfTimestamp() );
		foreach ( $survey as $question => $config ) {
			if ( in_array( 'PrefSwitchSurveyField', class_implements( self::$fieldTypes[$config['type']] ) ) ) {
				$dbw->insert(
					'prefswitch_survey',
					array_merge(
						array(
							'pss_user' => $wgUser->getId(),
							'pss_timestamp' => $now,
							'pss_name' => $name,
							'pss_question' => $question,
						),
						call_user_func( array( self::$fieldTypes[$config['type']], 'save' ), $question, $wgRequest )
					),
					__METHOD__
				);
			}
		}
	}
}
interface PrefSwitchSurveyField {	

	/* Static Functions */
	
	public static function render( $question, $config, $answer, $answerData );
	public static function save( $question, $request );
}
class PrefSwitchSurveyFieldSelect implements PrefSwitchSurveyField {

	/* Static Functions */

	public static function render( $question, $config, $answer, $answerData ) {
		$html = Xml::tags( 'dt', null, wfMsgWikiHtml( $config['question'] ) );
		$html .= Xml::openElement( 'dd' );
		$attrs = array( 'id' => "prefswitch-survey-{$question}", 'name' => "prefswitch-survey-{$question}" );
		if ( isset( $config['other'] ) ) {
			$attrs['class'] = "prefswitch-survey-need-other";
		}
		$html .= Xml::openElement( 'select', $attrs );
		foreach ( $config['answers'] as $answerId => $answerMsg ) {
			$html .= Xml::option( wfMsg( $answerMsg ), $answerId, $answer === $answerId );
		}
		if ( isset( $config['other'] ) ) {
			$html .= Xml::option( wfMsg( $config['other'] ), 'other', $answer === 'other' );
		}
		$html .= Xml::closeElement( 'select' );
		if ( isset( $config['other'] ) ) {;
			$html .= Xml::tags( 'div', array(),
				Xml::input(
					"prefswitch-survey-{$question}-other",
					false,
					$answer === 'other' ? $answerData : false,
					array(
						'class' => 'prefswitch-survey-other-select',
						'id' => "prefswitch-survey-{$question}-other",
					)
				)
			);
		}
		$html .= Xml::closeElement( 'dd' );
		return $html;
	}
	public static function save( $question, $request ) {
		$answer = $request->getVal( "prefswitch-survey-{$question}", '' );
		switch ( $answer ) {
			case 'other':
				return array(
					'pss_answer' => null,
					'pss_answer_data' => $request->getVal( "prefswitch-survey-{$question}-other" )
				);
			case '':
				return array(
					'pss_answer' => null,
					'pss_answer_data' => null,
				);
			default:
				return array(
					'pss_answer' => $answer,
					'pss_answer_data' => null,
				);
		}
	}
}
class PrefSwitchSurveyFieldRadios extends PrefSwitchSurveyFieldSelect {

	/* Static Functions */

	public static function render( $question, $config, $answer, $answerData ) {
		$html = Xml::tags( 'dt', null, wfMsgWikiHtml( $config['question'] ) );
		$html .= Xml::openElement( 'dd' );
		$radios = array();
		foreach ( $config['answers'] as $answerId => $answerMsg ) {
			$radios[] = Xml::radioLabel(
				wfMsg( $answerMsg ),
				"prefswitch-survey-{$question}",
				$answerId,
				"prefswitch-survey-{$question}-{$answerId}",
				$answer === $answerId
			);
		}
		if ( isset( $config['other'] ) ) {
			$radios[] = Xml::radioLabel(
				wfMsg( $config['other'] ),
				"prefswitch-survey-{$question}",
				'other',
				"prefswitch-survey-{$question}-other",
				$answer === 'other'
			) .
			'&nbsp;' .
			Xml::input(
				"prefswitch-survey-{$question}-other-radio",
				false,
				$answer === 'other' ? $answerData : false,
				array( 'class' => 'prefswitch-survey-other-radios' )
			);
		}
		$html .= implode( Xml::element( 'br' ), $radios );
		$html .= Xml::closeElement( 'dd' );
		return $html;
	}
}
class PrefSwitchSurveyFieldChecks implements PrefSwitchSurveyField {

	/* Static Functions */

	public static function render( $question, $config, $answer, $answerData ) {
		$answers = explode( ',', $answer );
		$html = Xml::tags( 'dt', null, wfMsgWikiHtml( $config['question'] ) );
		$html .= Xml::openElement( 'dd' );
		$checkboxes = array();
		foreach ( $config['answers'] as $answerId => $answerMsg ) {
			$checkboxes[] = Xml::checkLabel(
				wfMsg( $answerMsg ),
				"prefswitch-survey-{$question}[]",
				"prefswitch-survey-{$question}-{$answerId}",
				in_array( $answerId, $answers, true ),
				array( 'value' => $answerId )
			);
		}
		if ( isset( $config['other'] ) ) {
			$checkboxes[] = Xml::checkLabel(
				wfMsg( $config['other'] ),
				"prefswitch-survey-{$question}[]",
				"prefswitch-survey-{$question}-other-check",
				in_array( 'other', $answers, true ),
				array( 'value' => 'other' )
			) .
			'&nbsp;' .
			Xml::input(
				"prefswitch-survey-{$question}-other",
				false,
				in_array( 'other', $answers, true ) ? $answerData : false,
				array( 'class' => 'prefswitch-survey-other-checks' )
			);
		}
		$html .= implode( Xml::element( 'br' ), $checkboxes );
		$html .= Xml::closeElement( 'dd' );
		return $html;
	}
	public static function save( $question, $request ) {
		$checked = $request->getArray( "prefswitch-survey-{$question}", array() );
		return array(
			'pss_answer' => ( count( $checked ) ? implode( ',', $checked ) : null ),
			'pss_answer_data' =>
				in_array( 'other', $checked ) ? $request->getVal( "prefswitch-survey-{$question}-other" ) : null,
		);
	}
}
class PrefSwitchSurveyFieldBoolean implements PrefSwitchSurveyField {

	/* Static Functions */

	public static function render( $question, $config, $answer, $answerData ) {
		$html = Xml::tags( 'dt', null, wfMsgWikiHtml( $config['question'] ) );
		$html .= Xml::openElement( 'dd' );
		$html .= Xml::radioLabel(
			wfMsg( 'prefswitch-survey-true' ),
			"prefswitch-survey-{$question}",
			'true',
			"prefswitch-survey-{$question}-true",
			$answer === 'true',
			array( 'class' => 'prefswitch-survey-true' )
		);
		$html .= Xml::element( 'br' );
		$html .= Xml::radioLabel(
			wfMsg( 'prefswitch-survey-false' ),
			"prefswitch-survey-{$question}",
			'false',
			"prefswitch-survey-{$question}-false",
			$answer === 'false',
			array( 'class' => 'prefswitch-survey-false' )
		);
		$html .= Xml::closeElement( 'dd' );
		if ( isset( $config['iftrue'] ) ) {
			$html .= Xml::openElement(
				'blockquote',
				array( 'id' => "prefswitch-survey-{$question}-iftrue-row", 'class' => 'prefswitch-survey-iftrue' )
			);
			$html .= Xml::tags( 'dt', null, wfMsgWikiHtml( $config['iftrue'] ) );
			$html .= Xml::tags(
				'dd', null, Xml::textarea( "prefswitch-survey-{$question}-iftrue", $answerData ? $answerData : '' )
			);
			$html .= Xml::closeElement( 'blockquote' );
		}
		if ( isset( $config['iffalse'] ) ) {
			$html .= Xml::openElement(
				'blockquote',
				array( 'id' => "prefswitch-survey-{$question}-iffalse-row", 'class' => 'prefswitch-survey-iffalse' )
			);
			$html .= Xml::tags( 'dt', null, wfMsgWikiHtml( $config['iffalse'] ) );
			$html .= Xml::tags(
				'dd', null, Xml::textarea( "prefswitch-survey-{$question}-iffalse", $answerData ? $answerData : '' )
			);
			$html .= Xml::closeElement( 'blockquote' );
		}
		return $html;
	}
	public static function save( $question, $request ) {
		return array(
			'pss_answer' => $request->getVal( "prefswitch-survey-{$question}", null ),
			'pss_answer_data' => $insert['pss_answer'] == 'true' || $insert['pss_answer'] == 'false' ?
				$request->getVal( "prefswitch-survey-{$question}-if{$insert['pss_answer']}", null ) : null,
		);
	}
}
class PrefSwitchSurveyFieldDimensions implements PrefSwitchSurveyField {

	/* Static Functions */

	public static function render( $question, $config, $answer, $answerData ) {
		list( $x, $y ) = $answerData ? explode( 'x', $answerData ) : array( false, false );
		$html = Xml::tags( 'dt', null, wfMsgWikiHtml( $config['question'] ) );
		$html .= Xml::openElement( 'dd' );
		$html .= Xml::input(
			"prefswitch-survey-{$question}-x", 5, $x, array( 'id' => "prefswitch-survey-{$question}-x" )
		);
		$html .= ' x ';
		$html .= Xml::input(
			"prefswitch-survey-{$question}-y", 5, $y, array( 'id' => "prefswitch-survey-{$question}-y" )
		);
		$html .= Xml::closeElement( 'dd' );
		return $html;
	}
	public static function save( $question, $request ) {
		$x = $request->getVal( "prefswitch-survey-{$question}-x" );
		$y = $request->getVal( "prefswitch-survey-{$question}-y" );
		if ( $x === '' && $y === '' ) {
			return array(
				'pss_answer' => null,
				'pss_answer_data' => null,
			);
		} else {
			return array(
				'pss_answer' => null,
				'pss_answer_data' => $x . 'x' . $y,
			);
		}
	}
}
class PrefSwitchSurveyFieldText implements PrefSwitchSurveyField {

	/* Static Functions */

	public static function render( $question, $config, $answer, $answerData ) {
		$html = Xml::tags( 'dt', null, wfMsgWikiHtml( $config['question'] ) );
		$html .= Xml::tags(
			'dd', null, Xml::textarea( "prefswitch-survey-{$question}", $answerData ? $answerData : '' )
		);
		return $html;
	}
	public static function save( $question, $request ) {
		$answer = $request->getVal( "prefswitch-survey-{$question}" );
		if ( $answer === '' ) {
			return array(
				'pss_answer' => null,
				'pss_answer_data' => null,
			);
		} else {
			return array(
				'pss_answer' => null,
				'pss_answer_data' => $answer,
			);
		}
	}
}
