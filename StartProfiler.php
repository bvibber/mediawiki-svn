<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
#
$rand = mt_rand(0, 0x7fffffff);
$host = @$_SERVER['HTTP_HOST'];

/*if ( ( !($rand % 50) && $host == 'en.wikipedia.org' ) || 
     ( !($rand % 50) && $host == 'commons.wikimedia.org') ||
     ( !($rand % 50) && $host == 'de.wikipedia.org') ||
     ( !($rand % 50) && $host == 'es.wikipedia.org') ||
     ( !($rand % 1)  && $host == 'test.wikipedia.org' ) || 
     (                  $host == 'zh.wikipedia.org' ) || 
     ( !($rand % 10) && $host == 'ja.wikipedia.org' )
) {*/
if ( @$_SERVER['REQUEST_URI'] == '/w/index.php?title=United_States&action=submit' ) {
	require_once( dirname(__FILE__).'/includes/ProfilerSimpleUDP.php' );
	$wgProfiler = new ProfilerSimpleUDP;
	$wgProfiler->setProfileID( 'bigpage' );
} elseif (@defined($_REQUEST['forceprofile'])) {
    require_once( dirname(__FILE__).'/includes/ProfilerSimpleText.php' );
    $wgProfiler = new ProfilerSimpleText;
    $wgProfiler->setProfileID( 'forced' );
} elseif (@defined($_REQUEST['forcetrace'])) {
    require_once( dirname(__FILE__).'/includes/ProfilerSimpleTrace.php' );
    $wgProfiler = new ProfilerSimpleTrace;
} elseif ( strpos( @$_SERVER['REQUEST_URI'], '/w/thumb.php' ) !== false ) {
  	require_once( dirname(__FILE__).'/includes/ProfilerSimpleUDP.php' );
	$wgProfiler = new ProfilerSimpleUDP;
	$wgProfiler->setProfileID( 'thumb' );
} elseif ( !( $rand % 50 ) ) {
  	require_once( dirname(__FILE__).'/includes/ProfilerSimpleUDP.php' );
	$wgProfiler = new ProfilerSimpleUDP;
	if ( $host == 'en.wikipedia.org' ) {
		$wgProfiler->setProfileID( 'enwiki' );
	} elseif ( $host == 'de.wikipedia.org' ) {
		$wgProfiler->setProfileID( 'dewiki' );
	} else {
		$wgProfiler->setProfileID( 'others' );
	}
	#$wgProfiler->setProfileID( 'all' );
	#$wgProfiler->setMinimum(5 /* seconds */);
}
elseif ( defined( 'MW_FORCE_PROFILE' ) ) {
	require_once( dirname(__FILE__).'/includes/Profiler.php' );
	$wgProfiler = new Profiler;
} else {
	require_once( dirname(__FILE__).'/includes/ProfilerStub.php' );
}



