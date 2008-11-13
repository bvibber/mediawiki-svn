<?php
/**
 * Special Page for Contribution statistics extension
 *
 * @file
 * @ingroup Extensions
 */

// Special page ContributionStatistics
class SpecialFundraiserStatistics extends SpecialPage {

	/* Functions */

	public function __construct() {
		// Initialize special page
		parent::__construct( 'FundraiserStatistics' );
		
		// Internationalization
		wfLoadExtensionMessages( 'ContributionReporting' );
	}
	
	public function execute( $sub ) {
		global $wgRequest, $wgOut, $wgUser;
		global $egFundraiserStatisticsFundraisers;
		
		// Begin output
		$this->setHeaders();
		
		$htmlOut = Xml::openElement( 'div', array( 'style' => 'margin-bottom: 10px;' ) );
		$today = strtotime( date( 'M j Y' ) );
		
		$columns = array();
		foreach ( $egFundraiserStatisticsFundraisers as $fundraiser ) {
			$htmlOut .= Xml::element( 'span',
				array(
					'style' => "background-color:{$fundraiser['color']};" .
							   "border:outset 1px {$fundraiser['color']};" .
							   "padding:5px;"
				),
				$fundraiser['title']
			);
			
			// Get data for fundraiser
			$days = $this->getDailyTotals( $fundraiser['start'], $fundraiser['end'] );
			
			// Determine maximimum for fundraiser
			$max = 0;
			foreach ( $days as $day ) {
				if ( $day[0] > $max ) {
					$max = $day[0];
				}
			}
			
			$todayStyle = 'position:absolute;' .
						  'width:6px;' .
						  'height:6px;' .
						  'background-color:black;' .
						  'margin-top:3px;' .
						  'margin-left:-1px';
			
			// Build columns
			$column = 0;
			foreach( $days as $day ) {
				$height = ( 200 / $max ) * $day[0];
				if ( !isset( $columns[$column] ) ) {
					$columns[$column] = '';
				}
				$style = "height:{$height}px;" .
						 "width:4px;" .
						 "background-color:{$fundraiser['color']};" .
						 "border:outset 1px {$fundraiser['color']};";
				$extra = '';
				if( strtotime( $day[1] ) == $today ) {
					$extra = Xml::element( 'div',
						array(
							'style' => $todayStyle
						)
					);
				}
				$columns[$column] .= Xml::tags( 'td', array( 'valign' => 'bottom' ),
					Xml::element( 'div', array( 'style' => $style ), '', false ) . $extra
				);
				$column++;
			}
		}
		
		$htmlOut .= Xml::closeElement( 'div' );
		
		// Show bar graph
		$htmlOut .= Xml::openElement( 'table',
			array(
				'cellpadding' => 0,
				'cellspacing' => 0,
				'border' => 0
			)
		);
		$htmlOut .= Xml::openElement( 'tr' );
		foreach( $columns as $column ) {
			$htmlOut .= $column;
			$htmlOut .= Xml::tags( 'td', array( 'valign' => 'bottom' ),
				Xml::element( 'div', array( 'style' => "width:4px;" ), '', false )
			);
		}
		$htmlOut .= Xml::closeElement( 'tr' );
		$htmlOut .= Xml::closeElement( 'table' );
		
		$wgOut->addHTML( $htmlOut );
	}
	
	/* Query Functions */
	
	public function getDailyTotals( $start, $end ) {
		global $egFundraiserStatisticsMinimum;
		global $egFundraiserStatisticsMaximum;
		
		// Get connection
		$dbr = efContributionReportingConnection();
		
		// Select sums and dates of contributions grouped by day
		$res = $dbr->select( 'public_reporting',
			array(
				'sum(converted_amount)',
				"FROM_UNIXTIME(received, '%Y-%m-%d')"
			),
			array_merge(
				array(
				'converted_amount >= ' . $egFundraiserStatisticsMinimum,
				'converted_amount <= ' . $egFundraiserStatisticsMaximum
				),
				$this->dateConds( $dbr, $start, $end )
			),
			__METHOD__,
			array(
				'ORDER BY' => 'received',
				'GROUP BY' => "FROM_UNIXTIME(received, '%Y-%m-%d')"
			)
		);
		
		// Build day/value array
		$totals = array();
		while ( $row = $dbr->fetchRow( $res ) ) {
			$totals[] = $row;
		}
		
		// Return results
		return $totals;
	}
	
	protected function dateConds( $dbr, $start, $end ) {
		return
			array(
				'received >= ' . $dbr->addQuotes( wfTimestamp( TS_UNIX, strtotime( $start ) ) ),
				'received <= ' . $dbr->addQuotes( wfTimestamp( TS_UNIX, strtotime( $end ) + 24 * 60 * 60 ) )
			);
	}
}
