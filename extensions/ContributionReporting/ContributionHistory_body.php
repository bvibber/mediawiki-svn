<?php
class ContributionHistory extends SpecialPage {
	function ContributionHistory() {
		SpecialPage::SpecialPage( 'ContributionHistory' );
	}

	function execute( $language = NULL ) {
		global $wgRequest, $wgOut;

		if ( !preg_match( '/^[a-z-]+$/', $language ) ) {
			$language = 'en';
		}
		$this->lang = Language::factory( $language );

		// Get request data
		$dir = $wgRequest->getText( 'dir', '' );
		$offset = $wgRequest->getText( 'offset' );
		$limit = $wgRequest->getText( 'limit', 50 );

		wfLoadExtensionMessages( 'ContributionReporting' );
		wfLoadExtensionMessages( 'ContributionReporting', $language );

		$this->setHeaders();

		$db = efContributionReportingConnection();

		$sql = 'SELECT * FROM public_reporting ORDER BY received DESC LIMIT ' . intval( $limit );

		$res = $db->query( $sql );

		$output = '<style type="text/css">';
		$output .= 'td {vertical-align: top; padding: 5px;}';
		$output .= 'td.left {padding-right: 10px;}';
		$output .= 'td.right {padding-left: 10px; text-align: right;}';
		$output .= 'td.alt {background-color: #DDDDDD;}';
		$output .= '</style>';

		$output .= '<table style="width: 100%">';
		$output .= '<tr>';
		$output .= '<th style="width: 200px;">' . $this->msg( 'contrib-hist-name' ) . '</th>';
		$output .= '<th>' . $this->msg( 'contrib-hist-date' ) . '</th>';
		$output .= '<th style="text-align: right;">' . $this->msg( 'contrib-hist-amount' ) . '</th>';
		$output .= '</tr>';

		$alt = TRUE;
		while ( $row = $res->fetchRow() ) {
			$name = $this->formatName( $row );

			$amount = $this->formatAmount( $row );
			$date = $this->formatDate( $row );

			$class = '';
			if ( $alt ) {
				$class = ' alt';
			}

			$output .= "<tr>";
			$output .= "<td class=\"left $class\">$name</td>";
			$output .= "<td class=\"left $class\" style=\"width: 100px;\">$date</td>";
			$output .= "<td class=\"right $class\" style=\"width: 75px;\">$amount</td>";
			$output .= "</tr>";

			$alt = !$alt;
		}

		$output .= '</table>';

		header( 'Cache-Control: max-age=300,s-maxage=300' );
		$wgOut->addWikiText( '{{Template:2008/Donate-header/' . $language . '}}' );
		$wgOut->addWikiText( '<skin>Tomas</skin>' );
		$wgOut->addHTML( '<h1>' . $this->msg( 'contrib-hist-header' ) . '</h1>' );
		$wgOut->addWikiText( '<strong>{{Template:2008/Contribution history introduction/' . $language . '}}</strong>' );
		$wgOut->addHTML( $output );
		$wgOut->addWikiText( '{{Template:2008/Donate-footer/' . $language . '}}' );
	}
	
	function msg( $key ) {
		return wfMsgExt( $key, array( 'escape', 'language' => $this->lang ) );
	}
	
	function formatName( $row ) {
		$name = htmlspecialchars( $row['name'] );
		if( !$name ) {
			$name = $this->msg( 'contrib-hist-anonymous' );
		}
		$name = '<strong>' . $name . '</strong>';
		
		if( $row['note'] && !$this->isTiny( $row ) ) {
			$name .= '<br />' . htmlspecialchars( $row['note'] );
		}

		return $name;
	}
	
	function isTiny( $row ) {
		$mins = array(
			'USD' => 1,
			'GBP' => 1, // $1.26
			'EUR' => 1, // $1.26
			'AUD' => 2, // $1.35
			'CAD' => 1, // $0.84
			'CHF' => 1, // $0.85
			'CZK' => 20, // $1.03
			'DKK' => 5, // $0.85
			'HKD' => 10, // $1.29
			'HUF' => 200, // $0.97
			'JPY' => 100, // $1
			'NZD' => 2, // $1.18
			'NOK' => 1, // $1.48
			'PLN' => 5, // $1.78
			'SGD' => 2, // $1.35
			'SEK' => 10, // $1.28
		);
		$currency = $row['original_currency'];
		if( $currency ) {
			$amount = $row['original_amount'];
		} else {
			$currency = 'USD';
			$amount = $row['converted_amount'];
		}
		if( isset( $mins[$currency] ) && $amount < $mins[$currency] ) {
			// Too small... don't show comments for 1-yen donations,
			// they tend to be disruptive.
			return true;
		} else {
			// dunno !
			return false;
		}
	}
	
	function formatDate( $row ) {
		$ts = wfTimestamp( TS_MW, $row['received'] );
		return $this->lang->timeanddate( $ts );
	}

	function formatAmount( $row ) {
		global $wgLang;
		$converted = $row['converted_amount'];
		
		if ( $row['original_currency'] ) {
			$currency = $row['original_currency'];
			$amount = $row['original_amount'];
		} else {
			$currency = 'USD';
			$amount = $converted;
		}

		if ( $currency == 'JPY' ) {
			// No decimals for yen
			$amount = intval( $amount );
		}

		return Xml::element( 'span',
			array( 'title' => "USD " . $this->lang->formatNum( $converted ) ),
			"$currency " . $this->lang->formatNum( $amount ) );
	}
}
