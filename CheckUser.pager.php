<?php

abstract class CUTablePager extends TablePager {

	public $mLimitsShown;
	public $mTimeConds;

	function __construct( $result = array(), $index = 'cuc_ip' ) {
		parent::__construct();
		
		$this->mIndexField = $index;
		$this->mResult = $result[0];
		$this->mTimeConds = $result[1];
		
	}

	/* This function normally does a database query to get the results; we need
	 * to make a pretend result using a FakeResultWrapper.
	 */
	function reallyDoQuery( $offset, $limit, $descending ) {
		global $wgRequest;
		
		$result = array();
		
		$index = ( $wgRequest->getVal( 'sort' ) ) ? $wgRequest->getVal( 'sort' ) : $this->mIndexField;

		if ( $descending ) {
			$operator = '>';
			
			$obj = new CUSortArray( 'DESC', $index );
		} else {
			$operator = '<';
			
			$obj = new CUSortArray( 'ASC', $index );
		}
		
		$forNow = array();
		
		foreach( $this->mResult as $row ) {
			$row = (array) $row;
			
			$forNow[] = $this->fixRowResult( $row );
		}
		
		$this->mResult = $forNow;
		
		usort( $this->mResult, array( $obj, 'run' ) );

		$count = 0;
		foreach( $this->mResult as $res ) {
			
			if ( $offset != '' ) {
				if ( $descending ) {
					if( $res[$this->mIndexField] > $offset ) continue;
				} else {
					if( $res[$this->mIndexField] < $offset ) continue;
				}
				
			}
			
			$result[] = $res;
			
			$count++;
			
			if( $count == $limit ) break;
		}
		
		return new FakeResultWrapper( $result );
	}
	
	abstract function fixRowResult( $row );
	
	function getTitle() {
		return SpecialPage::getTitleFor( 'CheckUser', false );
	}
	
	function isFieldSortable( $field ) {
		return true;
	}
	
	function getQueryInfo() {
		return '';
	}
	
}

class CUTablePagerUser2IP extends CUTablePager {

	function fixRowResult( $row ) {
		$row['allusers'] = CheckUser::getAllEdits( $row['cuc_ip_hex'], $this->mTimeConds );
			
		$blockinfo = CheckUser::checkBlockInfo( $row['cuc_ip'] );
		$row['blockinfo'] = $blockinfo;
		
		if( $blockinfo ) {
			$row['blockinfo'] = array();
			
			$row['blockinfo']['by'] = $blockinfo->ipb_by_text;
			$row['blockinfo']['reason'] = $blockinfo->ipb_reason;
			$row['blockinfo']['timestamp'] = $blockinfo->ipb_timestamp;
			$row['blockinfo']['expiry'] = $blockinfo->ipb_expiry;
		}
		
		return $row;
	}
	
	function formatValue( $field, $value ) {
		global $wgContLang;
		
		switch( $field ) {
			case 'cuc_ip':
				return '<a href="' .
					$this->getTitle()->escapeLocalURL( 'user=' . urlencode( $value ) . '&reason=' . urlencode( $reason ) ) . '">' .
					htmlspecialchars( $value ) . '</a>' .
					' (<a href="' . SpecialPage::getTitleFor( 'Blockip' )->escapeLocalURL( 'ip=' . urlencode( $value ) ) . '">' .
					wfMsgHtml( 'blocklink' ) . '</a>)<br /><small>' . 
					wfMsgExt( 'checkuser-toollinks', array( 'parseinline' ), urlencode( $value ) ) . '</small>';
				
				break;
			case 'first':
				return $wgContLang->timeanddate( wfTimestamp( TS_MW, $value ), true );
				break;
			case 'last':
				return $wgContLang->timeanddate( wfTimestamp( TS_MW, $value ), true );
				break;
			case 'blockinfo':
				return $this->fixBlockInfo( $value );
				break;
		}
		
		return $value;
	}
	
	private function fixBlockInfo( $value ) {
		global $wgContLang;
		
		if( !$value ) return '';

		$expirydate = wfMsg( 'checkuser-expires' ) . ' ' . $wgContLang->timeanddate( wfTimestamp( TS_MW, $value['expiry'] ), true );
		
		if( !is_numeric( $value['expiry'] ) ) {
			$expirydate = '';
		}
		
		return wfMsgExt( 'checkuser-blockedby', 'parseinline', $value['by'], $value['reason'], $wgContLang->timeanddate( wfTimestamp( TS_MW, $value['timestamp'] ), true ), $expirydate );
	}
	
	function getCellAttrs( $field, $value ) {
		$retArr = array( 'class' => 'TablePager_col_' . $field );
		
		if( 
			( $field == 'first' && $value == $this->mCurrentRow->last ) || 
			( $field == 'last' && $value == $this->mCurrentRow->first ) ||
			( $field == 'blockinfo' && !empty( $value ) ) 
		) {
			$retArr['style'] = 'background-color: #FFFFCC;';
			
			if( $field == 'blockinfo' ) {
				$retArr['style'] .= 'width: 33%;';
			}
		}
		
		return $retArr;
	}
	
	function getFieldNames() {
		$fields = array(
			$this->getDefaultSort() => wfMsg( 'checkuser-cuc_ip' ),
			'count' => wfMsg( 'checkuser-count' ),
			'allusers' => wfMsg( 'checkuser-allusers' ),
			'first' => wfMsg( 'checkuser-first' ),
			'last' => wfMsg( 'checkuser-last' ),
			'blockinfo' => wfMsg( 'checkuser-blockinfo' ),
		);
		return $fields;
	} 
	
	function getDefaultSort() {
		return 'cuc_ip';
	}
	
}

class CUSortArray {

	var $dir, $index;

	function __construct( $dir, $index ) {
		$this->dir = $dir;
		$this->index = $index;
	}
	
	function run( $old, $new ) {
		if( $this->index == "blockinfo" ) {	

			if( $this->dir == "DESC" ) {
				return $this->runeval( $old, $new );
			}
			else {
				return $this->runeval( $new, $old );
			}
		}
		else {
			if( $this->dir == "DESC" ) {
				 return strnatcmp( $old[$this->index], $new[$this->index] );
			}
			else {
				return strnatcmp( $new[$this->index], $old[$this->index] );
			}
		}
	}
	
	function runeval( $old, $new ) {
		$oper = ( $this->dir == "DESC" ) ? -1 : 1;
		
		if( !isset( $old[$this->index]['timestamp'] ) ) {
			return -1;
		}
		elseif( !isset( $new[$this->index]['timestamp'] ) ) {
			return 1;
		}
		else {
			return $oper * strnatcmp( $old[$this->index]['timestamp'], $new[$this->index]['timestamp'] );
		}
	}
}