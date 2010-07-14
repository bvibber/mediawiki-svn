<?php

class SpecialCheckUser extends SpecialPage {
	
	function __construct() {
		global $wgUser;
		
		parent::__construct( 'CheckUser', 'checkuser' );
		
		wfLoadExtensionMessages( 'CheckUser' );
	} 
	
	function execute( $subpage ) {
		global $wgRequest, $wgOut, $wgUser, $wgCheckUserForceSummary;
	
		wfLoadExtensionMessages( 'CheckUser' ); 
		
		$this->setHeaders();
		
		if ( !$wgUser->isAllowed( 'checkuser' ) ) {
			if ( $wgUser->isAllowed( 'checkuser-log' ) ) {
				$wgOut->addWikiText( wfMsg( 'checkuser-summary' ) .
					"\n\n[[" . SpecialPage::getTitleFor( 'CheckUserLog', false ) .
						'|' . wfMsg( 'checkuser-showlog' ) . ']]'
				);
				return;
			}

			$wgOut->permissionRequired( 'checkuser' );
			return;
		}
		
		$user = trim( $wgRequest->getText( 'user' ) );
		$reason = $wgRequest->getText( 'reason' );
		$blockreason = $wgRequest->getText( 'blockreason' );
		$checktype = $wgRequest->getVal( 'checktype' );
		$period = $wgRequest->getInt( 'period' );
		$users = $wgRequest->getArray( 'users' );
		$tag = $wgRequest->getBool( 'usetag' ) ? trim( $wgRequest->getVal( 'tag' ) ) : '';
		$talkTag = $wgRequest->getBool( 'usettag' ) ? trim( $wgRequest->getVal( 'talktag' ) ) : '';
		$xff = $wgRequest->getBool( 'xff' );
		
		$this->doForm( $user, $reason, $checktype, $xff, $period );
		# Perform one of the various submit operations...
		if ( $wgRequest->wasPosted() |1 ) { //FIXME
			if ( $wgRequest->getVal( 'action' ) === 'block' ) {
				$this->doMassUserBlock( $users, $blockreason, $tag, $talkTag );
			} elseif ( $wgCheckUserForceSummary && !strlen( $reason ) ) {
				$wgOut->addWikiMsg( 'checkuser-noreason' );
			} elseif ( $checktype == 'user2ip' ) {
				//$this->doUser2IP( $user, $reason, $period );
			} elseif ( $xff && $checktype == 'subipedits' ) {
				$this->doIPEditsRequest( $xff, true, $reason, $period );
			} elseif ( $checktype == 'subipedits' ) {
				$this->doIPEditsRequest( $ip, false, $reason, $period );
			} elseif ( $xff && $checktype == 'subipusers' ) {
				$this->doIPUsersRequest( $xff, true, $reason, $period, $tag, $talkTag );
			} elseif ( $checktype == 'subipusers' ) {
				$this->doIPUsersRequest( $ip, false, $reason, $period, $tag, $talkTag );
			} elseif ( $checktype == 'subuseredits' ) {
				$this->doUserEditsRequest( $user, $reason, $period );
			}
		}
		
		# Add CIDR calculation convenience form
		$this->addJsCIDRForm();
		$this->addStyles();
		
	}
	
	protected function doForm( $user, $reason, $checktype, $xff, $period ) {
		global $wgOut, $wgUser;
		
		$action = $this->getTitle()->escapeLocalUrl();
		
		# Fill in requested type if it makes sense
		$encipusers = $encipedits = $encuserips = $encuseredits = 0;
		if ( $checktype == 'ip2user' ) {
			$encipusers = 1;
		} elseif ( $checktype == 'ip2edits' ) {
			$encipedits = 1;
		} elseif ( $checktype == 'user2ip' ) {
			$encuserips = 1;
		} elseif ( $checktype == 'user2edits' ) {
			$encuseredits = 1;
		# Defaults otherwise
		} else {
			$encuserips = 1;
		}

		if ( $wgUser->isAllowed( 'checkuser-log' ) ) {
			$wgOut->addWikiText( wfMsg( 'checkuser-summary' ) .
				"\n\n[[" . SpecialPage::getTitleFor( 'CheckUserLog', false ) .
					'|' . wfMsg( 'checkuser-showlog' ) . ']]'
			);
		}
		
		$form = Xml::openElement( 'form', array( 'name' => 'checkuserform', 'id' => 'checkuserform', 'action' => $action, 'method' => 'post' ) ) .
			Xml::openElement( 'fieldset' ) . Xml::openElement( 'legend' ) . wfMsgHtml( 'checkuser-query' ) . Xml::closeElement( 'legend') . 
			Xml::openElement( 'table', array( 'border' => 0, 'cellpadding' => 2 ) ) . Xml::openElement( 'tr' ) .
			Xml::openElement( 'td' ) . wfMsgHtml( 'checkuser-target' ) . Xml::closeElement( 'td' ) .
			Xml::openElement( 'td' ) . Xml::input( 'user', 46, $user, array( 'id' => 'checktarget' ) ) . 
			'&#160;&#160;'. Xml::check( 'xff', $xff ) . '&#160;' . Xml::label( wfMsg( 'checkuser-xff' ), 'xff' ) . Xml::closeElement( 'td' ) .
			
			Xml::closeElement( 'tr' ) . Xml::openElement( 'tr' ) .
			
			Xml::openElement( 'td' ) . wfMsgHtml( 'checkuser-type' ) . Xml::closeElement( 'td' ) .
			Xml::openElement( 'td', array( 'class' => 'checkuserradios' ) ) . 
			Xml::openElement( 'table', array( 'border' => 0, 'cellpadding' => 3 ) ) . 
				Xml::openElement( 'tr' ) . 
				
				Xml::openElement( 'td' ) . Xml::radio( 'checktype', 'user2ip', $encuserips, array( 'id' => 'subuserips' ) ) . ' ' . 
					Xml::label( wfMsg( 'checkuser-ips' ), 'subuserips' ) . Xml::closeElement( 'td' ) .
				Xml::openElement( 'td' ) . Xml::radio( 'checktype', 'user2edits', $encuseredits, array( 'id' => 'subuseredits' ) ) . ' ' . 
					Xml::label( wfMsg( 'checkuser-account' ), 'subuseredits' ) . Xml::closeElement( 'td' ) .
				Xml::openElement( 'td' ) . Xml::radio( 'checktype', 'ip2user', $encipusers, array( 'id' => 'subipusers' ) ) . ' ' . 
					Xml::label( wfMsg( 'checkuser-users' ), 'subipusers' ) . Xml::closeElement( 'td' ) .
				Xml::openElement( 'td' ) . Xml::radio( 'checktype', 'ip2edits', $encipedits, array( 'id' => 'subipedits' ) ) . ' ' . 
					Xml::label( wfMsg( 'checkuser-edits' ), 'subipedits' ) . Xml::closeElement( 'td' ) .
			
			Xml::closeElement( 'tr' ) . Xml::closeElement( 'table' ) . Xml::closeElement( 'td' ) .
			Xml::closeElement( 'tr' ) . Xml::openElement( 'tr' ) .
			Xml::openElement( 'td' ) . wfMsgHtml( 'checkuser-reason' ) . Xml::closeElement( 'td' ) .
			Xml::openElement( 'td' ) . Xml::input( 'reason', 46, $reason, array( 'maxlength' => '255', 'id' => 'checkreason' ) ) . Xml::closeElement( 'td' ) .
			Xml::closeElement( 'tr' ) . Xml::openElement( 'tr' ) .
			$this->getPeriodMenu( $period ) .
			Xml::closeElement( 'tr' ) . Xml::openElement( 'tr' ) .
			Xml::openElement( 'td' ) . 
			Xml::submitButton( wfMsg( 'checkuser-check' ), array( 'id' => 'checkusersubmit', 'name' => 'checkusersubmit' ) ) .
			Xml::closeElement( 'td' ) . Xml::closeElement( 'tr' ) . Xml::closeElement( 'table' ) . Xml::closeElement( 'fieldset' ) .
			Xml::closeElement( 'form' );
			

		# Output form
		$wgOut->addHTML( $form );
	}
	
	/**
	 * As we use the same small set of messages in various methods and that
	 * they are called often, we call them once and save them in $this->message
	 */
	protected function preCacheMessages() {
		// Precache various messages
		if ( !isset( $this->message ) ) {
			foreach ( explode( ' ', 'diff hist minoreditletter newpageletter blocklink log' ) as $msg ) {
				$this->message[$msg] = wfMsgExt( $msg, array( 'escape' ) );
			}
		}
	}
	
	function getLogSubpageTitle() {
	}
	
	/**
	 * Add CSS/JS
	 */
	protected function addStyles() {
		global $wgScriptPath, $wgCheckUserStyleVersion, $wgOut;
		// FIXME, use Html::
		$encJSFile = htmlspecialchars( "$wgScriptPath/extensions/CheckUser/checkuser.js?$wgCheckUserStyleVersion" );
		$wgOut->addScript( "<script type=\"text/javascript\" src=\"$encJSFile\"></script>" );
	}
	
	/**
	 * Get a selector of time period options
	 * @param int $selected, selected level
	 */
	protected function getPeriodMenu( $selected = null ) {
		$s = '<td>' . wfMsgHtml( 'checkuser-period' ) . '</td>';
		$s .= '<td>' . Xml::openElement( 'select', array( 'name' => 'period', 'id' => 'period', 'style' => 'margin-top:.2em;' ) );
		$s .= Xml::option( wfMsg( 'checkuser-week-1' ), 7, $selected === 7 );
		$s .= Xml::option( wfMsg( 'checkuser-week-2' ), 14, $selected === 14 );
		$s .= Xml::option( wfMsg( 'checkuser-month' ), 31, $selected === 31 );
		$s .= Xml::option( wfMsg( 'checkuser-all' ), 0, $selected === 0 );
		$s .= Xml::closeElement( 'select' ) . "</td>\n";
		return $s;
	}
	
	/**
	 * Make a quick JS form for admins to calculate block ranges
	 */
	protected function addJsCIDRForm() {
		global $wgOut;
		
		$s = '<fieldset id="mw-checkuser-cidrform" style="display:none; clear:both;">' .
			'<legend>' . wfMsgHtml( 'checkuser-cidr-label' ) . '</legend>';
		$s .= '<textarea id="mw-checkuser-iplist" rows="5" cols="50" onkeyup="updateCIDRresult()" onclick="updateCIDRresult()"></textarea><br />';
		$s .= wfMsgHtml( 'checkuser-cidr-res' ) . '&#160;' .
			Xml::input( 'mw-checkuser-cidr-res', 35, '', array( 'id' => 'mw-checkuser-cidr-res' ) ) .
			'&#160;<strong id="mw-checkuser-ipnote"></strong>';
		$s .= '</fieldset>';
		
		$wgOut->addHTML( $s );
	}
	
	function doMassUserBlock() {
	}
	
	function noMatchesMessage() {
	}
	
	function doUser2IP( $user, $reason, $period ) {
		global $wgOut;
		
		$checkuser = new CheckUser( $user );

		$result = $checkuser->doUser2IP( array(
			'target' => $user,
			'reason' => $reason,
			'period' => $period
		) );
		
		$pager = new CUTablePager( $result );
		
		$output =
		$pager->getNavigationBar() .
		$pager->getBody() .
		$pager->getNavigationBar();
		
		$wgOut->addHTML( $output ); 
	}
	
	function doUser2Edits() {
	}
	
	function doIP2User() {
	}
	
	function doIP2Edits() {
	}
	
	
	
	
}

class CUTablePager extends TablePager { 

	private $mCUSelectParams;
	private $mBlockInfo;

	function __construct( $result ) {
		$this->mCUSelectParams = $result;
		
		parent::__construct(); 
	}
	
	function getQueryInfo() {
		$ret = array(
			'tables' => $this->mCUSelectParams[0],
			'fields' => $this->mCUSelectParams[1],
			'conds' => $this->mCUSelectParams[2],
			'options' => $this->mCUSelectParams[3]
		);
		
		if( isset( $ret['options']['ORDER BY'] ) ) {
			unset( $ret['options']['ORDER BY'] );
		}
		
		return $ret;
	}
	
	function getIndexField() {
		return 'cuc_ip';
	}
 
	//function formatRow( $row ) {
		//$title = Title::newFromDBkey( $row->cuc_ip );
		//$s = '<td><a href="' /* . $title->getFullURL()*/ . '">' . $row->cuc_ip . '</a></li>';
		//return $s;
	//}
	
	function isFieldSortable( $field ) {
		return true;
	} 
	
	function getDefaultSort() {
		return 'cuc_ip';
	} 
	
	function formatValue( $name, $value ) { 
		global $wgContLang;
		
		switch( $name ) {
			case 'cuc_ip':
				$value = '<a href="' .
					$this->getTitle()->escapeLocalURL( 'user=' . urlencode( $value ) . '&reason=' . urlencode( $reason ) ) . '">' .
					htmlspecialchars( $value ) . '</a>' .
					' (<a href="' . SpecialPage::getTitleFor( 'Blockip' )->escapeLocalURL( 'ip=' . urlencode( $value ) ) . '">' .
					wfMsgHtml( 'blocklink' ) . '</a>)<br /><small>' . 
					wfMsgExt( 'checkuser-toollinks', array( 'parseinline' ), urlencode( $value ) ) . '</small>';
				
				break;
			case 'count':
				$dbr = wfGetDB( DB_SLAVE );
				$dbr->setFlag( DBO_DEBUG );

				# If we get some results, it helps to know if the IP in general
				# has a lot more edits, e.g. "tip of the iceberg"...
				$ipedits = $dbr->estimateRowCount( 'cu_changes', '*',
					array( 'cuc_ip_hex' => $this->mCurrentRow->cuc_ip_hex, $this->mCUSelectParams[2][0] ),
					__METHOD__ );
				# If small enough, get a more accurate count
				if ( $ipedits <= 1000 ) {
					$ipedits = $dbr->selectField( 'cu_changes', 'COUNT(*)',
						array( 'cuc_ip_hex' => $this->mCurrentRow->cuc_ip_hex, $this->mCUSelectParams[2][0] ),
						__METHOD__ );
				}
				
				$value .= '<td>' . $ipedits . '</td>';
				break;
				
			case 'first':
				return $wgContLang->timeanddate( wfTimestamp( TS_MW, $value ), true );
				break;
			case 'last':
				$ret = $wgContLang->timeanddate( wfTimestamp( TS_MW, $value ), true );
				
				$ret .= '</td>';
				
				if( $this->mBlockInfo ) {
					$ret .= '<td style="background-color: #FFFFCC;">';
				}
				else {
					$ret .= '<td>';
				}
				
				$ret .= $this->fixBlockInfo( $this->mBlockInfo ) . '</td>';
				//Wow, that's hacky.
				
				return $ret;
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
		
		if( 
			( $field == 'first' && $value == $this->mCurrentRow->last ) || 
			( $field == 'last' && $value == $this->mCurrentRow->first )
		) {
			$retArr['style'] = 'background-color: #FFFFCC;';
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
	
	function getTitle() {
		return SpecialPage::getTitleFor( 'CheckUser', false );
	}
	
	function formatRow( $row ) {
		$this->mCurrentRow = $row;  	# In case formatValue etc need to know
		$this->mBlockInfo = CheckUser::checkBlockInfo( $this->mCurrentRow->cuc_ip );
		
		$s = Xml::openElement( 'tr', $this->getRowAttrs($row) );
		$fieldNames = $this->getFieldNames();
		foreach ( $fieldNames as $field => $name ) {
			if( $field == 'blockinfo' || $field == 'allusers' ) continue;
			$value = isset( $row->$field ) ? $row->$field : null;
			$formatted = strval( $this->formatValue( $field, $value ) );
			if ( $formatted == '' ) {
				$formatted = '&#160;';
			}
			$s .= Xml::tags( 'td', $this->getCellAttrs( $field, $value ), $formatted );
		}
		$s .= "</tr>\n";
		return $s;
	}

}
