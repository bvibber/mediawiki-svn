<?php
/**
 * Special Page for Contribution tracking statistics extension
 *
 * @file
 * @ingroup Extensions
 */

// Special page ContributionTrackingStatistics
class SpecialContributionTrackingStatistics extends SpecialPage {

	/* Members */
	
	/* Functions */

	public function __construct() {
		// Initialize special page
		parent::__construct( 'ContributionTrackingStatistics' );
		
		// Internationalization
		wfLoadExtensionMessages( 'ContributionReporting' );
	}
	
	public function execute( $sub ) {
		global $wgRequest, $wgOut, $wgUser;
		global $egContributionTrackingStatisticsViewDays;
		
		// Begin output
		$this->setHeaders();
		
		// Show day totals
		$this->showDayTotals();
		
		// Show weekly total
		$this->showWeeklyTotals();
	}
	
	/* Display Functions */
	
	public function showDayTotals() {
		global $wgOut, $wgLang;
		global $wgAllowedTemplates;

		$totals = $this->getDayTotals();

		$msg = wfMsgExt( 'contribstats-day-totals' , array ( 'parsemag' ), $wgLang->formatNum( count ( $months ) ) ); 
		$htmlOut = Xml::element( 'h3', null, $msg );

		// Day
		$htmlOut .= Xml::openElement( 'table',
				array(
					'border' => 0,
					'cellpadding' => 5, 
					'width' => '100%'
				)
		);

		// Table headers
		$htmlOut .= Xml::element( 'th', array( 'align' => 'left' ), wfMsg( 'contribstats-template' ) ) ;
		$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-clicks' ) );
		$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-donations' ) );
		$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-conversion' ) );

		foreach( $totals as $template ) {
			if ( ! in_array($template[0], $wgAllowedTemplates ) )
				continue;
			// Pull together templates, clicks, donations, conversion rate
			$htmlOut .= Xml::tags( 'tr', null,
					Xml::element( 'td', array( 'align' => 'left'), $template[0] ) .
					Xml::element( 'td', array( 'align' => 'right'), $template[1] ) .
					Xml::element( 'td', array( 'align' => 'right'), $template[2] ) .
					Xml::element( 'td', array( 'align' => 'right'), $template[1] / $template[2] ) 
			);

		}

		$htmlOut .= Xml::closeElement( 'table' );

		// Output HTML
		$wgOut->addHTML( $htmlOut );
		
		}

	public function showWeeklyTotals() {
		global $wgOut,$wgLang;
		global $wgContributionTrackingStatisticsViewWeeks;

		$msg = wfMsgExt( 'contribstats-weekly-totals' , array ( 'parsemag' ), $wgLang->formatNum( $wgContributionTrackingStatisticsViewWeeks ) );
		$htmlOut = Xml::element( 'h3', null, $msg );
		$wgOut->addHTML( $htmlOut );

		// clunky
		$wgOut->addHTML($ts);
		$ts = date('Y-m-d');
		$range = $this->weekRange( $ts ) ;
		$ts = strtotime( $range[0] );
		while ( $wgContributionTrackingStatisticsViewWeeks > 0 ) {
			$this->showWeekTotal( date('Y-m-d', $ts ) ) ;
			$ts = strtotime('last sunday', $ts  );
			$wgContributionTrackingStatisticsViewWeeks--;
		}
	}

	public function showWeekTotal( $week ) {
		global $wgOut,$wgLang;
		
		global $wgAllowedTemplates;

		$totals = $this->getWeekTotals( $week );
		
		$msg = wfMsgExt( 'contribstats-weekly-totals' , array ( 'parsemag' ), $wgLang->formatNum( count ( $months ) ) ); 
		$htmlOut = Xml::element( 'h3', null, $msg );
		
		// Weeks
		$htmlOut = '';

		$msg = $week;
		$htmlOut .= Xml::element( 'h2', null, $msg ); 		
		$htmlOut .= Xml::openElement( 'table',
				array(
					'border' => 0,
					'cellpadding' => 5, 
					'width' => '100%'
				)
		);

		// Table headers
		$htmlOut .= Xml::element( 'th', array( 'align' => 'left' ), wfMsg( 'contribstats-template' ) ) ;
		$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-clicks' ) );
		$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-donations' ) );
		$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-conversion' ) );

		foreach( $totals as $template ) {
			if ( ! in_array($template[0], $wgAllowedTemplates ) )
				continue;
			// Pull together templates, clicks, donations, conversion rate
			$conversion = ( $template[2] == 0 ) ? 0 : $template[1] / $template[2]; 
			
			$htmlOut .= Xml::tags( 'tr', null,
					Xml::element( 'td', array( 'align' => 'left'), $template[0] ) .
					Xml::element( 'td', array( 'align' => 'right'), $template[1] ) .
					Xml::element( 'td', array( 'align' => 'right'), $template[2] ) .
					Xml::element( 'td', array( 'align' => 'right'), $conversion ) 
			);
		}

		$htmlOut .= Xml::closeElement( 'table' );

		// Output HTML
		$wgOut->addHTML( $htmlOut );
		
	}
	
	/* Query Functions */

	public function getDayTotals() {
		$dbr = efContributionReportingTrackingConnection();
		
		$conds[] = "ts >=" . $dbr->addQuotes( date( 'Y-m-d' ) );
		$conds[] = "ts <=" . $dbr->addQuotes( date( 'Y-m-d H:i:s' ) ); 

		$res = $dbr->select( 'contribution_tracking',
			array(
				'utm_source',
				'count(*) as total',
			),
			$conds,
			__METHOD__,
			array(
				'ORDER BY' => 'total DESC',
				'GROUP BY' => 'utm_source'
			)
		);

		array_push( $conds, 'contribution_id is not NULL' );

		$res_null = $dbr->select( 'contribution_tracking',
			array(
				'utm_source',
				'count(*) as total',
			),
			$conds,
			__METHOD__,
			array(
				'ORDER BY' => 'total DESC',
				'GROUP BY' => 'utm_source'
			)
		); 

		while ( $row = $dbr->fetchRow( $res ) ) {
			$result[] = array(
					$row[0],
					$row[1],
			);
		}

		$i = 0; // hack!
		while ( $row = $dbr->fetchRow( $res_null ) ) {
			array_push( $result[$i],$row[1] );
			$i++; 
			
		}

		return $result;
	}
	
	
	public function getWeekTotals( $week ) {
		$dbr = efContributionReportingTrackingConnection();

		$range = $this->weekRange( $week );

		$conds[] = "ts >=" . $dbr->addQuotes( $range[0] );
		$conds[] = "ts <=" . $dbr->addQuotes( $range[1] );
		  
		$res = $dbr->select( 'contribution_tracking',
			array(
				'utm_source',
				'count(*) as total',
			),
			$conds,
			__METHOD__,
			array(
				'ORDER BY' => 'total DESC',
				'GROUP BY' => 'utm_source'
			)
		);
		
		array_push( $conds, 'contribution_id is not NULL' );

		$res_null = $dbr->select( 'contribution_tracking',
			array(
				'utm_source',
				'count(*) as total',
			),
			$conds,
			__METHOD__,
			array(
				'ORDER BY' => 'total DESC',
				'GROUP BY' => 'utm_source'
			)
		); 
		
		while ( $row = $dbr->fetchRow( $res ) ) {
			$result[] = array(
					$row[0],
					$row[1],
			);
		}

		$i = 0; // clunky!
		while ( $row = $dbr->fetchRow( $res_null ) ) {
			array_push( $result[$i],$row[1] );
			$i++; 
			
		}

		return $result;
	}

	public function weekRange( $date ) {
		$ts = strtotime($date);
		$start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
		return array(date('Y-m-d', $start),
                 date('Y-m-d', strtotime('next saturday', $start)));
	}
}
