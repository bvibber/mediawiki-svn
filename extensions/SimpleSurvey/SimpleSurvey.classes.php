<?php 
class SimpleSurvey extends PrefSwitchSurvey {
	// Correlation between names of field types and implementation classes
	private static $fieldTypes = array(
		'select' => 'PrefSwitchSurveyFieldSelect',
		'radios' => 'PrefSwitchSurveyFieldRadios',
		'checks' => 'PrefSwitchSurveyFieldChecks',
		'boolean' => 'PrefSwitchSurveyFieldBoolean',
		'dimensions' => 'PrefSwitchSurveyFieldDimensions',
		'text' => 'PrefSwitchSurveyFieldText',
		'smallinput' => 'PrefSwitchSurveyFieldSmallInput',
	);
	
	/* Static Functions */
	
	/* update schema*/
	public static function schema(  ){
		global $wgExtModifiedFields, $wgExtNewTables;
		
		$wgExtNewTables[] = array( 'prefswitch_survey', 
			dirname( dirname( __FILE__ ) ) ."/UsabilityInitiative/PrefSwitch/PrefSwitch.sql"
		);
		
		$wgExtModifiedFields[] = array(
			'prefswitch_survey',
			'pss_user',
			dirname( __FILE__ ) . '/SimpleSurvey.patch-pssuser.sql'
		);
		
		return true;
	}
	
	/**
	 * creates a random token
	 * @return a random token
	 */
	public static function generateRandomCookieID() {
		global $wgUser;
		return wfGenerateToken( array( $wgUser, time() ) );
	}
	
	
	/**
	 * Render the HTML for a survey.
	 * @param $name string Survey name
	 * @param $questions array Array containing question data
	 * @param $loadFromDB bool Load previous survey data from the database
	 * @return string HTML
	 */
	public static function render( $questions ) {
		global $wgUser;
		
		$html = Xml::openElement( 'dl' );
		foreach ( $questions as $field => $config ) {
			$answer = null;
			$answerData = null;
			$invisible = false;
			if(isset($config['visibility'])  && $config['visibility'] == 'hidden' ){
				$invisible = true;
			}
			if($invisible){
				$html .= Xml::openElement( 'div', array("style" => "display:none;") );
			}
			$html .= call_user_func( array( self::$fieldTypes[$config['type']], 'render' ),
				$field, $config, $answer, $answerData
			);
			if($invisible){
				$html .= Xml::closeElement('div');
			}
		}
		$html .= Xml::closeElement( 'dl' );
		return $html;
	}
		
	/**
	 * Save a survey to the database
	 * @param $name string Survey name
	 * @param $survey array Survey configuration data
	 */
	public static function save( $name, $survey ) {
		global $wgRequest, $wgUser;
		$dbw = wfGetDb( DB_MASTER );
		$now = $dbw->timestamp();
		$cookieID = $wgRequest->getCookie("vitals-survey");
		if($cookieID == null){
			$cookieID = self::generateRandomCookieID();
			$wgRequest->response()->setcookie("vitals-survey", $cookieID);
		}
		
		foreach ( $survey['questions'] as $question => $config ) {
			$dbw->insert(
				'prefswitch_survey',
				array_merge(
					array(
						'pss_user' => $cookieID,
						'pss_timestamp' => $now,
						'pss_name' => $name,
						'pss_question' => $question,
					),
					call_user_func( array( self::$fieldTypes[$config['type']], 'save' ), $question, $wgRequest )
				),
				__METHOD__
			);
		}
		
		//pseudoquestion, logged in? IP address?
		$dbw->insert(
				'prefswitch_survey',
				array(
					'pss_user' => $cookieID,
					'pss_timestamp' => $now,
					'pss_name' => $name,
					'pss_question' => "logged_in",
					'pss_answer' => $wgUser->isLoggedIn()?"yes":"no",
					'pss_answer_data' => isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:null,
				),
				__METHOD__
			);
		
		
		
	}
}