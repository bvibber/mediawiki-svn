<?php

abstract class CUTablePager extends TablePager {

	protected $mCUSelectParams;
	protected $mTimeConds;
	protected $mBlockInfo = false;

	function __construct( $result ) {
		$this->mCUSelectParams = $result[0];
		$this->mTimeConds = $result[1];
		
		parent::__construct(); 
	}
	
	function getQueryInfo() {
		$ret = array(
			'tables' => $this->mCUSelectParams[0],
			'fields' => $this->mCUSelectParams[1],
			'conds' => $this->mCUSelectParams[2],
			'options' => $this->mCUSelectParams[4]
		);
		
		if( isset( $ret['options']['ORDER BY'] ) ) {
			unset( $ret['options']['ORDER BY'] );
		}
		
		$this->addMoreQueryInfo( $ret );
		
		return $ret;
	}
	
	abstract function addMoreQueryInfo( &$query );
	
	function isFieldSortable( $field ) {
		return true;
	} 
	
	function getTitle() {
		return SpecialPage::getTitleFor( 'CheckUser', false );
	}
	
}

class CUTablePagerUser2IP extends CUTablePager { 

	protected $mBlockInfo = false;
	
	function addMoreQueryInfo( &$query ) {
		$query['fields'][] = $query['fields'][0] . ' AS allusers';
		$query['fields'][] = $query['fields'][0] . ' AS blockinfo';
		$query['fields'][] = 'cuc_ip_int';
	}
	
	function getIndexField() {
		return 'cuc_ip_int';
	}
 
	
	function getDefaultSort() {
		return 'cuc_ip';
	} 
	
	function formatValue( $name, $value ) { 
		global $wgContLang;
		
		switch( $name ) {
			case 'cuc_ip':
				$this->mBlockInfo = CheckUser::checkBlockInfo( $this->mCurrentRow->cuc_ip );
				$value = '<a href="' .
					$this->getTitle()->escapeLocalURL( 'user=' . urlencode( $value ) . '&reason=' . urlencode( $reason ) ) . '">' .
					htmlspecialchars( $value ) . '</a>' .
					' (<a href="' . SpecialPage::getTitleFor( 'Blockip' )->escapeLocalURL( 'ip=' . urlencode( $value ) ) . '">' .
					wfMsgHtml( 'blocklink' ) . '</a>)<br /><small>' . 
					wfMsgExt( 'checkuser-toollinks', array( 'parseinline' ), urlencode( $value ) ) . '</small>';
				
				break;
			case 'allusers':
				$dbr = wfGetDB( DB_SLAVE );
				$dbr->setFlag( DBO_DEBUG );

				# If we get some results, it helps to know if the IP in general
				# has a lot more edits, e.g. "tip of the iceberg"...
				$ipedits = $dbr->estimateRowCount( 'cu_changes', '*',
					array( 'cuc_ip_hex' => $this->mCurrentRow->cuc_ip_hex, $this->mTimeConds ),
					__METHOD__ );
				# If small enough, get a more accurate count
				if ( $ipedits <= 1000 ) {
					$ipedits = $dbr->selectField( 'cu_changes', 'COUNT(*)',
						array( 'cuc_ip_hex' => $this->mCurrentRow->cuc_ip_hex, $this->mTimeConds ),
						__METHOD__ );
				}
				
				return $ipedits;
				break;
				
			case 'first':
				return $wgContLang->timeanddate( wfTimestamp( TS_MW, $value ), true );
				break;
			case 'last':
				return $wgContLang->timeanddate( wfTimestamp( TS_MW, $value ), true );
				break;
			case 'cuc_agent':
				return "<small>$value</small>";
			case 'blockinfo':
				if( $this->mBlockInfo ) {
					return $this->fixBlockInfo( $this->mBlockInfo );
				}
				
				return '';
				break;
			case 'cuc_rdns':
				if( empty( $this->mCurrentRow->rdns ) ) {
					$value = gethostbyaddr( $this->mCurrentRow->cuc_ip );
				}
		}
		
		return $value;
	}
	
	function fixBlockInfo( $info ) {
		global $wgContLang;
		
		$this->mBlockInfo = $info;
		
		if( !$info ) return '';

		$expirydate = wfMsg( 'checkuser-expires' ) . ' ' . $wgContLang->timeanddate( wfTimestamp( TS_MW, $info->ipb_expiry ), true );
		
		if( !is_numeric( $info->ipb_expiry ) ) {
			$expirydate = '';
		}
		
		return wfMsgExt( 'checkuser-blockedby', 'parseinline', $info->ipb_by_text, $info->ipb_reason, $wgContLang->timeanddate( wfTimestamp( TS_MW, $info->ipb_timestamp ), true ), $expirydate );
	}
	
	function getCellAttrs( $field, $value ) {
		$retArr = array( 'class' => 'TablePager_col_' . $field );
		
		switch($field ) {
			case 'first':
				if( $value != $this->mCurrentRow->last ) break;
			case 'last':
				if( $value != $this->mCurrentRow->first ) break;
				$retArr['style'] = 'background-color: #FFFFCC;';
				break;
			case 'blockinfo':
				if( $this->mBlockInfo ) {
					$retArr['style'] = 'background-color: #FFFFCC;';
					$retArr['width'] = '25%';
				}
				break;
			case 'cuc_agent':
				$retArr['width'] = '20%';
				break;
		}
		
		return $retArr;
	}
	
	function getFieldNames() {
		$fields = array(
			$this->getDefaultSort() => wfMsg( 'checkuser-cuc_ip' ),
			'cuc_rdns' => wfMsg( 'checkuser-cuc_rdns' ),
			'count' => wfMsg( 'checkuser-count' ),
			'allusers' => wfMsg( 'checkuser-allusers' ),
			'first' => wfMsg( 'checkuser-first' ),
			'last' => wfMsg( 'checkuser-last' ),
			'cuc_agent' => wfMsg( 'checkuser-cuc_agent' ),
			'blockinfo' => wfMsg( 'checkuser-blockinfo' ),
		);
		return $fields;
	} 

}

class CULogPager extends ReverseChronologicalPager {
	var $searchConds, $specialPage, $y, $m;

	function __construct( $specialPage, $searchConds, $y, $m ) {
		parent::__construct();
		/*
		$this->messages = array_map( 'wfMsg',
			array( 'comma-separator', 'checkuser-log-userips', 'checkuser-log-ipedits', 'checkuser-log-ipusers',
			'checkuser-log-ipedits-xff', 'checkuser-log-ipusers-xff' ) );*/

		$this->getDateCond( $y, $m );
		$this->searchConds = $searchConds ? $searchConds : array();
		$this->specialPage = $specialPage;
	}

	function formatRow( $row ) {
		global $wgLang;

		$skin = $this->getSkin();

		if ( $row->cul_reason === '' ) {
			$comment = '';
		} else {
			$comment = $skin->commentBlock( $row->cul_reason );
		}

		$user = $skin->userLink( $row->cul_user, $row->user_name );

		if ( $row->cul_type == 'user2ip' || $row->cul_type == 'user2edits' ) {
			$target = $skin->userLink( $row->cul_target_id, $row->cul_target_text ) .
				$skin->userToolLinks( $row->cul_target_id, $row->cul_target_text );
		} else {
			$target = $row->cul_target_text;
		}

		return '<li>' .
			$wgLang->timeanddate( wfTimestamp( TS_MW, $row->cul_timestamp ), true ) .
			wfMsg( 'comma-separator' ) .
			wfMsg(
				'checkuser-log-' . $row->cul_type,
				$user,
				$target
			) .
			$comment .
			'</li>';
	}

	function getStartBody() {
		if ( $this->getNumRows() ) {
			return '<ul>';
		} else {
			return '';
		}
	}

	function getEndBody() {
		if ( $this->getNumRows() ) {
			return '</ul>';
		} else {
			return '';
		}
	}

	function getEmptyBody() {
		return '<p>' . wfMsgHtml( 'checkuser-empty' ) . '</p>';
	}

	function getQueryInfo() {
		$this->searchConds[] = 'user_id = cul_user';
		return array(
			'tables' => array( 'cu_log', 'user' ),
			'fields' => $this->selectFields(),
			'conds'  => $this->searchConds
		);
	}

	function getIndexField() {
		return 'cul_timestamp';
	}

	function getTitle() {
		return $this->specialPage->getTitle();
	}

	function selectFields() {
		return array(
			'cul_id', 'cul_timestamp', 'cul_user', 'cul_reason', 'cul_type',
			'cul_target_id', 'cul_target_text', 'user_name'
		);
	}
}
