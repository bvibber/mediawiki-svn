<?php

class SpecialCheckUserLog extends SpecialPage {
	
	function __construct() {
		global $wgUser;
		
		parent::__construct( 'CheckUserLog', 'checkuser-log' );
	} 
	
	function execute( $subpage ) {
		global $wgRequest, $wgOut, $wgUser, $wgCheckUserForceSummary;
	
		wfLoadExtensionMessages( 'CheckUserLog' ); 
		
		$this->setHeaders();
		
		if ( !$wgUser->isAllowed( 'checkuser-log' ) ) {
			$wgOut->permissionRequired( 'checkuser-log' );
			return;
		}
		
		$this->showLog();
	}
	
	protected function showLog() {
		global $wgRequest, $wgOut, $wgUser;
		
		$initiator = $wgRequest->getVal( 'initiator' );
		$target = $wgRequest->getVal( 'target' );
		$year = $wgRequest->getIntOrNull( 'year' );
		$month = $wgRequest->getIntOrNull( 'month' );
		$expanded = $wgRequest->getBool( 'expanded' );
		$xff = $wgRequest->getBool( 'xff' );
		
		$error = false;
		
		$dbr = wfGetDB( DB_SLAVE );

		$wgOut->setPageTitle( wfMsg( 'checkuser-log' ) );

		$wgOut->addHTML( $wgUser->getSkin()->makeKnownLinkObj( SpecialPage::getTitleFor( 'CheckUser', false ), wfMsgHtml( 'checkuser-log-return' ) ) );
		
		$this->doForm( $initiator, $target, $year, $month, $expanded, $xff );
		
		$queryParams = CheckUserLog::getQuery( $initiator, $target, $year, $month, $expanded, $xff );
		
		if ( isset( $queryParams['error'] ) ) {
			$wgOut->addWikiText( '<div class="errorbox">' . wfMsg( $error ) . '</div>' );
			return;
		}

		$pager = new CULogPager( $this, $queryParams, $year, $month );
		$wgOut->addHTML(
			$pager->getNavigationBar() .
			$pager->getBody() .
			$pager->getNavigationBar()
		);
	}
	
	protected function doForm( $initiator, $target, $year, $month, $expanded, $xff ) {
		global $wgOut, $wgUser;
		
		$action = $this->getTitle()->escapeLocalUrl();
		
		$form = Xml::openElement( 'form', array( 'name' => 'checkuserlogform', 'id' => 'checkuserlogform', 'action' => $action, 'method' => 'get' ) ) .
			Xml::openElement( 'fieldset' ) . Xml::openElement( 'legend' ) . wfMsgHtml( 'checkuser-search' ) . Xml::closeElement( 'legend') . 
			Xml::openElement( 'table', array( 'border' => 0, 'cellpadding' => 2 ) ) . Xml::openElement( 'tr' ) .
			Xml::openElement( 'td' ) . wfMsgHtml( 'checkuser-target' ) . Xml::closeElement( 'td' ) .
			Xml::openElement( 'td' ) . Xml::input( 'target', 46, $target, array( 'id' => 'checklogtarget' ) ) . 
			'&#160;&#160;'. Xml::check( 'xff', $xff ) . '&#160;' . Xml::label( wfMsg( 'checkuser-xff' ), 'xff' ) . Xml::closeElement( 'td' ) .
			
			Xml::closeElement( 'tr' ) . Xml::openElement( 'tr' ) .
			Xml::openElement( 'td' ) . wfMsgHtml( 'checkuser-initiator' ) . Xml::closeElement( 'td' ) .
			Xml::openElement( 'td' ) . Xml::input( 'initiator', 46, $initiator, array( 'id' => 'checkloginitiator' ) ) . Xml::closeElement( 'td' ) .
			Xml::closeElement( 'tr' ) . Xml::openElement( 'tr' ) .
			
			Xml::openElement( 'td', array( 'colspan' => 2 ) ) . $this->getDateMenu( $year, $month ) . Xml::closeElement( 'td' ) .
			
			Xml::closeElement( 'tr' ) . Xml::openElement( 'tr' ) .
			
			Xml::openElement( 'td', array( 'colspan' => 2 ) ) . 
			
			Xml::label( wfMsg( 'checkuser-expand' ), 'expanded' ) . ' ' . Xml::check( 'expanded', $expanded ) . 
			
			Xml::closeElement( 'td' ) .
			
			Xml::closeElement( 'tr' ) . Xml::openElement( 'tr' ) .

			Xml::openElement( 'td' ) . 
			Xml::submitButton( wfMsgHtml( 'checkuser-search-submit' ), array( 'id' => 'checkuserlogsubmit', 'name' => 'checkuserlogsubmit' ) ) .
			Xml::closeElement( 'td' ) . Xml::closeElement( 'tr' ) . Xml::closeElement( 'table' ) . Xml::closeElement( 'fieldset' ) .
			Xml::closeElement( 'form' );
			

		# Output form
		$wgOut->addHTML( $form );
	}
	
	/**
	 * @return string Formatted HTML
	 * @param int $year
	 * @param int $month
	 */
	protected function getDateMenu( $year, $month ) {
		# Offset overrides year/month selection
		if ( $month && $month !== - 1 ) {
			$encMonth = intval( $month );
		} else {
			$encMonth = '';
		}
		if ( $year ) {
			$encYear = intval( $year );
		} elseif ( $encMonth ) {
			$thisMonth = intval( gmdate( 'n' ) );
			$thisYear = intval( gmdate( 'Y' ) );
			if ( intval( $encMonth ) > $thisMonth ) {
				$thisYear--;
			}
			$encYear = $thisYear;
		} else {
			$encYear = '';
		}
		return Xml::label( wfMsg( 'year' ), 'year' ) . ' ' .
			Xml::input( 'year', 4, $encYear, array( 'id' => 'year', 'maxlength' => 4 ) ) .
			' ' .
			Xml::label( wfMsg( 'month' ), 'month' ) . ' ' .
			Xml::monthSelector( $encMonth, - 1 );
	}
	
}


