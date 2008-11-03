<?php

class ContributionTracking extends SpecialPage {
	function ContributionTracking() {
		SpecialPage::SpecialPage( 'ContributionTracking' );
	}

	function execute( $par ) {
		global $wgRequest, $wgOut;

		$this->setHeaders();

		$db = contributionTrackingConnection();

		$tracked_contribution = array(
		'note' => $wgRequest->getText( 'comment', NULL ),
		'referrer' => $wgRequest->getText( 'referrer', NULL ),
		'anonymous' => ( $wgRequest->getInt( 'comment', 0 ) ? 0 : 1 ),
		'utm_source' => $wgRequest->getText( 'utm_source', NULL ),
		'utm_medium' => $wgRequest->getText( 'utm_medium', NULL ),
		'utm_campaign' => $wgRequest->getText( 'utm_campaign', NULL ),
		'optout' => ( $wgRequest->getInt( 'email', 0 ) ? 0 : 1 ),
		'language' => $wgRequest->getText( 'language', NULL ),
		);

		// Make all empty strings NULL
		foreach ( $tracked_contribution as $key => $value ) {
			if ( $value === '' ) {
				$tracked_contribution[$key] = NULL;
			}
		}

		// Store the contribution data
		$db->insert( 'contribution_tracking', $tracked_contribution );

		$contribution_tracking_id = $db->insertId();

		$values = $wgRequest->getValues();

		// Set the action and tracking ID fields
		$action = 'http://wikimediafoundation.org/';
		$tracking = '';
		if ( $values['gateway'] == 'paypal' ) {
			$action = 'https://www.paypal.com/cgi-bin/webscr';
			$tracking = '<input type="hidden" name="on0" value="contribution_tracking_id" />';
		}
		else if ( $values['gateway'] == 'moneybookers' ) {
			$action = 'https://www.moneybookers.com/app/payment.pl';
			$tracking = '<input type="hidden" name="merchant_fields" value="os0" />';
		}
		$tracking .= '<input type="hidden" name="os0" value="' . $contribution_tracking_id . '" />';

		// Output the repost form
		$output = '<form method="post" name="contributiontracking" action="' . $action . '">';

		foreach ( $values as $key => $value ) {
			$output .= '<input type="hidden" name="' . htmlspecialchars( $key ) . '" value="' . htmlspecialchars( $value ) . '" />';
		}
		$output .= $tracking;

		// Offer a button to post the form if the user has Javascript support
		$output .= '<noscript>';
		$output .= '<input type="submit" value="Continue" />';
		$output .= '</noscript>';

		$output .= '</form>';

		// Automatically post the form if the user has Javascript support
		$output .= '<script type="text/javascript">document.contributiontracking.submit();</script>';

		$wgOut->addHTML( $output );
	}
}
