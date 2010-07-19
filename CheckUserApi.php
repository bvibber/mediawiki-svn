<?php

class CheckUserApi extends ApiQueryBase { 

	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'cu' );
	} 
	
	public function execute() {
		global $wgUser;
		
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
			$result = new CheckUser( $target );
			
			switch( $params['type'] ) {
				case "user2ip":
					$ips = $this->doUser2IP( $result, $params, $params['prop'], $params['limit'] );
					$endResult[] = array( 'target' => $result->target, 'ips' => $ips );
					break;
				case "ip2user":
					$users = $this->doIP2User( $result, $params, $params['prop'], $params['limit'] );
					$endResult[] = array( 'target' => $result->target, 'users' => $users );
					break;
				
			}
		}		
		
		$this->getResult()->setIndexedTagName( $result, 'checkuser' );
		$this->getResult()->addValue( 'query', $this->getModuleName(), $endResult );

	}
	
	public function doUser2IP( &$cuClass, $params, $prop, $limit ) {
		
		$dbParams = $cuClass->doUser2IP( $params, $prop, $limit );
		
		$retArray = array();

		$dbr = wfGetDB( DB_SLAVE );
		
		$ret = $dbr->select( 
			$dbParams[0][0],
			$dbParams[0][1],
			$dbParams[0][2], 
			__METHOD__,
			$dbParams[0][4]
		);
		 
		if ( !$dbr->numRows( $ret ) ) {
			return $retArray;
		} else {
			$counter = 0;
				
			foreach( $ret as $row ) {
				$retArray[$counter] = array( 'ip' => $row->cuc_ip );
				
				if( in_array( 'count', $prop ) || is_null( $prop ) ) $retArray[$counter]['count'] = $row->count;
				if( in_array( 'first', $prop ) || is_null( $prop ) ) $retArray[$counter]['first'] = $row->first;
				if( in_array( 'last', $prop ) || is_null( $prop ) ) $retArray[$counter]['last'] = $row->last;
				if( in_array( 'hex', $prop ) || is_null( $prop ) ) $retArray[$counter]['hex'] = $row->cuc_ip_hex;
				if( in_array( 'agent', $prop ) || is_null( $prop ) ) $retArray[$counter]['agent'] = $row->cuc_agent;
				if( in_array( 'blockinfo', $prop ) || is_null( $prop ) ) {
					$blockinfo = CheckUser::checkBlockInfo( $row->cuc_ip );
					if( $blockinfo ) {
						$retArray[$counter]['blockinfo'] = array();
						$retArray[$counter]['blockinfo']['by'] = $blockinfo->ipb_by_text;
						$retArray[$counter]['blockinfo']['reason'] = $blockinfo->ipb_reason;
						$retArray[$counter]['blockinfo']['timestamp'] = $blockinfo->ipb_timestamp;
						$retArray[$counter]['blockinfo']['expiry'] = $blockinfo->ipb_expiry;
					}
				}
				if( in_array( 'alledits', $prop ) || is_null( $prop ) ) {
					$retArray[$counter]['alledits'] = CheckUser::getAllEdits( $row->cuc_ip_hex, $dbParams[1] );
				}
				if( in_array( 'rdns', $prop ) || is_null( $prop ) ) {
					if( empty( $row->rdns ) ) {
						$retArray[$counter]['rdns'] = gethostbyaddr( $row->cuc_ip );
					}
					else {
						$retArray[$counter]['rdns'] = $row->cuc_rdns;
					}
				}
				
				$counter++;
				
			}
		}

		return $retArray;
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
				ApiBase::PARAM_DFLT => 30,
				ApiBase::PARAM_TYPE => 'integer'
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
				ApiBase::PARAM_TYPE => 'boolean',
			),
		); 
	}
	
	public function getParamDescription() { 
	}
	
	public function getDescription() { 
	}
	
	public function getPossibleErrors() { 
	}
	
	public function getExamples() { 
	}
	
	public function getVersion() { 
	}
}
