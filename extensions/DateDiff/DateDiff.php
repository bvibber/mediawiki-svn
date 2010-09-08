<?php 
//http://www.mediawiki.org/wiki/Manual:Extensions#Writing_Extensions
//error_reporting(E_ALL);
//ini_set("display_errors", 1);


if( !defined( 'MEDIAWIKI' ) ) {
        die( 'Not an entry point.' );
}

$wgExtensionFunctions[] = "efDateDiff";
$wgHooks['LanguageGetMagic'][] = 'efDatesFunctionMagic';

 
// Extension credits that show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
        'name' => 'Datediff',
        'author' => 'David Raison',
        'url' => 'http://www.hackerspace.lu',
        'description' => 'Takes two dates and returns all intermediary days'
);

function efDateDiff(){
    global $wgParser; 
    $wgParser->setFunctionHook('dates', 'calcdates');
}

/**
 * Adds the magic words for the parser functions
 */
function efDatesFunctionMagic( &$magicWords, $langCode ) {
        $magicWords['dates'] = array( 0, 'dates' );
	return true;
}

function calcdates(&$parser){
	$params = func_get_args();
        array_shift( $params ); // We already know the $parser ...
	while(empty($params[0])) array_shift($params);	// quite common

	$dates = array();
	foreach($params as $pair)
		$dates[] = substr($pair,strpos($pair,'=')+1);	// we currently ignore the label of the date

	$time1 = strtotime($dates[0]);
	$time2 = strtotime($dates[1]);

	$a = ($time2 > $time1) ? $time2 : $time1;       // higher
	$b = ($a == $time1) ? $time2 : $time1;          // lower
	$datediff = $a - $b;

	$oneday = 86400;
	$days = array();
	for($i=0;$i <= $datediff; $i+=$oneday){
		$days[] = date('c',strtotime($dates[0])+$i);
	}
	return implode(',',$days);

}
