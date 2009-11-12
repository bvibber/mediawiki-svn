<?php
/**
 * Special Page for Contribution tracking statistics extension
 *
 * @file
 * @ingroup Extensions
 */

// Special page ContributionTrackingStatistics
class SpecialContributionTrackingStatistics extends SpecialPage {

	public static $number_of_days_to_show = 7;
	
	/* Functions */

	public function __construct() {
		// Initialize special page
		parent::__construct( 'ContributionTrackingStatistics' );
		
		// Internationalization
		wfLoadExtensionMessages( 'ContributionReporting' );
	}
	
	public function execute( $sub ) {
		// Begin output
		$this->setHeaders();
		
		// Show day totals
		$this->showDayTotals();
		
		$this->showDayTotalsForLastDays(SpecialContributionTrackingStatistics::$number_of_days_to_show);
		
		// Show weekly total
		//$this->showWeeklyTotals();
	}
	
	/* Wrapper */
	public function showDayTotalsForLastDays( $num_days ){
                //Seriously, PHP 5.3 has cleaner ways of doing this, till then strtotime to the rescue!
                $end_day = new DateTime( "now" );
                ++$num_days; //really you probably don't want today
                $end_day->modify("-$num_days days");

                for( $i = 0 ; $i < ($num_days - 2) ; $i++){ //you don't want today
                        $this->showDayTotals(false, $end_day->format("YmdHis")); //MW Format
                        $end_day->modify("+1 day");
                }
        }
	
	
	
		
	/* Display Functions */
	
	// Html out for the days total
	public function showDayTotals( $is_now = true, $timestamp = 0 ) {
		global $wgOut,$wgLang;
		global $wgAllowedTemplates;
		
		$totals = $this->getDayTotals($is_now, $timestamp);
		
		$msg = wfMsg( 'contribstats-day-totals' ) . " - " . date( 'o-m-d', wfTimestamp( TS_UNIX, $is_now?time():$timestamp ) );
		$htmlOut = Xml::element( 'h3', null, $msg );

		// Day
		$htmlOut .= Xml::openElement( 'table',
				array(
					'class' => 'sortable',
					'border' => 0,
					'cellpadding' => 5, 
					'width' => '100%'
				)
		);

		if ( isset ( $totals ) ) {
			// Table headers
			$htmlOut .= Xml::element( 'th', array( 'align' => 'left' ), wfMsg( 'contribstats-template' ) ) ;
			$htmlOut .= Xml::element( 'th', array( 'align' => 'left' ), wfMsg( 'contribstats-landingpage' ) ) ;
			$htmlOut .= Xml::element( 'th', array( 'align' => 'left' ), wfMsg( 'contribstats-donatepage' ) ) ;
			$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-clicks' ) );
			$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-donations' ) );
			$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-amount' ) );
			$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-conversion' ) );

			foreach( $totals as $template ) {
				if ( ! in_array($template[0], $wgAllowedTemplates ) )
					continue;
				// Pull together templates, clicks, donations, conversion rate
				$conversion_rate = ( $template[1] == 0 ) ? 0 : ( $template[2] / $template[1] ) * 100; 
				$amount = ( $template[3] == 0 ) ? 0 : $template[3];

				$expanded_template = explode(".", $template[0]);
				if(!isset($expanded_template[1])){ $expanded_template[1] = "";}
				if(!isset($expanded_template[2])){ $expanded_template[2] = "";}
				
				
				
				$htmlOut .= Xml::tags( 'tr', null,
						Xml::element( 'td', array( 'align' => 'left'), $expanded_template[0] ) .
						Xml::element( 'td', array( 'align' => 'left'), $expanded_template[1] ) .
						Xml::element( 'td', array( 'align' => 'left'), $expanded_template[2] ) .
						Xml::element( 'td', array( 'align' => 'right'), $template[1] ) .
						Xml::element( 'td', array( 'align' => 'right'), $template[2] ) .
						Xml::element( 'td', array( 'align' => 'right'), $amount ) .
						Xml::element( 'td', array( 'align' => 'right'), $wgLang->formatNum( number_format( $conversion_rate, 2 ) ) ) 
				);

			}

			$htmlOut .= Xml::closeElement( 'table' );
		} else {
			$htmlOut .= wfMsg( 'contribstats-nodata' );
		}

		// Output HTML
		$wgOut->addHTML( $htmlOut );
		}

	// Html out for the weekly totals
	public function showWeeklyTotals() {
		global $wgOut,$wgLang;
		global $wgContributionTrackingStatisticsViewWeeks;

		$msg = wfMsgExt( 'contribstats-weekly-totals' , array ( 'parsemag' ),
			 $wgLang->formatNum( $wgContributionTrackingStatisticsViewWeeks ) );
		$htmlOut = Xml::element( 'h3', null, $msg );
		$wgOut->addHTML( $htmlOut );

		$range = $this->weekRange( wfTimestampNow( TS_UNIX ) ) ;
		$ts = strtotime( $range[0] );
		while ( $wgContributionTrackingStatisticsViewWeeks > 0 ) {
			$this->showWeekTotal( date('Ymd000000', $ts ) ) ;
			$ts -= 60 * 60 * 24 * 7;
			$wgContributionTrackingStatisticsViewWeeks--;
		}
	}

	// Html out for a single week
	public function showWeekTotal( $week ) {
		global $wgOut,$wgLang;
		global $wgAllowedTemplates;

		$totals = $this->getWeekTotals( $week );
		
		// Weeks
		if ( isset ( $totals ) ) {
			$htmlOut = '';

			$htmlOut .= Xml::element( 'h2', null, date( 'o-m-d', wfTimeStamp( TS_UNIX, $week ) ) ); 		
			$htmlOut .= Xml::openElement( 'table',
					array(
						'class' => 'sortable',
						'border' => 0,
						'cellpadding' => 5, 
						'width' => '100%'
					)
			);

			// Table headers
			$htmlOut .= Xml::element( 'th', array( 'align' => 'left' ), wfMsg( 'contribstats-template' ) ) ;
			$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-clicks' ) );
			$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-donations' ) );
			$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-amount' ) );
			$htmlOut .= Xml::element( 'th', array( 'align' => 'right' ), wfMsg( 'contribstats-conversion' ) );

			foreach( $totals as $template ) {
				if ( ! in_array($template[0], $wgAllowedTemplates ) )
					continue;
				// Pull together templates, clicks, donations, conversion rate
				$conversion_rate = ( $template[1] == 0 ) ? 0 : $template[2] / $template[1] * 100; 
				$amount = ( $template[3] == 0 ) ? 0 : $template[3];
				
				$htmlOut .= Xml::tags( 'tr', null,
						Xml::element( 'td', array( 'align' => 'left'), $template[0] ) .
						Xml::element( 'td', array( 'align' => 'right'), $template[1] ) .
						Xml::element( 'td', array( 'align' => 'right'), $template[2] ) .
						Xml::element( 'td', array( 'align' => 'right'), $amount ) .
						Xml::element( 'td', array( 'align' => 'right'), $wgLang->formatNum( number_format( $conversion_rate, 2 ) ) ) 
				);
			}

			$htmlOut .= Xml::closeElement( 'table' );
		} else {
			$htmlOut .= wfMsg( 'contribstats-nodata' );
		}
			
		// Output HTML
		$wgOut->addHTML( $htmlOut );
		
	}
	
	/* Query Functions */

	// Totals for today
	public function getDayTotals($is_now = true, $timestamp = 0) {
		$range = array();
		$end_format = 'Ymd235959';
		if($is_now){
			$timestamp = time();
			$end_format = 'YmdHis';	
		}
		
		$range[0] = date( 'Ymd000000' , wfTimestamp(TS_UNIX, $timestamp) );
		$range[1] = date( $end_format , wfTimestamp(TS_UNIX, $timestamp) );
		
		return $this->getTotalsInRange($range);
	}
	
	// Database lookup for week totals
	public function getWeekTotals( $week ) {
		$range = $this->weekRange( $week );
		return $this->getTotalsInRange($range);
	}
	
	//generalized lookup
	public function getTotalsInRange($range){
		$dbr = efContributionTrackingConnection();

		$conds[] = "ts >=" . $dbr->addQuotes( $range[0] );
		$conds[] = "ts <=" . $dbr->addQuotes( $range[1] );
		  
		$res = $dbr->select( 
			array( 'contribution_tracking',
			       'civicrm.public_reporting',
			),
			array(
				'utm_source',
				'sum(isnull(contribution_tracking.contribution_id)) as miss',
				'sum(not isnull(contribution_tracking.contribution_id)) as hit',
				'sum(converted_amount) as converted_amount',
			),
			$conds,
			__METHOD__,
			array(
				'ORDER BY' => 'hit desc',
				'GROUP BY' => 'utm_source'
			),
			array( 'civicrm.public_reporting' =>
				array(
					'LEFT JOIN',
				 	'contribution_tracking.contribution_id = civicrm.public_reporting.contribution_id',
				) 
			)

		);
		
		while ( $row = $dbr->fetchRow( $res ) ) {
			$result[] = array(
					$row[0],
					$row[1],
					$row[2],
					$row[3],
			);
		}

		return $result;
	}

        // Given a day figure out what its week bounds are
	public function weekRange( $day ) {
		$day = wfTimestamp( TS_UNIX, $day );
		$start = ( date( 'w', $day ) == 0) ? $day : strtotime('last sunday', $day ); // Use current Sunday
		return array( 
			date( 'Ymd000000', $start ),
                	date( 'Ymd235959', strtotime( 'next sunday', $start) )
		);
	}
}
