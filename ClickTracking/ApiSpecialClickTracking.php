<?php
/**
 * Extend the API for click tracking visualization in the special:clicktracking page
 *
 * @file
 * @ingroup API
 */

class ApiSpecialClickTracking extends ApiBase {

	/**
	 * runs when the API is called with "specialclicktracking", takes in "startdate" and "enddate" as YYYYMMDD , "eventid" as the event ID,
	 *     and "increment" as how many days to increment
	 * @see includes/api/ApiBase#execute()
	 */
	public function execute() {
		$params = $this->extractRequestParams();
		$this->validateParams( $params );
		$event_id = $params['eventid'];
		$startdate = $params['startdate'];
		$enddate = $params['enddate'];
		$increment = $params['increment'];
		$userDefString = $params['userdefs'];
		
		try {
			$click_data = SpecialClickTracking::getChartData( $event_id, $startdate, $enddate, $increment, $userDefString );
			$this->getResult()->addValue( null, 'datapoints', $click_data );
		} catch ( Exception $e ) { /* no result */ }
	}

	/**
	 * Required parameter check
	 * @param $params params extracted from the POST
	 */
 	protected function validateParams( $params ) {
		$required = array( 'eventid', 'startdate', 'enddate', 'increment', 'userdefs');
		foreach( $required as $arg ) {
			if ( !isset( $params[$arg] ) ) {
				$this->dieUsageMsg( array( 'missingparam', $arg ) );
			}
		}
		
		//check if increment is a positive int
 		if( (int) $params['increment'] <= 0){
			$this->dieUsage("Invalid increment", "badincrement"); 
		}
		
		if(json_decode($params['userdefs']) == null){
			$this->dieUsage("Invalid JSON encoding <<{$params['userdefs']}>>", "badjson");
		}
	}

	/**
	 * Space out the dates, 
	 * @param $datewithnospaces date with no spaces
	 * @return date with spaces
	 */
	public function space_out_date($datewithnospaces){
		return (substr($datewithnospaces, 0, 4) . " " .substr($datewithnospaces, 4, 2) . " " . substr($datewithnospaces, 6, 2));
	}
	
	public function getParamDescription() {
		return array(
			'eventid' => 'event ID (number)',
			'startdate'  => 'start date for data in YYYYMMDD format',
			'enddate' =>'end date for the data in YYYYMMDD format',
			'increment' => 'increment interval (in days) for data points',
			'userdefs' => 'JSON object to encode user definitions'
		);
	}

	public function getDescription() {
		return array(
			'Returns data to the special:clicktracking visualization page'
		);
	}

	public function getAllowedParams() {
		return array(
			'eventid' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_MIN => 1
			),
			'startdate' => array(
				ApiBase::PARAM_TYPE => 'timestamp'
			),
			'enddate' => array(
				ApiBase::PARAM_TYPE => 'timestamp'
			),
			'increment' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => 365 //1 year
			),
			'userdefs' => array (
				ApiBase::PARAM_TYPE => 'string'
			)
		);
	}

	// TODO: create a more useful 'version number'
	public function getVersion() {
		return __CLASS__ . ': $Id: $';
	}

}