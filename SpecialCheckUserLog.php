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
		
		$type = $wgRequest->getVal( 'cuSearchType' );
		$target = $wgRequest->getVal( 'cuSearch' );
		$year = $wgRequest->getIntOrNull( 'year' );
		$month = $wgRequest->getIntOrNull( 'month' );
		$error = false;
		$dbr = wfGetDB( DB_SLAVE );
		$searchConds = false;

		$wgOut->setPageTitle( wfMsg( 'checkuser-log' ) );

		$wgOut->addHTML( $wgUser->getSkin()->makeKnownLinkObj( $this->getTitle(), wfMsgHtml( 'checkuser-log-return' ) ) );

		if ( $type === null ) {
			$type = 'target';
		} elseif ( $type == 'initiator' ) {
			$user = User::newFromName( $target );
			if ( !$user || !$user->getID() ) {
				$error = 'checkuser-user-nonexistent';
			} else {
				$searchConds = array( 'cul_user' => $user->getID() );
			}
		} else /* target */ {
			$type = 'target';
			// Is it an IP?
			list( $start, $end ) = IP::parseRange( $target );
			if ( $start !== false ) {
				if ( $start == $end ) {
					$searchConds = array( 'cul_target_hex = ' . $dbr->addQuotes( $start ) . ' OR ' .
						'(cul_range_end >= ' . $dbr->addQuotes( $start ) . ' AND ' .
						'cul_range_start <= ' . $dbr->addQuotes( $end ) . ')'
					);
				} else {
					$searchConds = array(
						'(cul_target_hex >= ' . $dbr->addQuotes( $start ) . ' AND ' .
						'cul_target_hex <= ' . $dbr->addQuotes( $end ) . ') OR ' .
						'(cul_range_end >= ' . $dbr->addQuotes( $start ) . ' AND ' .
						'cul_range_start <= ' . $dbr->addQuotes( $end ) . ')'
					);
				}
			} else {
				// Is it a user?
				$user = User::newFromName( $target );
				if ( $user && $user->getID() ) {
					$searchConds = array(
						'cul_type' => array( 'userips', 'useredits' ),
						'cul_target_id' => $user->getID(),
					);
				} elseif ( $target ) {
					$error = 'checkuser-user-nonexistent';
				}
			}
		}

		$searchTypes = array( 'initiator', 'target' );
		$select = "<select name=\"cuSearchType\" style='margin-top:.2em;'>\n";
		foreach ( $searchTypes as $searchType ) {
			if ( $type == $searchType ) {
				$checked = 'selected="selected"';
			} else {
				$checked = '';
			}
			$caption = wfMsgHtml( 'checkuser-search-' . $searchType );
			$select .= "<option value=\"$searchType\" $checked>$caption</option>\n";
		}
		$select .= '</select>';

		$encTarget = htmlspecialchars( $target );
		$msgSearch = wfMsgHtml( 'checkuser-search' );
		$input = "<input type=\"text\" name=\"cuSearch\" value=\"$encTarget\" size=\"40\"/>";
		$msgSearchForm = wfMsgHtml( 'checkuser-search-form', $select, $input );
		$formAction = $this->getTitle()->escapeLocalUrl();
		$msgSearchSubmit = '&#160;&#160;' . wfMsgHtml( 'checkuser-search-submit' ) . '&#160;&#160;';

		$s = "<form method='get' action=\"$formAction\">\n" .
			"<fieldset><legend>$msgSearch</legend>\n" .
			"<p>$msgSearchForm</p>\n" .
			"<p>" . $this->getDateMenu( $year, $month ) . "&#160;&#160;&#160;\n" .
			"<input type=\"submit\" name=\"cuSearchSubmit\" value=\"$msgSearchSubmit\"/></p>\n" .
			"</fieldset></form>\n";
		$wgOut->addHTML( $s );

		if ( $error !== false ) {
			$wgOut->addWikiText( '<div class="errorbox">' . wfMsg( $error ) . '</div>' );
			return;
		}

		$pager = new CheckUserLogPager( $this, $searchConds, $year, $month );
		$wgOut->addHTML(
			$pager->getNavigationBar() .
			$pager->getBody() .
			$pager->getNavigationBar()
		);
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

class CheckUserLogPager extends ReverseChronologicalPager {
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

		if ( $row->cul_type == 'userips' || $row->cul_type == 'useredits' ) {
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
