<?php
/**
 * Click tracking API module
 *
 * @file
 * @ingroup API
 */

class ApiClickTracking extends ApiBase {

	/**
	 * API clicktracking action
	 *
	 * Parameters:
	 * 		eventid: event name
	 * 		token: unique identifier for a user session
	 *
	 * @see includes/api/ApiBase#execute()
	 */
	public function execute() {
		global $wgUser, $wgTitle, $wgClickTrackContribGranularity1, $wgClickTrackContribGranularity2,
			$wgClickTrackContribGranularity3;

		$params = $this->extractRequestParams();
		$this->validateParams( $params );
		$eventid_to_lookup = $params['eventid'];
		$sessionId = $params['token'];

		$additional = null;

		if ( isset( $params['additional'] ) && strlen( $params['additional'] ) > 0 ) {
			$additional = $params['additional'];
		}

		// Event ID lookup table
		// FIXME: API should already have urldecode()d
		$eventId = ClickTrackingHooks::getEventIDFromName( urldecode( $eventid_to_lookup ) );

		$isLoggedIn = $wgUser->isLoggedIn();
		$now = time();
		$granularity1 = $isLoggedIn ?
			ClickTrackingHooks::getEditCountSince( $now - $wgClickTrackContribGranularity1 ) : 0;

		$granularity2 = $isLoggedIn ?
			ClickTrackingHooks::getEditCountSince( $now - $wgClickTrackContribGranularity2 ) : 0;

		$granularity3 = $isLoggedIn ?
			ClickTrackingHooks::getEditCountSince( $now - $wgClickTrackContribGranularity3 ) : 0;

		ClickTrackingHooks::trackEvent(
			$sessionId,  // randomly generated session ID
			$isLoggedIn, 						 // is the user logged in?
			$wgTitle->getNamespace(), 			 // what namespace are they editing?
			$eventId,							 // event ID passed in
			( $isLoggedIn ? $wgUser->getEditCount() : 0 ), // total edit count or 0 if anonymous
			$granularity1, // contributions made in granularity 1 time frame
			$granularity2, // contributions made in granularity 2 time frame
			$granularity3,  // contributions made in granularity 3 time frame
			$additional
		);

		// For links that go off the page, redirect the user
		// FIXME: The API should have a proper infrastructure for this
		if ( !is_null( $params['redirectto'] ) ) {
			// Validate the redirectto parameter
			// Must be a local URL, may not be protocol-relative
			// This validation rule is the same as the one in ClickTracking.js
			$href = $params['redirectto'];
			if ( strlen( $href ) > 0 && $href[0] == '/' && ( strlen( $href ) == 1 || $href[1] != '/' ) ) {
				global $wgOut;
				$wgOut->redirect( $params['redirectto'] );
				$wgOut->output();

				// Prevent any further output
				$wgOut->disable();
				$this->getMain()->getPrinter()->disable();
			} else {
				$this->dieUsage( 'The URL to redirect to must be domain-relative, i.e. start with a /', 'badurl' );
			}
		}
	}

	/**
	 * Required parameter check
	 * @param $params params extracted from the POST
	 */
	protected function validateParams( $params ) {
		$required = array( 'eventid', 'token' );
		foreach ( $required as $arg ) {
			if ( !isset( $params[$arg] ) ) {
				$this->dieUsageMsg( array( 'missingparam', $arg ) );
			}
		}
	}

	public function getParamDescription() {
		return array(
			'eventid' => 'string of eventID',
			'token'  => 'unique edit ID for this edit session',
			'redirectto' => 'URL to redirect to (only used for links that go off the page)',
			'additional' => 'additional info for the event, like state information'
		);
	}

	public function getDescription() {
		return array(
			'Track user clicks on JavaScript items.'
		);
	}

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'eventid' ),
			array( 'missingparam', 'token' ),
		) );
	}

	public function getAllowedParams() {
		return array(
			'eventid' => null,
			'token' => null,
			'redirectto' => null,
			'additional' => null
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
