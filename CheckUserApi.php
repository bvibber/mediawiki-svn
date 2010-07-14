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
				case "user2edits":
					$endResult[] = $result->doUser2Edits( $params['reason'], $params['period'] );
					break;
				case "ip2user":
					$endResult[] = $result->doIP2User( $params['reason'], $params['period'] );
					break;
				case "ip2edits":
					$endResult[] = $result->doIP2Edits( $params['reason'], $params['period'] );
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
		
		//$dbr->setFlag( DBO_DEBUG );
		
		$ret = $dbr->select( 
			$dbParams[0],
			$dbParams[1],
			$dbParams[2], 
			__METHOD__,
			$dbParams[3]
		);
		 
		if ( !$dbr->numRows( $ret ) ) {
			return $retArray;
		} else {
			$counter = 0;
			
			foreach( $ret as $id => $row ) {
				$retArray[$counter] = array( 'ip' => $row->cuc_ip );
				
				if( in_array( 'count', $prop ) || is_null( $prop ) ) $retArray[$counter]['count'] = $row->count;
				if( in_array( 'first', $prop ) || is_null( $prop ) ) $retArray[$counter]['first'] = $row->first;
				if( in_array( 'last', $prop ) || is_null( $prop ) ) $retArray[$counter]['last'] = $row->last;
				if( in_array( 'hex', $prop ) || is_null( $prop ) ) $retArray[$counter]['hex'] = $row->cuc_ip_hex;
				if( in_array( 'blockinfo', $prop ) || is_null( $prop ) ) {
					$blockinfo = CheckUser::checkBlockInfo( $row->cuc_ip );
					if( $blockinfo ) {
						$retArray[$counter]['blockinfo']['by'] = $blockinfo->ipb_by_text;
						$retArray[$counter]['blockinfo']['reason'] = $blockinfo->ipb_reason;
						$retArray[$counter]['blockinfo']['timestamp'] = $blockinfo->ipb_timestamp;
						$retArray[$counter]['blockinfo']['expiry'] = $blockinfo->ipb_expiry;
					}
				}
				
				$counter++;
				
			}
		}
		
		$dbr->freeResult( $ret );
		
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
				ApiBase::PARAM_TYPE => array( 'user2ip', 'user2edits', 'ip2user', 'ip2edits' ),
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
				ApiBase::PARAM_DFLT => 'count|first|last|blockinfo',
				ApiBase::PARAM_TYPE => array(
					'count',
					'first',
					'last',
					'hex',
					'blockinfo',
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
