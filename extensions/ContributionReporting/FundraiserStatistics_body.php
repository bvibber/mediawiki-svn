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
		global $wgRequest, $wgOut, $wgUser, $wgLang;
		global $egFundraiserStatisticsFundraisers;
		
		// Begin output
		$this->setHeaders();
		
		$style = <<<END
.fundraiserstats-view-box {
	width: 100%;
	height: 200px;
	margin-top:20px;
	border: solid 1px silver;
}

END;
		$script = <<<END
var currentLayerID = 'fundraiserstats-view-box-0';
function replaceView( newLayerID ) {
	var currentLayer = document.getElementById( currentLayerID );
	currentLayer.style.display = 'none';
	var newLayer = document.getElementById( newLayerID );
	newLayer.style.display = 'block';
	currentLayerID = newLayerID;
}
END;
		
		$htmlOut = Xml::element( 'style', array( 'type' => 'text/css' ), $style );
		$htmlOut .= Xml::element( 'script', array( 'type' => 'text/javascript' ), $script );
		
		$htmlOut .= Xml::openElement( 'div', array( 'style' => 'margin-bottom: 20px;' ) );
		$today = strtotime( date( 'M j Y' ) );
		
		$max = 0;
		foreach ( $egFundraiserStatisticsFundraisers as $fundraiser ) {
			$days = $this->getDailyTotals( $fundraiser['start'], $fundraiser['end'] );
			// Determine maximimum for fundraiser
			foreach ( $days as $day ) {
				if ( $day[2] > $max ) {
					$max = $day[2];
				}
			}
		}
		
		$columns = array();
		$views = array();
		$view = 0;
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
			
			$todayStyle = 'position:absolute;' .
						  'width:6px;' .
						  'height:6px;' .
						  'background-color:black;' .
						  'margin-top:3px;' .
						  'margin-left:-1px';
			
			// Build columns
			$column = 0;
			$lastDay = false;
			foreach( $days as $day ) {
				$height = ( 200 / $max ) * $day[2];
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
					Xml::element( 'div',
						array(
							'style' => $style,
							'onMouseOver' => "replaceView( 'fundraiserstats-view-box-{$view}' )"
						),
						'',
						false
					) . $extra
				);
				
				$viewHTML = Xml::openElement( 'table',
					array(
						'cellpadding' => 5,
						'cellspacing' => 0,
						'border' => 0,
						'style' => 'margin:10px'
					)
				);
				
				$viewHTML .= Xml::tags( 'tr', null,
					Xml::element( 'td', null, wfMsg( 'fundraiserstats-date' ) ) .
					Xml::element( 'td', null, $day[0] )
				);
				$viewHTML .= Xml::tags( 'tr', null,
					Xml::element( 'td', null, wfMsg( 'fundraiserstats-contributions' ) ) .
					Xml::element( 'td', null, $wgLang->formatNum( $day[1] ) )
				);
				$viewHTML .= Xml::tags( 'tr', null,
					Xml::element( 'td', null, wfMsg( 'fundraiserstats-total' ) ) .
					Xml::element( 'td', null, $wgLang->formatNum( $day[2] ) )
				);
				$viewHTML .= Xml::tags( 'tr', null,
					Xml::element( 'td', null, wfMsg( 'fundraiserstats-avg' ) ) .
					Xml::element( 'td', null, $wgLang->formatNum( $day[3] ) )
				);
				$viewHTML .= Xml::tags( 'tr', null,
					Xml::element( 'td', null, wfMsg( 'fundraiserstats-max' ) ) .
					Xml::element( 'td', null, $wgLang->formatNum( $day[4] ) )
				);
				$viewHTML .= Xml::closeElement( 'table' );
				
				$views[$view] = Xml::tags( 'div',
					array(
						'id' => 'fundraiserstats-view-box-' . $view,
						'class' => 'fundraiserstats-view-box',
						'style' => 'display: ' . ( $view == 0 ? 'block' : 'none' )
					),
					$viewHTML
				);
				
				$column++;
				$view++;
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
		$htmlOut .= Xml::openElement( 'td' );
		$htmlOut .= Xml::openElement( 'table',
			array(
				'cellpadding' => 0,
				'cellspacing' => 0,
				'border' => 0
			)
		);
		$htmlOut .= Xml::openElement( 'tr' );
		foreach( $columns as $i => $column ) {
			$htmlOut .= $column;
			if ( $i < count( $columns ) - 1 ) {
				$htmlOut .= Xml::tags( 'td', array( 'valign' => 'bottom' ),
					Xml::element( 'div', array( 'style' => "width:4px;" ), '', false )
				);
			}
		}
		$htmlOut .= Xml::closeElement( 'tr' );
		$htmlOut .= Xml::closeElement( 'table' );
		$htmlOut .= Xml::closeElement( 'td' );
		$htmlOut .= Xml::closeElement( 'tr' );
		$htmlOut .= Xml::openElement( 'tr' );
		$htmlOut .= Xml::openElement( 'td' );
		foreach( $views as $view ) {
			$htmlOut .= $view;
		}
		$htmlOut .= Xml::closeElement( 'td' );
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
				"FROM_UNIXTIME(received, '%Y-%m-%d')",
				'count(*)',
				'sum(converted_amount)',
				'avg(converted_amount)',
				'max(converted_amount)',
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
