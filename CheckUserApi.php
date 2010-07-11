<?php

class CheckUserApi extends ApiQueryBase { 

	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'cu' );
	} 
	
	public function execute( $resultPageSet = null ) {
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
		if ( !isset( $params['reason'] ) ) {
			$params['reason'] = '';
		}
		if ( !isset( $params['period'] ) ) {
			$params['period'] = 0;
		}
		
		$result = new CheckUser( $params['target'] );
		
		switch( $params['type'] ) {
			case "user2ip":
				$result = $result->doUser2IP( $params, $params['prop'], 'LIMIT ' . $params['limit'] );
				break;
			case "user2edits":
				$result = $result->doUser2Edits( $params['reason'], $params['period'] );
				break;
			case "ip2user":
				$result = $result->doIP2User( $params['reason'], $params['period'] );
				break;
			case "ip2edits":
				$result = $result->doIP2Edits( $params['reason'], $params['period'] );
				break;
			
		}		
		
		$this->getResult()->setIndexedTagName( $result, 'checkuser' );
		$this->getResult()->addValue( 'query', $this->getModuleName(), $result );

	}
	
	public function executeGenerator( $resultPageSet ) {
		$this->execute( $resultPageSet );
	}

	
	/*public function mustBePosted() {
		return true;
	}*/
	
	public function getAllowedParams() { 
		return array(
			'target' => array( 
				ApiBase::PARAM_TYPE => 'user',
			),
			'type' => array( 
				ApiBase::PARAM_TYPE => array( 'user2ip', 'user2edits', 'ip2user', 'ip2edits' ),
			),
			'reason' => 'string',
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
