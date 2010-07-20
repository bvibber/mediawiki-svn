<?php

class CheckUserApi extends ApiQueryBase { 

	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'cu' );
	} 
	
	public function execute() {
		global $wgUser;
		
		$this->getMain()->setVaryCookie();
		
		// Before doing anything at all, let's check permissions
		if ( !$wgUser->isAllowed( 'checkuser' ) ) {
			$this->dieUsage( 'You don\'t have permission to use checkuser', 'permissiondenied' );
		}
		
		$params = $this->extractRequestParams();

		if ( !isset( $params['target'] ) ) {
			$this->dieUsageMsg( array( 'missingparam', 'cutarget' ) );
		}
		if ( !isset( $params['type'] ) ) {
			$this->dieUsageMsg( array( 'missingparam', 'cutype' ) );
		}
		if ( !isset( $params['period'] ) ) {
			$params['period'] = 0;
		}
		
		$endResult = array();
		
		foreach( $params['target'] as $target ) {
			$result = new CheckUser( $target, true );
			
			switch( $params['type'] ) {
				case "user2ip":
					$this->doUser2IP( $result, $params, $params['prop'], $params['limit'] );
					break;
				case "ip2user":
					$this->doIP2User( $result, $params, $params['prop'], $params['limit'] );
					break;
				
			}
		}		
	}
	
	public function doUser2IP( &$cuClass, $params, $prop, $limit ) {
		
		$dbParams = $cuClass->doUser2IP( $params, $prop, $limit );
		
		if( isset( $dbParams['error'] ) ) {
			$this->dieUsageMsg( array( $dbParams['error'] ) );
		}

		$this->addTables( $dbParams[0][0] );
		$this->addFields( $dbParams[0][1] );
		
		foreach( $dbParams[0][2] as $id => $cond ) {
			$this->addWhereFld( $id, $cond );
		}
		
		if( $params['dir'] == "ascending" ) {
			$params['dir'] = 'newer';
		}
		else {
			$params['dir'] = 'older';
		}
		
		$this->addWhereRange( 'cuc_ip', $params['dir'], $params['start'], $params['end'] );
		
		foreach( $dbParams[0][4] as $id => $opt ) {
			$this->addOption( $id, $opt );
		}
		
		$this->addOption( 'LIMIT', $params['limit'] + 1);
		
		$res = $this->select( __METHOD__ );
		
		$count = 0;
		foreach( $res as $id => $row ) {
			if ( ++$count > $params['limit'] ) {
				// We've had enough
				$this->setContinueEnumParameter( 'start', $row->cuc_ip );
				break;
			}
			
			$logEntry = array( 'ip' => $row->cuc_ip );
			
			if( in_array( 'count', $prop ) || is_null( $prop ) ) $logEntry['count'] = $row->count;
			if( in_array( 'first', $prop ) || is_null( $prop ) ) $logEntry['first'] = $row->first;
			if( in_array( 'last', $prop ) || is_null( $prop ) ) $logEntry['last'] = $row->last;
			if( in_array( 'hex', $prop ) || is_null( $prop ) ) $logEntry['hex'] = $row->cuc_ip_hex;
			if( in_array( 'agent', $prop ) || is_null( $prop ) ) $logEntry['agent'] = $row->cuc_agent;
			if( in_array( 'blockinfo', $prop ) || is_null( $prop ) ) {
				$blockinfo = CheckUser::checkBlockInfo( $row->cuc_ip );
				if( $blockinfo ) {
					$logEntry['blockinfo'] = array();
					$logEntry['blockinfo']['by'] = $blockinfo->ipb_by_text;
					$logEntry['blockinfo']['reason'] = $blockinfo->ipb_reason;
					$logEntry['blockinfo']['timestamp'] = $blockinfo->ipb_timestamp;
					$logEntry['blockinfo']['expiry'] = $blockinfo->ipb_expiry;
				}
			}
			if( in_array( 'alledits', $prop ) || is_null( $prop ) ) {
				$logEntry['alledits'] = CheckUser::getAllEdits( $row->cuc_ip_hex, $dbParams[1] );
			}
			if( in_array( 'rdns', $prop ) || is_null( $prop ) ) {
				if( empty( $row->rdns ) ) {
					$logEntry['rdns'] = gethostbyaddr( $row->cuc_ip );
				}
				else {
					$logEntry['rdns'] = $row->cuc_rdns;
				}
			}
			
			$fit = $this->getResult()->addValue( array( 'query', $this->getModuleName() ), null, $logEntry );
			if ( !$fit ) {
				$this->setContinueEnumParameter( 'start', $row->cuc_ip );
				break;
			}
		}

		$this->getResult()->setIndexedTagName_internal( array( 'query', $this->getModuleName() ), 'checkuser' );
	}

	
	/*public function mustBePosted() {
		return true;
	}*/
	
	public function getAllowedParams() { 
		return array(
			'target' => array( 
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_TYPE => 'user',
			),
			'type' => array( 
				ApiBase::PARAM_TYPE => array( 'user2ip', 'ip2user' ),
			),
			'reason' => array(
				ApiBase::PARAM_DFLT => '',
				ApiBase::PARAM_TYPE => 'string'
			),
			'period' => array(
				ApiBase::PARAM_DFLT => 0,
				ApiBase::PARAM_TYPE => 'integer'
			),
			'start' => array(
				ApiBase::PARAM_TYPE => 'integer'
			),
			'end' => array(
				ApiBase::PARAM_TYPE => 'integer'
			),
			'dir' => array(
				ApiBase::PARAM_DFLT => 'ascending',
				ApiBase::PARAM_TYPE => array(
					'ascending',
					'descending'
				)
			),
			'prop' => array(
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_DFLT => 'count|first|last|blockinfo|alledits|agent|rdns',
				ApiBase::PARAM_TYPE => array(
					'count',
					'first',
					'last',
					'hex',
					'blockinfo',
					'alledits',
					'agent',
					'rdns',
				)
			), 
			'limit' => array(
				ApiBase::PARAM_DFLT => 10,
				ApiBase::PARAM_TYPE => 'limit',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => ApiBase::LIMIT_BIG1,
				ApiBase::PARAM_MAX2 => ApiBase::LIMIT_BIG2
			), 
			'xff' => array(
				ApiBase::PARAM_DFLT => false,
				ApiBase::PARAM_TYPE => 'boolean',
			),
		); 
	}
	
	public function getParamDescription() { 
		return array(
			'target' => 'The IP or username to check',
			'type' => array(
				'Type of checkuser to run',
				' user2ip    - Get IPs used by a certain user',
				' ip2user    - Get usernames used by a certain IP'
			),
			'reason' => 'Reason for checking IP addresses',
			'period' => 'How many days back to check',
			'xff' => 'Show edits routed through the target IP using XFF (only for ip2user)',
			'start' => 'The IP or username to start enumerating from',
			'end' => 'The IP or username to stop enumerating at',
			'dir' => 'The direction in which to enumerate',
			'limit' => 'The maximum amount of IPs or usernames to list',
			'prop' => array(
				'Which properties to get',
				' count      - Adds how many edits the IP or username has',
				' first      - Adds first usage of the IP address or username',
				' last       - Adds last usage of the IP address or username',
				' hex        - Adds the hex-encoded IP address (only for user2ip)',
				' blockinfo  - If the IP or user is blocked, adds the block information',
				' alledits   - Counts all edits from the hex-encoded IP address (only for user2ip)',
				' agent      - Shows the user agent of the IP or username',
				' rdns       - Shows the Reverse DNS of the IP address (only for user2ip)',
			),
		);
	}
	
	public function getDescription() { 
		return 'Check users\' IP addresses and other information';
	}
	
	public function getPossibleErrors() { 
		return array_merge( parent::getPossibleErrors(), array(
			array( 'code' => 'permissiondenied', 'info' => 'You don\'t have permission to use checkuser' ),
			array( 'code' => 'cunocutarget', 'info' => 'The cutarget parameter must be set' ),
			array( 'code' => 'cunocutype', 'info' => 'The cutype parameter must be set' ),
			array( 'code' => 'cunosuchuser', 'info' => 'The user you specified doesn\'t exist' ),
			array( 'code' => 'cidrtoobroad', 'info' => 'CIDR ranges broader than /16 are not accepted' ),
		) );
	}
	
	public function getExamples() { 
		return array(
			'api.php?action=query&list=checkuser&cutarget=Example&cutype=user2ip',
			'api.php?action=query&list=checkuser&cutarget=127.0.0.1&cutype=ip2user&cuxff&cuperiod=50'
		);
	}
	
	public function getVersion() { 
		return __CLASS__ . ': $Id$';
	}
}
