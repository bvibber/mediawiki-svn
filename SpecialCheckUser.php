<?php

class SpecialCheckUser extends SpecialPage {
	
	function __construct() {
		global $wgUser;
		
		parent::__construct( 'CheckUser', 'checkuser' );
	} 
	
	function execute( $subpage ) {
		global $wgRequest, $wgOut, $wgUser, $wgCheckUserForceSummary, $wgScriptPath;
	
		wfLoadExtensionMessages( 'CheckUser' ); 
		
		$this->setHeaders();
		$this->addStyles();
		
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
				$this->doUser2IP( $user, $reason, $period );
			} elseif ( $checktype == 'ip2user' ) {
				$this->doIP2User( $user, $reason, $period );
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
		
		$this->finishJS();
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
		
		$form = Xml::openElement( 'form', array( 'name' => 'checkuserform', 'id' => 'checkuserform', 'action' => $action, 'method' => 'get' ) ) .
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
			$this->getLimitMenu() .
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
	
	protected function addStyles() {
		global $wgScriptPath, $wgCheckUserStyleVersion, $wgCheckUserCSSVersion, $wgCheckUserJQueryVersion, $wgCheckUserPopupVersion, $wgOut;
		// FIXME, use Html::
		
		$jsPath = "$wgScriptPath/extensions/CheckUser/js/";
		
		$encJSFile = htmlspecialchars( "{$jsPath}checkuser.js?$wgCheckUserStyleVersion" );
		$wgOut->addScript( "<script type=\"text/javascript\" src=\"$encJSFile\"></script>" );
		
		$encJSFile = htmlspecialchars( "{$jsPath}jquery-1.3.2.min.js?$wgCheckUserJQueryVersion" );
		$wgOut->addScript( "<script type=\"text/javascript\" src=\"$encJSFile\"></script>" );
		
		$encJSFile = htmlspecialchars( "{$jsPath}jquery.ui-1.7.min.js?$wgCheckUserJQueryVersion" );
		$wgOut->addScript( "<script type=\"text/javascript\" src=\"$encJSFile\"></script>" );
		
		$encJSFile = htmlspecialchars( "{$jsPath}checkuser-popup.js?$wgCheckUserPopupVersion" );
		$wgOut->addScript( "<script type=\"text/javascript\" src=\"$encJSFile\"></script>" );
		
		$wgOut->addExtensionStyle( "{$wgScriptPath}/extensions/CheckUser/checkuser.css?" . $wgCheckUserCSSVersion );

	} 
	
	protected function finishJS() {
		global $wgOut;
		
		$out = <<<HTML
		<script>
		$('.mw-checkuser-menu').rb_menu({triggerEvent: 'click', hideOnLoad: true, loadHideDelay: 0, autoHide: true, transition: 'swing'});
		</script>
HTML;
		
		$wgOut->addHTML( $out );
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
	
	protected function getLimitMenu() {
		global $wgRequest;
		
		$currLimit = $wgRequest->getVal( 'limit' );
		
		$s = '<td>' . wfMsgHtml( 'checkuser-limit' ) . '</td>';
		$s .= '<td>' . Xml::openElement( 'select', array( 'name' => 'limit', 'id' => 'limit', 'style' => 'margin-top:.2em;' ) );
		
		foreach( array( 20, 50, 100, 250, 500, 5000 ) as $limit ) {
			$s .= Xml::option( $limit, $limit, $limit == $currLimit );
		}
		
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
		
		$pager = new CUTablePagerUser2IP( $result );
		
		$output =
		$pager->getNavigationBar() .
		$pager->getBody() .
		$pager->getNavigationBar();
		
		$wgOut->addHTML( $output ); 
	}
	
	function doIP2User( $ip, $reason, $period ) {
		global $wgOut;
		
		$checkuser = new CheckUser( $user );

		$result = $checkuser->doIP2User( array(
			'target' => $ip,
			'reason' => $reason,
			'period' => $period
		) );
		
		$pager = new CUTablePagerIP2User( $result );
		
		$output =
		$pager->getNavigationBar() .
		$pager->getBody() .
		$pager->getNavigationBar();
		
		$wgOut->addHTML( $output ); 
	}
	
	function doUser2Edits() {
	}
	
	function doIP2Edits() {
	}
	
	
	
	
}



