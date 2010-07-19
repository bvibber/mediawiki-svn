<?php

class CheckUser {

	var $target;
	
	function __construct( $target ) {
		$this->target = $target;
	}
	
	function doUser2IP( $params, $prop = array(), $limit = '' ) {

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
		if ( !$this->addLogEntry( 'user2ip', 'user', $user, $params['reason'], $user_id ) ) {
			$retArray['warn'] = 'checkuser-log-fail';
		}
		
		$dbr = wfGetDB( DB_SLAVE );
		$time_conds = $this->getTimeConds( $params['period'] );
		# Ordering by the latest timestamp makes a small filesort on the IP list
		$cu_changes = $dbr->tableName( 'cu_changes' );
		$use_index = $dbr->useIndexClause( 'cuc_user_ip_time' );
		
		$select = array(
			'cuc_ip',
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