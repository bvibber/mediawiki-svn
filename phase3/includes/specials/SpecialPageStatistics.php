<?php
if (!defined('MEDIAWIKI'))
	die();

class SpecialPageStatistics extends SpecialPage {
	function __construct() {
		parent::__construct( 'PageStatistics' );
	}
	
	function execute( $subpage ) {
		global $wgOut;
		
		$wgOut->setPageTitle( wfMsg( 'pagestatistics' ) );
		$wgOut->setRobotPolicy( "noindex,nofollow" );
		$wgOut->setArticleRelated( false );
		$wgOut->enableClientCache( false );
		
		$this->setHeaders();
		$this->loadParameters( $subpage );
	
		if ($this->page) {
			$this->showStatistics( );
		} else {
			$this->showMain();
		}
		
		
	}
	
	function loadParameters( $subpage ) {
		global $wgRequest;
		
		$this->page = $subpage;
		$this->periodStart = $wgRequest->getVal( 'periodstart' );
		$this->periodEnd = $wgRequest->getVal( 'periodend' );
		
		if ($p = $wgRequest->getVal( 'target' ) )
			$this->page = $p;
	}
	
	function showSearchBox(  ) {
		global $wgOut;
		
		$fields = array();
		$fields['pagestatistics-page'] = Xml::input( 'target', 45, $this->page );
		$fields['pagestatistics-periodstart'] = Xml::input( 'periodstart', 45, $this->periodStart );
		$fields['pagestatistics-periodend'] = Xml::input( 'periodend', 45, $this->periodEnd );
		
		$form = Xml::buildForm( $fields, 'pagestatistics-search' );
		$form .= Xml::hidden( 'title', $this->getTitle()->getPrefixedText() );
		$form = Xml::tags( 'form', array( 'method' => 'GET', 'action' => $this->getTitle()->getFullURL() ), $form );
		$form = Xml::fieldset( wfMsgExt( 'pagestatistics-search-legend', 'parseinline' ), $form );
		
		$wgOut->addHTML( $form );
	}
	
	function showMain() {
		global $wgUser, $wgOut;
		
		$sk = $wgUser->getSkin();
		
		## Create initial intro
		$wgOut->addWikiMsg( 'pagestatistics-intro' );
		
		## Fieldset with search stuff
		$this->showSearchBox( );
	}
	
	function showStatistics() {
		global $wgLang, $wgOut;
		
		$this->showSearchBox();
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$articles = array();
		
		foreach( explode('|', $this->page) as $pageName ) {
			$a = new Article( Title::newFromText( $pageName ) );
			$articles[$pageName] = $a->getID();
		}
		
		$periodStart = $dbr->addQuotes( $dbr->timestamp( strtotime( $this->periodStart ) ) );
		$periodEnd = $dbr->addQuotes( $dbr->timestamp( strtotime( $this->periodEnd ) ) );
		
		$res = $dbr->select( 'hit_statistics', '*', array( "hs_period_start>=$periodStart", "hs_period_end<=$periodEnd", 'hs_page' => $articles ), __METHOD__ );
		
		$articles = array_flip( $articles );

		$html = Xml::tags( 'th', null, wfMsgExt( 'pagestatistics-datatable-page', 'parseinline' ) );
		$html .= Xml::tags( 'th', null, wfMsgExt( 'pagestatistics-datatable-periodstart', 'parseinline' ) );
		$html .= Xml::tags( 'th', null, wfMsgExt( 'pagestatistics-datatable-periodend', 'parseinline' ) );
		$html .= Xml::tags( 'th', null, wfMsgExt( 'pagestatistics-datatable-count', 'parseinline' ) );
		$html = Xml::tags( 'tr', null, $html );
		
		$total = 0;
		$data = array();
		while( $row = $dbr->fetchObject( $res ) ) {
			$thisData = array(
				'count' => $row->hs_count,
				'start' => wfTimestamp( TS_UNIX, $row->hs_period_start),
				'end' => wfTimestamp( TS_UNIX, $row->hs_period_end),
				'article' => $articles[$row->hs_page],
			);
			$data[] = $thisData;
			
			$total += $row->hs_count;
			
			$thisRow = Xml::tags( 'td', null, $articles[$row->hs_page] );
			$thisRow .= Xml::tags( 'td', null, $wgLang->timeanddate( $row->hs_period_start ) );
			$thisRow .= Xml::tags('td', null, $wgLang->timeanddate( $row->hs_period_end ) );
			$thisRow .= Xml::tags('td', null, $row->hs_count );
			$thisRow = Xml::tags( 'tr', null, $thisRow );
			
			$html .= "$thisRow\n";
		}
		
		## Rollup total row
		$totalLabel = Xml::tags( 'strong', null, wfMsgExt( 'pagestatistics-datatable-total', 'parseinline' ) );
		$thisRow = Xml::tags( 'td', null, $totalLabel );
		$thisRow .= Xml::tags('td', null, '' );
		$thisRow .= Xml::tags('td', null, '' );
		$thisRow .= Xml::tags('td', null, $total );
		$thisRow = Xml::tags( 'tr', null, $thisRow );
		
		$html .= "$thisRow\n";
		
		$html = Xml::tags( 'table', null, Xml::tags( 'tbody', null, $html ) );
		
		## Graph!
		$reducedData = $this->reduceData( $data, $articles );
		
		$this->showGraph( $reducedData );
		
		## Data table AFTER graph
		$wgOut->addHTML( $html );
	}
	
	function getTimeFormat( $period ) {
		if ($period <= 86400) {
			// Less than one day, give times in hours and minutes.
			return "H:i";
		} elseif ($period <= 86400 * 7) {
			// Less than one week. Give times in day of week, then hour.
			return "D H:00";
		} elseif ($period <= 86400 * 365) {
			// Less than one year. Give times with month/day
			return "M j";
		} else {
			// More than one year. Give times with Year/month
			return "Y M";
		}
	}
	
	function showGraph( $data ) {
		global $wgOut;
		
		## Some random colours, trying to make them as dissimilar as possible.
		$colours = array( 'FF0000', '00FF00', '0000FF', '00FFFF', 'FF00FF', '0000FF', 'FF8844', 'FF4488', '88FF44', '44FF88', '4488FF', '8844FF' );
		
		foreach( $colours as $colour ) {
		
		}
		
		$periodStart = strtotime($this->periodStart);
		$periodEnd = strtotime($this->periodEnd);
		$period = $periodEnd - $periodStart;
		
		$graphMax = max( array_map( 'max', $data ) );
		
		## Data has already been reduced...
		$parameters = array();
		$parameters['chd'] = 's:'.implode( ',', array_map( array($this, 'encodeForGoogle'), $data, array_fill( 0, count($data), $graphMax ) ) );
		$parameters['chtt'] = wfMsg( 'pagestatistics-chart-title' );
		$parameters['chs'] = '600x500';
		$parameters['cht'] = 'lc';
		$parameters['chxt'] = 'y,x';
		$parameters['chxr'] = '0,0,'.$graphMax;
		
		## Hit axis labels.
		### Round max to 2 sig figs.
		$powTen = pow(10, intval( log( $graphMax, 10 ) ) );
		$graphMax = $powTen * round( $graphMax / $powTen, 1 );
		$hitLabels = array();
		for( $i=0;$i<=(10*$graphMax/$powTen);++$i ) {
			$hitLabels[] = $i * $powTen / 10;
		}
		$hitLabels = implode( '|', $hitLabels );
		
		## Time axis labels
		$timeLabels = array();
		$interval_step = $period/5; // We'll mark the time at 5 places.
		$time_format = $this->getTimeFormat( $period );
		
		for( $i=0;$i<=5;$i++ ) {
			$time = $periodStart + ($i * $interval_step );
			$timeLabels[] = date( $time_format, $time );
		}
		
		$timeLabels = implode( '|', $timeLabels );
		
		$parameters['chxl'] = "1:|$timeLabels";
		
		## Legend, colours.
		$parameters['chdl'] = implode( '|', array_keys($data) );
		$parameters['chco'] = implode( ',', array_splice( $colours, 0, count($data) ) );
		
		## Put it all together...
		$queryString = '';
		$queryStringComponents = array();
		foreach( $parameters as $key => $value ) {
			$queryStringComponents[] = urlencode($key) . '=' . urlencode($value);
		}
		$queryString = implode( '&', $queryStringComponents );
		
		$chartURL = 'http://chart.apis.google.com/chart?'.$queryString;
		
		$wgOut->addHTML( Xml::element( 'img', array( 'src' => $chartURL ) ) );
	}
	
	function encodeForGoogle($data, $max) {
		$googleEncoding = preg_split( '//', 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', -1, PREG_SPLIT_NO_EMPTY );
		
		if ($max != 0) {
			$scaleValue = (count($googleEncoding)-1) / $max;
		} else {
			$scaleValue = 0;
		}
		
		$return = '';
		
		foreach( $data as $value ) {
			if ($value === false) {
				$return .= '_';
			} else {
				$scaled = round($value * $scaleValue);
				$return .= $googleEncoding[$scaled];
			}
		}
		
		return $return;
	}
	
	## TODO error handling...
	function reduceData( $data, $articles ) {
		## The basic strategy is to determine our data points, and then fill them with the average of neighbouring data.
		$graphWidth = 600;
		
		$periodStart = strtotime( $this->periodStart );
		$periodEnd = strtotime( $this->periodEnd );
		$periodLength = $periodEnd - $periodStart;
		$pointCount = ($graphWidth / 20);
		
		$articlePointCount = array_fill_keys($articles, 0);
		foreach( $data as $row ) {
			$articlePointCount[$row['article']]++;
		}
		
		## Don't create more points than we can conceivably fill.
		if ($pointCount > $npc = max($articlePointCount)) {
			$pointCount = $npc;
		}
		
		$sampleSize = $periodLength / $pointCount;
		
		$reducedData = array(); ## 3d array, array( article => array( pointID => array( 'count' => count, 'average' => average ) ) )
		foreach( $articles as $article ) {
			$reducedData[$article] = array();
			for($i=0;$i<=$pointCount;++$i) {
				$reducedData[$article][$i] = array( 'count' => 0, 'average' => 0 );
			}
		}
		
		## Loop through all data, and find the best data points to place the data into.
		foreach( $data as $row ) {
			## For now, place the data at the point closest to the center of the period. Not entirely accurate, but not awful.
			$article = $row['article'];
			$timestamp = ($row['start'] + $row['end'])/2;
			$pointID = round(($timestamp - $periodStart)/$sampleSize);
			$rate = 86400 * $row['count'] / ($row['end'] - $row['start']); ## Hits per day
			
			if ($pointID < 0) {
				$pointID = 0;
				global $wgOut;
				$offset = $periodStart - $timestamp;
// 				$wgOut->addWikitext( "* Out-of-range data point (offset $offset seconds, timestamp $timestamp compared to period $periodStart&ndash;$periodEnd): \n<pre>".wfEscapeWikitext( print_r( $row, true ) )."</pre>" );
			} elseif ($pointID > $pointCount) {
				$pointID = $pointCount;
			}
			
			## Update
			$temp = $reducedData[$article][$pointID]['average'] * $reducedData[$article][$pointID]['count'];
			$temp = $temp + $rate;
			$reducedData[$article][$pointID]['count']++;
			$reducedData[$article][$pointID]['average'] = ($temp / $reducedData[$article][$pointID]['count']);
		}
		
		## Now, just pull out the averages.
		$return = array();
		foreach( $reducedData as $article => $stuff ) {
			for( $i=0; $i<$pointCount; ++$i ) {
				if ($stuff[$i]['count']) {
					$return[$article][$i] = $stuff[$i]['average'];
				} else {
					## Check adjacent points, interpolate
					$result = 0;
					$count = 0;
					if (!empty($stuff[$i-1]) && $stuff[$i-1]['count']) {
						$result += $stuff[$i-1]['average'] * $stuff[$i-1]['count'];
						$count += $stuff[$i-1]['count'];
					}
					if (!empty($stuff[$i+1]) && $stuff[$i+1]['count']) {
						$result += $stuff[$i+1]['average'] * $stuff[$i+1]['count'];
						$count += $stuff[$i+1]['count'];
					}
					
					if ($count) {
						$return[$article][$i] = $result / $count;
					} else {
						$return[$article][$i] = false;
					}
					
				}
			}
		}
		
		return $return;
	}
}