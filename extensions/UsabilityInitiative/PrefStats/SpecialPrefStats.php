<?php
/**
 * Special:PrefStats
 *
 * @file
 * @ingroup Extensions
 */

class SpecialPrefStats extends SpecialPage {
	function __construct() {
		parent::__construct( 'PrefStats', 'prefstats' );
		wfLoadExtensionMessages( 'PrefStats' );
	}
	
	function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser, $wgPrefStatsTrackPrefs;
		$this->setHeaders();
		
		// Check permissions
		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return;
		}
		
		$wgOut->setPageTitle( wfMsg( 'prefstats-title' ) );
		
		if( !isset( $wgPrefStatsTrackPrefs[$par] ) ) {
			$this->displayTrackedPrefs();
			return;
		}
		
		$this->displayPrefStats( $par );
	}
	
	function displayTrackedPrefs() {
		global $wgOut, $wgUser, $wgPrefStatsTrackPrefs;
		$wgOut->addWikiMsg( 'prefstats-list-intro' );
		$wgOut->addHTML( Xml::openElement( 'ul' ) );
		foreach ( $wgPrefStatsTrackPrefs as $pref => $value ) {
			$wgOut->addHTML( Xml::tags( 'li', array(),
				$wgUser->getSkin()->link(
					$this->getTitle( $pref ),
					htmlspecialchars( wfMsg( 'prefstats-list-elem', $pref,
						$value ) ) ) ) );
		}
		$wgOut->addHTML( Xml::closeElement( 'ul' ) );
	}
	
	function displayPrefStats( $pref ) {
		global $wgOut, $wgRequest, $wgPrefStatsTrackPrefs;
		$max = $this->getMaxDuration( $pref );
		$stats = $this->getPrefStats( $pref,
			$wgRequest->getIntOrNull( 'inc' ) );
		$wgOut->addHTML( Xml::element( 'img', array( 'src' =>
			$this->getGoogleChartParams( $stats ) ) ) );
	}
	
	function getGoogleChartParams( $stats ) {
		global $wgPrefStatsChartDimensions;
		return "http://chart.apis.google.com/chart?" . wfArrayToCGI( 
		array(
			'chs' => $wgPrefStatsChartDimensions,
			'cht' => 'bvs',
			'chds' => '0,' . max( $stats ),
			'chd' => 't:' . implode( ',', $stats ),
			'chxt' => 'x,y',
			'chxr' => '1,' . min( $stats ) . ',' . max( $stats ),
			'chxl' => '0:|'. implode( '|', array_keys( $stats ) ),
			'chm' => 'N*f0zy*,000000,0,-1,11' 
		) );
	}
	
	function getPrefStats( $pref, $inc = null ) {
		global $wgPrefStatsTimeUnit;
		$max = ceil( $this->getMaxDuration( $pref ) /
			$wgPrefStatsTimeUnit );
		$inc = max( 1, ( is_null( $inc ) ? ceil( $max / 10 ) : $inc ) );
		$retval = array();
		for( $i = 0; $i <= $max; $i += $inc ) {
			$end = min( $max, $i + $inc );
			$key = $i . '-' . $end;
			$retval[$key] = $this->countBetween( $pref,
				$i * $wgPrefStatsTimeUnit,
				$end * $wgPrefStatsTimeUnit );
		}
		return $retval;
	}
	
	/**
	 * Get the highest duration in the database
	 */
	function getMaxDuration( $pref ) {
		$dbr = wfGetDb( DB_SLAVE );
		$max1 = $dbr->selectField( 'prefstats', 'MAX(ps_duration)',
			array( 'ps_pref' => $pref ), __METHOD__ );
		$minTS = $dbr->selectField( 'prefstats', 'MIN(ps_start)',
			array(	'ps_pref' => $pref,
				'ps_duration IS NULL' ), __METHOD__ );
		$max2 = wfTimestamp( TS_UNIX ) - wfTimestamp( TS_UNIX, $minTS );
		return max( $max1, $max2 );
	}
	
	/**
	 * Count the number of users having $pref enabled between
	 * $min and $max seconds
	 */
	function countBetween( $pref, $min, $max ) {
		$dbr = wfGetDb( DB_SLAVE );
		$count1 = $dbr->selectField( 'prefstats', 'COUNT(*)', array(
				'ps_pref' => $pref,
				'ps_duration < ' . intval( $max ),
				'ps_duration >= ' . intval( $min )
			), __METHOD__ );
		$maxTS = wfTimestamp( TS_UNIX ) - $min;
		$minTS = wfTimestamp( TS_UNIX ) - $max;
		$count2 = $dbr->selectField( 'prefstats', 'COUNT(*)', array(
				'ps_duration IS NULL',
				'ps_start <' . $dbr->timestamp( $maxTS ),
				'ps_start >=' . $dbr->timestamp( $minTS )
			), __METHOD__ );
		return $count1 + $count2;
	}
}
