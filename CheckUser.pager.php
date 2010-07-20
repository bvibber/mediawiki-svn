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
	
	function getCellAttrs( $field, $value ) {
		$retArr = array( 
			'class' => 'TablePager_col_' . $field,
			'style' => 'padding: 0.3em;'
		);
		
		$retArr = $this->fixCellAttrs( $field, $value, $retArr );
		
		return $retArr;
	}
	
	abstract function fixCellAttrs( $field, $value, $retArr );
	
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
					'<span class="mw-checkuser-menu"><span class="mw-checkuser-toggle">'. 
					wfMsgExt( 'checkuser-more', array( 'inline' ) ) .
					'</span><span class="mw-checkuser-items">'.
					wfMsgExt( 'checkuser-toollinks', array( 'parseinline' ), urlencode( $value ) ) . 
					'</span></span>';
				
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
	
	function fixCellAttrs( $field, $value, $retArr ) {
		
		switch($field ) {
			case 'cuc_ip':
				if( $this->mBlockInfo ) $retArr['style'] .= 'background-color: #FFCCCC;';
				break;
			case 'first':
				if( $value != $this->mCurrentRow->last ) break;
			case 'last':
				if( $value != $this->mCurrentRow->first ) break;
				$retArr['style'] .= 'background-color: #FFFFCC;';
				break;
			case 'cuc_agent':
				$retArr['width'] = '45%';
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
		);
		return $fields;
	} 

}

class CULogPager extends ReverseChronologicalPager {
	var $params, $specialPage;

	function __construct( $specialPage, $params, $year, $month ) {
		parent::__construct();
		
		$this->params = $params;
		$this->getDateCond( $year, $month );
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

		if ( in_array( $row->cul_type, array( 'user2ip', 'user2edits', 'userips', 'useredits' ) ) ) {
			$target = $skin->userLink( $row->cul_target_id, $row->cul_target_text ) .
				$skin->userToolLinks( $row->cul_target_id, $row->cul_target_text );
		} else {
			$target = $row->cul_target_text;
		}

		return '<li>' .
			$wgLang->timeanddate( wfTimestamp( TS_MW, $row->cul_timestamp ), true ) .
			wfMsg( 'comma-separator' ) .
			wfMsg(
				CULogPager::getLogEquiv( 'checkuser-log-' . $row->cul_type ),
				$user,
				$target,
				( $row->cul_api ) ? wfMsg( 'checkuser-using-api' ) : ''
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
		return $this->params;
	}

	function getIndexField() {
		return 'cul_timestamp';
	}

	function getTitle() {
		return $this->specialPage->getTitle();
	}
	
	static function getLogEquiv( $type ) {
			switch( $type ) {
				case 'checkuser-log-user2ip':
					return 'checkuser-log-userips';
				case 'checkuser-log-ip2edits':
					return 'checkuser-log-ipedits';
				case 'checkuser-log-ip2user':
					return 'checkuser-log-ipusers';
				case 'checkuser-log-ip2edits-xff':
					return 'checkuser-log-ipeditsxff';
				case 'checkuser-log-ip2user-xff':
					return 'checkuser-log-ipusersxff';
				case 'checkuser-log-user2edits':
					return 'checkuser-log-useredits';
				default:
					return $type;
			}
	}
}
