<?php

class CheckUser {

	var $target;
	var $api = false;
	
	function __construct( $target, $api = false ) {
		$this->target = $target;
		$this->api = $api;
	}
	
	function doUser2IP( $params, $prop = array(), $limit = '' ) {
		
		##FIXME: Make sure that the special page detects errors
		$userTitle = Title::newFromText( $this->target, NS_USER );
		if ( !is_null( $userTitle ) ) {
			// normalize the username
			$user = $this->target = $userTitle->getText();
		}
		
		# IPs are passed in as a blank string
		if ( !$user ) {
			return array( 'error' => 'nouserspecified'  );
		}
		
		# Get ID, works better than text as user may have been renamed
		$user_id = User::idFromName( $user );

		# If user is not IP or nonexistent
		if ( !$user_id ) {
			return array( 'error' => 'nosuchusershort' );
		}

		# Record check...
		if ( !$this->addLogEntry( 'userips', 'user', $user, $params['reason'], $user_id ) ) {
			$retArray['warn'] = 'checkuser-log-fail';
		}
		
		$dbr = wfGetDB( DB_SLAVE );
		$time_conds = $this->getTimeConds( $params['period'] );
		# Ordering by the latest timestamp makes a small filesort on the IP list
		$cu_changes = $dbr->tableName( 'cu_changes' );
		$use_index = $dbr->useIndexClause( 'cuc_user_ip_time' );
		
		$select = array(
			'cuc_ip',
			'cuc_ip_int',
			'cuc_ip_hex',
			'cuc_user',
			'cuc_agent',
			'cuc_rdns',
			'COUNT(*) AS count',
			'MIN(cuc_timestamp) AS first', 
			'MAX(cuc_timestamp) AS last'
		);
			
		$opts = array(
			'GROUP BY' => 'cuc_ip,cuc_ip_hex',
			'ORDER BY' => 'cuc_ip_int ASC',
			'USE INDEX' => 'cuc_user_ip_time'
		);
		
		if( !empty( $limit ) ) $opts['LIMIT'] = $limit;
		
		$ret = array(
			$cu_changes,
			$select,
			array(
				'cuc_user' => $user_id,
				$time_conds
			), 
			__METHOD__,
			$opts
		);
		
		return array( $ret, $time_conds );
		
	}
	
	function doUser2Edits() {
	}
	
	function doIP2User() {
	}
	
	function doIP2Edits() {
	}
	
	protected function addLogEntry( $logType, $targetType, $target, $reason, $targetID = 0 ) {
		global $wgUser;

		if ( $targetType == 'ip' ) {
			list( $rangeStart, $rangeEnd ) = IP::parseRange( $target );
			$targetHex = $rangeStart;
			if ( $rangeStart == $rangeEnd ) {
				$rangeStart = $rangeEnd = '';
			}
		} else {
			$targetHex = $rangeStart = $rangeEnd = '';
		}

		$dbw = wfGetDB( DB_MASTER );
		$cul_id = $dbw->nextSequenceValue( 'cu_log_cul_id_seq' );
		$dbw->insert( 'cu_log',
			array(
				'cul_id' => $cul_id,
				'cul_timestamp' => $dbw->timestamp(),
				'cul_user' => $wgUser->getID(),
				'cul_user_text' => $wgUser->getName(),
				'cul_reason' => $reason,
				'cul_type' => $logType,
				'cul_api' => intval( $this->api ),
				'cul_target_id' => $targetID,
				'cul_target_text' => $target,
				'cul_target_hex' => $targetHex,
				'cul_range_start' => $rangeStart,
				'cul_range_end' => $rangeEnd,
			), __METHOD__ );
		return true;
	}
	
	function getIPType() {
	}
	
	public static function checkBlockInfo( $name ) {
		$dbr = wfGetDB( DB_SLAVE );
		
		$ret = $dbr->selectRow(
			'ipblocks',
			array(
				'ipb_by_text',
				'ipb_reason',
				'ipb_timestamp',
				'ipb_expiry'
			),
			array(
				'ipb_address' => $name
			),
			__METHOD__
		);
		
		if( !is_null( $ret ) ) return $ret;
		
		return false;
	}
	
	public static function getAllEdits( $hex, $time_conds ) {
		$dbr = wfGetDB( DB_SLAVE );
		
		$ipedits = $dbr->estimateRowCount( 'cu_changes', '*',
			array( 'cuc_ip_hex' => $hex, $time_conds ),
			__METHOD__ );
		# If small enough, get a more accurate count
		if ( $ipedits <= 1000 ) {
			$ipedits = $dbr->selectField( 'cu_changes', 'COUNT(*)',
				array( 'cuc_ip_hex' => $hex, $time_conds ),
				__METHOD__ );
		}
		
		return $ipedits;
	}
	
	protected function getTimeConds( $period ) {
	
		if ( !$period ) {
			return '1 = 1';
		}
		
		$dbr = wfGetDB( DB_SLAVE );
		$cutoff_unixtime = time() - ( $period * 24 * 3600 );
		$cutoff_unixtime = $cutoff_unixtime - ( $cutoff_unixtime % 86400 );
		$cutoff = $dbr->addQuotes( $dbr->timestamp( $cutoff_unixtime ) );
		return "cuc_timestamp > $cutoff";
	}
	
}

class CheckUserLog {

	public static function getQuery( $initiator, $target, $year, $month, $expanded ) {
		
		$searchConds = array();
		
		if ( !is_null( $initiator ) ) {
			$user = User::newFromName( $target );
			if ( !$user || !$user->getID() ) {
				return array( 'error' =>  'nosuchusershort' );
			} else {
				$searchConds['cul_user'] = $user->getID();
			}
		}
	
		if( !is_null( $target ) ) {
			// Is it an IP?
			list( $start, $end ) = IP::parseRange( $target );
			if ( $start !== false ) {
				if ( $start == $end ) {
					$searchConds[] = 'cul_target_hex = ' . $dbr->addQuotes( $start ) . ' OR ' .
						'(cul_range_end >= ' . $dbr->addQuotes( $start ) . ' AND ' .
						'cul_range_start <= ' . $dbr->addQuotes( $end ) . ')';
				} else {
					$searchConds[] = 
						'(cul_target_hex >= ' . $dbr->addQuotes( $start ) . ' AND ' .
						'cul_target_hex <= ' . $dbr->addQuotes( $end ) . ') OR ' .
						'(cul_range_end >= ' . $dbr->addQuotes( $start ) . ' AND ' .
						'cul_range_start <= ' . $dbr->addQuotes( $end ) . ')';
				}
			} else {
				// Is it a user?
				$user = User::newFromName( $target );
				if ( $user && $user->getID() ) {
					$searchConds['cul_type'] = array( 'userips', 'useredits' );
					$searchConds['cul_target_id'] = $user->getID();
				} elseif ( $target ) {
					return array( 'error' =>  'nosuchusershort' );
				}
			}
		}
	
		$searchConds[] = 'user_id = cul_user';
		
		$ret = array(
			'tables' => array( 
				'cu_log', 
				'user' 
			),
			'fields' => array(
				'cul_id', 
				'cul_timestamp', 
				'cul_user', 
				'cul_reason', 
				'cul_type',
				'cul_target_id', 
				'cul_target_text', 
				'cul_api', 
				'user_name'
			),
			'conds'  => $searchConds,
			'options' => array()
		);
		
		##FIXME: How well will this work for renames?
		if( !$expanded ) {
			$ret['options']['GROUP BY'] = "DATE_FORMAT( cul_timestamp, '%Y%m%d%H' ), cul_user, cul_target_text, cul_type, cul_reason, cul_api";
		}
		
		return $ret;
	}
}