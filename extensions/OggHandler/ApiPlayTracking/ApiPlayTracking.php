<?php
/**
 * Extend the API for play request tracking
 *
 * @file
 * @ingroup API
 */

class ApiPlayTracking extends ApiBase {

	/**
	 * Runs when the API is called with "playtracking", takes in "filename"
	 * and "client" serialized data for ogg support analysis
	 *
	 * @see includes/api/ApiBase#execute()
	 */
	public function execute() {
		global $wgEnablePlayTracking;
		if( ! $wgEnablePlayTracking ){
			$this->dieUsageMsg( array( 'unknownerror', 'Play tracking is not enabled' ) );
		}
		$params = $this->extractRequestParams();
		$this->validateParams( $params );

		// insert into the play_tracking table
	}

	/**
	 * Required parameter check
	 * @param $params params extracted from the POST
	 */
 	protected function validateParams( $params ) {
		$required = array( 'filename', 'client' );
		foreach ( $required as $arg ) {
			if ( !isset( $params[$arg] ) ) {
				$this->dieUsageMsg( array( 'missingparam', $arg ) );
			}
		}
	}

	/**
	* Setup the ApiTracking tables
	*/
	public static function schema() {
		global $wgExtNewTables, $wgExtNewIndexes;

		$wgExtNewTables[] = array(
			'play_tracking',
			dirname( __FILE__ ) . '/ApiPlayTracking.sql'
		);

		return true;
	}

	public function getParamDescription() {
		return array(
			'filename' => 'title of filename played',
			'client'  => 'seralized data about client playback support',
		);
	}

	public function getDescription() {
		return array(
			'Track user audio and video play requests.'
		);
	}

	public function getAllowedParams() {
		return array(
			'filename' => null,
			'client' => null,
		);
	}
	public function getVersion() {
		return __CLASS__ . ': $Id: ApiPlayTracking.php 59374 2009-11-24 01:06:56Z dale $';
	}
}

?>