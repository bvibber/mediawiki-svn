<?php

/**
 * Extend the API for click tracking
 * 
 * @author nimishgautam
 *
 */
class ApiClickTracking extends ApiBase {

	/**
	 * runs when the api is called with "clicktracking", takes in "eventid" and an edit token given to the user, "token"
	 * @see includes/api/ApiBase#execute()
	 */
	public function execute(){
		global $wgUser, $wgTitle;
		
		$params = $this->extractRequestParams();
		$this->validateParams( $params );
		$eventid_to_lookup = $params['eventid'];
		$session_id = $params['token'];
		
		//Event ID lookup table
		$event_id = ClickTrackingHooks::getEventIDFromName(urldecode($eventid_to_lookup));
		
		$is_logged_in = $wgUser->isLoggedIn();
		
		
		ClickTrackingHooks::trackEvent(
		  $session_id,  //randomly generated session ID
		  $is_logged_in, 						 //is the user logged in?
		  $wgTitle->getNamespace(), 			 //what namespace are they editing?
		  $event_id,							 //event ID passed in
		  ( $is_logged_in ? $wgUser->getEditCount() : 0 ), //total edit count or 0 if anonymous
		  ( $is_logged_in ? 
		  				( ClickTrackingHooks::getEditCountSince( time() - $wgClickTrackContribTimeValue) ) 
		       			: 0)   					 //contributions since whatever the time value is, or 0 if anonymous
		  );
	}

	/**
	 * required parameter check
	 * @param $params params extracted from the POST
	 * @return unknown_type
	 */
 	protected function validateParams( $params ) {
        $required = array( 'eventid', 'token');
        foreach( $required as $arg ) {
            if ( !isset( $params[$arg] ) ) {
                $this->dieUsageMsg( array( 'missingparam', $arg ) );
            }
        }
    }
    
    

    public function getParamDescription() {
        return array(
            'eventid' => 'string of eventID',
        	'token'  => 'unique edit ID for this edit session'
        );
    }
    
	public function getDescription() {
        return array(
            'Track user clicks on Javascript items.' );
    }

	public function getAllowedParams() {
        return array(
            'eventid' => array(
                ApiBase::PARAM_TYPE => 'string'
            ),
            'token' => array(
                ApiBase::PARAM_TYPE => 'string'
            )
            );
    }
    
    //TODO: create a more useful 'version number'
    public function getVersion() {
        return __CLASS__ . ': $Id: $';
    }
	
	
}