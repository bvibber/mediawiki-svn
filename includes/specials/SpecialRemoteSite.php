<?php

/**
 * constructor
 */
function wfSpecialRemoteSite( $par ) {
	$rsite = new SpecialRemoteSite;
	$rsite->execute( $par );
}

/**
 * @ingroup SpecialPage
 */
class SpecialRemoteSite {

	/**
	 * main()
	 */
	function execute( $par ) {
		global $wgOut, $wgRequest;

		$wgOut->addHTML( '<div dir="ltr">' );
		
		if(!$par){
			$par = 'wikipedia';
		}
		$start['fetch'] = microtime(true);
		$rsite = RemoteSite::getForInterwiki( $par );
		$end['fetch'] = microtime(true);
		
		if(!$rsite){
			$wgOut->addHTML( '<h3>Error getting RemoteSite</h3>' );
		}else{
			$cache = $wgRequest->getVal('caching', 'no!');
			if($cache != 'no!'){
				$rsite->mCaching = $cache;
			}
			
			$start['siteinfo'] = microtime(true);
			$rsite->fetchSiteinfo();
			$end['siteinfo'] = microtime(true);
			
			$wgOut->addHTML( '<h3>' . get_class($rsite) . ' testing for ' . (($rsite->mSitename) ? $rsite->mSitename : $rsite->mShortname) . '</h3>' );
			
			$start['mpExists'] = microtime(true);
			$mpExists = ( $rsite->checkPageExistance( 'Main Page' ) ? 'true' : 'false' );
			$end['mpExists'] = microtime(true);
			$start['houseExists'] = microtime(true);
			$houseExists = ( $rsite->checkPageExistance( 'House' ) ? 'true' : 'false' );
			$end['houseExists'] = microtime(true);
			$start['randExists'] = microtime(true);
			$randExists = ( $rsite->checkPageExistance( 'adsfadsf1das23' ) ? 'true' : 'false' );
			$end['randExists'] = microtime(true);
			
			$wgOut->addHTML( 'Page existance of "Main Page": ' . $mpExists . '<br/><br/>' );
			$wgOut->addHTML( 'Page existance of "House": ' . $houseExists . '<br/><br/>' );
			$wgOut->addHTML( 'Page existance of "adsfadsf1das23": ' . $randExists . '<br/><br/>' );
			$wgOut->addHTML( 'Use subpages to check other wikis (they must be set up properly in the DB). Add ?caching=0 to disable memcaching<br/><br/>' );
			
			$name = array('fetch'=>'Creation of RemoteSite object', 'siteinfo'=>'Fetching Siteinfo', 'mpExists'=>'Checking "Main Page"', 'houseExists'=>'Checking "House"', 'randExists'=>'Checking existance of "adsfadsf1das23"');
			$code = "<small>Caching status: " . ($rsite->mCaching ? 'Enabled' : 'Disabled' ) . "</small>
			<table class='mw-statistics-table'>
			<th><td>Time</td></th>
			";
			foreach($start as $key=>$value){
				$time[$key] = $end[$key]-$value;
				$code .= "<tr><td>". $name[$key] . "</td><td>". $time[$key] . "</td></tr>";
			}
			$code .= "</table>";
			
			$wgOut->addHTML( $code );
		}
		$wgOut->addHTML( '</div>' );
	}

}

/**#@-*/
