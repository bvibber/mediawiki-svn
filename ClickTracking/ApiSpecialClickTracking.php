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
	public function execute(){
		
		$params = $this->extractRequestParams();
		$this->validateParams( $params );
		$event_id = $params['eventid'];
		$startdate = $params['startdate'];
		$enddate = $params['enddate'];
		$increment = $params['increment'];
		
		$click_data = array();
		try{
			$click_data = SpecialClickTracking::getChartData($event_id, $startdate, $enddate, $increment);
			$this->getResult()->addValue(array('datapoints'), 'expert', $click_data['expert']);
			$this->getResult()->addValue(array('datapoints'), 'basic', $click_data['basic']);
			$this->getResult()->addValue(array('datapoints'), 'intermediate', $click_data['intermediate']);
		}
		catch(Exception $e){ /* no result */   }
		
	}

	/**
	 * Required parameter check
	 * @param $params params extracted from the POST
	 */
 	protected function validateParams( $params ) {
		$required = array( 'eventid', 'startdate', 'enddate', 'increment' );
		foreach( $required as $arg ) {
			if ( !isset( $params[$arg] ) ) {
				$this->dieUsageMsg( array( 'missingparam', $arg ) );
			}
		}
		
		//check if event id parses to an int greater than zero
		if( (int) $params['eventid'] <= 0){
			$this->dieUsage("Invalid event ID", "badeventid"); 
		}
		
		//check start and end date are of proper format
		if(strptime(  $this->space_out_date($params['startdate']), "%Y %m %d") === false){
			$this->dieUsage("startdate not in YYYYMMDD format: <<{$params['startdate']}>>", "badstartdate");
		}
 		if(strptime( $this->space_out_date($params['enddate']), "%Y %m %d") === false){
			$this->dieUsage("enddate not in YYYYMMDD format", "badenddate");
		}
		
		//check if increment is a positive int
 		if( (int) $params['increment'] <= 0){
			$this->dieUsage("Invalid increment", "badincrement"); 
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
				ApiBase::PARAM_TYPE => 'integer'
			),
			'enddate' => array(
				ApiBase::PARAM_TYPE => 'integer'
			),
			'increment' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => 365 //1 year
			)
		);
	}

	// TODO: create a more useful 'version number'
	public function getVersion() {
		return __CLASS__ . ': $Id: $';
	}

}